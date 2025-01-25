<?php
namespace Lahza\PaymentGateway\Exceptions;
class PaymentConnectionException extends PaymentException
{
    public function __construct(string $message, int $code = 503)
    {
        parent::__construct($message, $code);
    }
}
