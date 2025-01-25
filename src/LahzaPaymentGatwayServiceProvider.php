<?php

namespace Lahza\PaymentGateway;

use Illuminate\Support\ServiceProvider;
use Lahza\PaymentGateway\LahzaGateway;

class LahzaPaymentGatwayServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/lahza.php', 'lahza');

        $this->app->singleton('lahza', function ($app) {
            return new LahzaGateway(
                config('lahza.api_key'),
                config('lahza.base_url'),
                config('lahza.timeout'),
                config('lahza.retries'),
                config('lahza.retry_delay')
            );
        });


        $this->app->alias('lahza', LahzaGateway::class);

        $this->app->bind(
            \Lahza\PaymentGateway\Exceptions\PaymentException::class,
            function ($app, $params) {
                return $app->make(\Lahza\PaymentGateway\Http\Controllers\ErrorController::class)
                    ->render($params['exception']);
            }
        );
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/lahza.php' => config_path('lahza.php'),
        ], 'lahza-config');

        $this->loadRoutesFrom(__DIR__ . '/routes/webhooks.php');
    }
}
