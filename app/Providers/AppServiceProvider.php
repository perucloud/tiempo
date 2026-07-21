<?php

namespace App\Providers;

use App\Contracts\Geo\GeocodingProviderInterface;
use App\Models\Cliente;
use App\Models\Pedido;
use App\Observers\ClienteObserver;
use App\Observers\PedidoObserver;
use App\Contracts\Geo\RoutingProviderInterface;
use App\Services\DeliveryPricingService;
use App\Services\Geo\MapConfigurationService;
use App\Services\Geo\NominatimProvider;
use App\Services\Geo\OsrmRoutingProvider;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            GeocodingProviderInterface::class,
            NominatimProvider::class,
        );

        $this->app->bind(
            RoutingProviderInterface::class,
            OsrmRoutingProvider::class,
        );

        $this->app->bind(DeliveryPricingService::class, function ($app) {
            return new DeliveryPricingService(
                $app->make(RoutingProviderInterface::class),
            );
        });

        $this->app->bind(\App\Services\DriverAssignmentService::class, function ($app) {
            return new \App\Services\DriverAssignmentService(
                $app->make(RoutingProviderInterface::class),
            );
        });
    }

    public function boot(): void
    {
        Cliente::observe(ClienteObserver::class);
        Pedido::observe(PedidoObserver::class);

        RedirectIfAuthenticated::redirectUsing(function (Request $request): string {
            if (auth()->guard('cliente')->check()) {
                return route('app.inicio');
            }
            return url('/');
        });

        $geoViews = [
            'admin.businesses.form',
            'admin.settings.zone-form',
        ];

        View::composer($geoViews, function ($view): void {
            $view->with('geoConfig', $this->app->make(MapConfigurationService::class)->jsConfig());
        });
    }
}
