<?php

namespace App\Services;

use App\Contracts\Geo\RoutingProviderInterface;
use App\DTOs\Assignment\DriverCandidate;
use App\Exceptions\Assignment\AlreadyAssignedException;
use App\Exceptions\Assignment\DriverNotAvailableException;
use App\Models\NegocioAfiliado;
use App\Models\Pedido;
use App\Models\PedidoAsignacion;
use App\Models\Repartidor;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Servicio de asignación de repartidores.
 *
 * Responsabilidades:
 * - Encontrar candidatos disponibles ordenados por cercanía al negocio.
 * - Asignar un repartidor a un pedido con protección anti-doble-asignación.
 * - Calcular rutas repartidor→negocio y negocio→cliente via OSRM.
 * - Cancelar o completar asignaciones activas.
 *
 * No hace: notificaciones, facturación, ni tracking en tiempo real.
 */
class DriverAssignmentService
{
    /** Tiempo máximo de GPS obsoleto para considerar al repartidor activo (minutos) */
    private const GPS_STALE_MINUTES = 10;

    public function __construct(
        private readonly RoutingProviderInterface $routing,
    ) {}

    /**
     * Encuentra repartidores disponibles y calcula distancia a la ubicación del negocio.
     * Devuelve lista ordenada por distancia (más cercano primero).
     *
     * @return DriverCandidate[]
     */
    public function findCandidates(NegocioAfiliado $negocio): array
    {
        if (! $negocio->latitud || ! $negocio->longitud) {
            return [];
        }

        $drivers = $this->availableDriversWithGps();

        $candidates = [];
        foreach ($drivers as $driver) {
            $route = $this->routing->route(
                (float) $driver->latitud_actual,
                (float) $driver->longitud_actual,
                (float) $negocio->latitud,
                (float) $negocio->longitud,
            );

            $candidates[] = new DriverCandidate(
                driver: $driver,
                distanceToBusinessKm: $route->routeFound ? $route->distanceKilometers : PHP_INT_MAX,
                estimatedMinutesToBusiness: $route->routeFound ? $route->durationMinutes : PHP_INT_MAX,
                routeGeometry: $route->geometry,
                routeFound: $route->routeFound,
            );
        }

        usort($candidates, fn (DriverCandidate $a, DriverCandidate $b) => $a->distanceToBusinessKm <=> $b->distanceToBusinessKm);

        return $candidates;
    }

    /**
     * Asigna un repartidor a un pedido con bloqueo de concurrencia.
     *
     * @throws AlreadyAssignedException si el pedido ya tiene asignación activa
     * @throws DriverNotAvailableException si el repartidor ya no está disponible
     */
    public function assign(
        Pedido $pedido,
        Repartidor $repartidor,
        User $assignedBy,
        string $type = PedidoAsignacion::TYPE_MANUAL,
        ?string $notes = null,
    ): PedidoAsignacion {
        return DB::transaction(function () use ($pedido, $repartidor, $assignedBy, $type, $notes) {

            /* ── Bloquear ambas filas para evitar race conditions ── */
            $pedido      = Pedido::lockForUpdate()->findOrFail($pedido->id);
            $repartidorL = Repartidor::lockForUpdate()->findOrFail($repartidor->id);

            /* ── Verificar que el pedido no tenga asignación activa ── */
            if ($pedido->asignaciones()->where('status', PedidoAsignacion::STATUS_ACTIVO)->exists()) {
                throw new AlreadyAssignedException;
            }

            /* ── Verificar que el repartidor siga disponible ── */
            if ($repartidorL->estado !== Repartidor::ESTADO_DISPONIBLE) {
                throw new DriverNotAvailableException;
            }

            /* ── Calcular ruta repartidor → negocio ── */
            $negocio    = $pedido->negocioAfiliado;
            $toNegocio  = null;
            $toCliente  = null;

            if ($negocio && $negocio->latitud && $negocio->longitud && $repartidorL->latitud_actual) {
                $toNegocio = $this->routing->route(
                    (float) $repartidorL->latitud_actual,
                    (float) $repartidorL->longitud_actual,
                    (float) $negocio->latitud,
                    (float) $negocio->longitud,
                );
            }

            /* ── Calcular ruta negocio → cliente ── */
            if ($negocio && $negocio->latitud && $pedido->latitud_cliente) {
                $toCliente = $this->routing->route(
                    (float) $negocio->latitud,
                    (float) $negocio->longitud,
                    (float) $pedido->latitud_cliente,
                    (float) $pedido->longitud_cliente,
                );
            }

            /* ── Crear registro de asignación ── */
            $asignacion = PedidoAsignacion::create([
                'pedido_id'                     => $pedido->id,
                'repartidor_id'                 => $repartidorL->id,
                'assigned_by'                   => $assignedBy->id,
                'assignment_type'               => $type,
                'status'                        => PedidoAsignacion::STATUS_ACTIVO,
                'distance_to_business_km'       => $toNegocio?->routeFound ? $toNegocio->distanceKilometers : null,
                'estimated_time_to_business_min'=> $toNegocio?->routeFound ? (int) round($toNegocio->durationMinutes) : null,
                'route_to_business'             => $toNegocio?->routeFound ? $toNegocio->geometry : null,
                'distance_to_customer_km'       => $toCliente?->routeFound ? $toCliente->distanceKilometers : null,
                'estimated_time_to_customer_min'=> $toCliente?->routeFound ? (int) round($toCliente->durationMinutes) : null,
                'route_to_customer'             => $toCliente?->routeFound ? $toCliente->geometry : null,
                'notes'                         => $notes,
                'assigned_at'                   => now(),
            ]);

            /* ── Actualizar pedido ── */
            $pedido->update([
                'repartidor_id' => $repartidorL->id,
                'estado'        => Pedido::ESTADO_ASIGNADO,
            ]);

            /* ── Actualizar estado del repartidor ── */
            $repartidorL->update([
                'estado'          => Repartidor::ESTADO_OCUPADO,
                'estado_operativo'=> Repartidor::OP_ASSIGNED,
            ]);

            return $asignacion;
        });
    }

    /**
     * Cancela la asignación activa de un pedido y libera al repartidor.
     */
    public function cancelAssignment(PedidoAsignacion $asignacion): void
    {
        DB::transaction(function () use ($asignacion): void {
            $asignacion->update([
                'status'      => PedidoAsignacion::STATUS_CANCELADO,
                'canceled_at' => now(),
            ]);

            /* Liberar repartidor solo si no tiene otro pedido activo */
            $repartidor = Repartidor::lockForUpdate()->find($asignacion->repartidor_id);
            if ($repartidor) {
                $tieneOtro = PedidoAsignacion::query()
                    ->where('repartidor_id', $repartidor->id)
                    ->where('status', PedidoAsignacion::STATUS_ACTIVO)
                    ->where('id', '!=', $asignacion->id)
                    ->exists();

                if (! $tieneOtro) {
                    $repartidor->update([
                        'estado'           => Repartidor::ESTADO_DISPONIBLE,
                        'estado_operativo' => Repartidor::OP_AVAILABLE,
                    ]);
                }
            }
        });
    }

    /**
     * Marca la asignación como completada (pedido entregado).
     */
    public function completeAssignment(PedidoAsignacion $asignacion): void
    {
        DB::transaction(function () use ($asignacion): void {
            $asignacion->update([
                'status'       => PedidoAsignacion::STATUS_COMPLETADO,
                'completed_at' => now(),
            ]);

            $repartidor = Repartidor::lockForUpdate()->find($asignacion->repartidor_id);
            if ($repartidor) {
                $repartidor->update([
                    'estado'           => Repartidor::ESTADO_DISPONIBLE,
                    'estado_operativo' => Repartidor::OP_AVAILABLE,
                ]);
            }
        });
    }

    /**
     * Repartidores disponibles con GPS actualizado recientemente.
     */
    private function availableDriversWithGps(): Collection
    {
        return Repartidor::query()
            ->where('estado', Repartidor::ESTADO_DISPONIBLE)
            ->whereNotNull('latitud_actual')
            ->whereNotNull('longitud_actual')
            ->where('ubicacion_actualizada_at', '>=', now()->subMinutes(self::GPS_STALE_MINUTES))
            ->get();
    }
}
