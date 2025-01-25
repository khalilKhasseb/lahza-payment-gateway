<?php
namespace Lahza\PaymentGateway\Exceptions;

class WebhookVerificationException extends PaymentException
{
    public function __construct()
    {
        parent::__construct(
            'invalid_webhook_signature',
            [],
            null
        );
    }
}