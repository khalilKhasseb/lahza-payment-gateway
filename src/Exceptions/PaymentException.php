<?php

namespace Lahza\PaymentGateway\Exceptions;

use Exception;
// use ErrorCodes;
class PaymentException extends Exception
{
    protected string $errorType;
    protected array $context;

    public function __construct(
        string $errorCode,
        array $context = [],
        \Throwable $previous = null
    ) {
        // $message = ErrorCodes::getMessage($errorCode, $context);
        // $code = ErrorCodes::getHttpCode($errorCode);

        parent::__construct(
            ErrorCodes::getMessage($errorCode, $context),
            ErrorCodes::getHttpCode($errorCode),
            $previous
        );

        $this->errorType = ErrorCodes::MAP[$errorCode]['type'] ?? 'api_error';
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
