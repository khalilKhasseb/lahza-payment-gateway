<?php

/**
 * Lahza Payment Gateway Service Provider
 * 
 * @author Khalil Khasseb <khalil.khasseb@proton.me>
 */

namespace Lahza\PaymentGateway;

use Illuminate\Support\ServiceProvider;
use Lahza\PaymentGateway\LahzaGateway;

class LahzaPaymentGatewayServiceProvider extends ServiceProvider
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
        if (config('lahza.webhook.enabled')) {
            $this->loadRoutesFrom(__DIR__ . '/../routes/webhooks.php');
            $this->app['router']->aliasMiddleware(
                'lahza.webhook',
                \Lahza\PaymentGateway\Http\Middleware\VerifyLahzaWebhook::class
            );
        }
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/lahza.php' => config_path('lahza.php'),
        ], 'lahza-config');

        $this->loadRoutesFrom(__DIR__ . '/routes/webhooks.php');
    }
}
