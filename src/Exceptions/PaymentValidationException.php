<?php

namespace Lahza\PaymentGateway\Exceptions;

use Google\Service\Batch\Message;
use Illuminate\Support\MessageBag;

class PaymentValidationException extends PaymentException
{
    public array $errors;

    public function __construct(MessageBag $errors)
    {
        $errorMessages = $errors->getMessages();
        
        parent::__construct('validation_error', [
            'count' => count($errorMessages),
            'fields' => implode(', ', array_keys($errorMessages)),
            'errors' => $errorMessages 
        ]);
        $this->errors = $errors->toArray() ;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
