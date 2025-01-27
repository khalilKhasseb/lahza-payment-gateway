<?php

namespace Lahza\PaymentGateway\Exceptions;

use Exception;
// use ErrorCodes;
class PaymentException extends Exception
{
    protected string $errorType;
    protected array $context;

    public function __construct(
        string $errorCode,        // Error code key from ErrorCodes::MAP
        array $context = [],      // Additional error context
        \Throwable $previous = null
    ) {
        $message = ErrorCodes::getMessage($errorCode, $context);
        $httpCode = ErrorCodes::getHttpCode($errorCode);
    
        parent::__construct($message, $httpCode, $previous);
    
        $this->errorType = ErrorCodes::getType($errorCode);
        $this->context = $context;
    }

    public function getErrorType(): string
    {
        return $this->errorType;
    }

    public function getContext(): array
    {
        return $this->context;
    }
    public function getDocumentationUrl(): string
    {
        return config('lahza.documentation_base_url', 'https://api-docs.lahza.io/errors/') . $this->getCode();
    }


    public function toArray(): array
    {
        return [
            'error' => $this->getErrorType(),
            'error_code' => $this->getCode(),
            'message' => $this->getMessage(),
            'context' => $this->getContext()
        ];
    }
}
