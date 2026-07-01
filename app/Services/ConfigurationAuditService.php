<?php

namespace App\Services;

use App\Models\ConfiguracionAuditoria;
use Illuminate\Support\Facades\Auth;

class ConfigurationAuditService
{
    public function record(string $entity, ?int $entityId, string $action, array $changes): void
    {
        ConfiguracionAuditoria::query()->create([
            'user_id' => Auth::id(),
            'entidad' => $entity,
            'entidad_id' => $entityId,
            'accion' => $action,
            'cambios' => $changes,
        ]);
    }
}
