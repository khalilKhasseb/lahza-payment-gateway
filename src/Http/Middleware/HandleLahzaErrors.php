<?php
namespace Lahza\PaymentGateway\Http\Middleware;

use Closure;
use Lahza\PaymentGateway\Exceptions\PaymentException;

class HandleLahzaErrors
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        if ($response->isClientError() || $response->isServerError()) {
            $errorData = $response->json();
            
            throw new PaymentException(
                $errorData['error_code'] ?? 'unknown_error',
                $errorData['details'] ?? []
            );
        }

        return $response;
    }
}