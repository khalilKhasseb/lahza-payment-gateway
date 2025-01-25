<?php

namespace Lahza\PaymentGateway\Exceptions;

class ErrorCodes
{
    const MAP = [
        'invalid_api_key' => [
            'code' => 401,
            'message' => 'Invalid API key provided',
            'type' => 'authentication_error'
        ],
        'invalid_amount' => [
            'code' => 400,
            'message' => 'Amount must be a positive integer',
            'type' => 'invalid_request_error'
        ],
        'missing_required_field' => [
            'code' => 400,
            'message' => 'Required field is missing: %s',
            'type' => 'validation_error'
        ],
        'currency_not_supported' => [
            'code' => 400,
            'message' => 'Currency %s is not supported',
            'type' => 'invalid_request_error'
        ],
        'rate_limit_exceeded' => [
            'code' => 429,
            'message' => 'Too many requests - please wait before retrying',
            'type' => 'rate_limit_error'
        ],
        'payment_processing_failed' => [
            'code' => 402,
            'message' => 'Payment could not be processed: %s',
            'type' => 'payment_error'
        ],
       'validation_error' => [
            'code' => 422,
            'message' => 'Validation failed for %d fields',
            'type' => 'validation_error'
        ],
    ];

    public static function getMessage(string $errorCode, array $context = []): string
    {
        $template = self::MAP[$errorCode]['message'] ?? 'Unknown error occurred';
        return vsprintf($template, $context);
    }

    public static function getHttpCode(string $errorCode): int
    {
        return self::MAP[$errorCode]['code'] ?? 500;
    }

    public static function getType(string $errorCode): string
    {
        return self::MAP[$errorCode]['type'] ?? 'unknown_error';
    }

}
