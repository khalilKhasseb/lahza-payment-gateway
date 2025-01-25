<?php

namespace Lahza\PaymentGateway\Http\Middleware;

use Closure;
use Lahza\PaymentGateway\Facades\Lahza;

class VerifyLahzaWebhook
{
    public function handle($request, Closure $next)
    {
        $signature = $request->header('X-Lahza-Signature');
        $payload = $request->getContent();

        if (!Lahza::verifyWebhook($payload, $signature)) {
            abort(401, 'Invalid webhook signature');
        }

        return $next($request);
    }
}