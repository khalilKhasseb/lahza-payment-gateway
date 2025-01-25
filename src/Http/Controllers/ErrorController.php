<?php
namespace Lahza\PaymentGateway\Http\Controllers;

use Lahza\PaymentGateway\Exceptions\PaymentException;

class ErrorController
{
    public function render(PaymentException $exception)
    {
        return response()->json([
            'error' => $exception->getErrorType(),
            'code' => $exception->getCode(),
            'message' => $exception->getMessage(),
            'documentation_url' => 'https://api-docs.lahza.io/errors/'.$exception->getCode(),
            'details' => $exception->getContext()
        ], $exception->getCode());
    }
}