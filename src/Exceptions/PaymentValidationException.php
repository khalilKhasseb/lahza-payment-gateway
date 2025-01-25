<?php

namespace Lahza\PaymentGateway\Exceptions;

use Google\Service\Batch\Message;
use Illuminate\Support\MessageBag;

class PaymentValidationException extends PaymentException
{
    public array $errors;

    public function __construct(MessageBag $errors)
    {
        parent::__construct(
            'validation_error',  // Error code from ErrorCodes::MAP
            [
                'error_count' => $errors->count(),
                'errors' => $errors->getMessages()
            ]// Context array
        );
        $this->errors = $errors->toArray();
    }
}
