<?php

namespace Lahza\PaymentGateway\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Lahza\PaymentGateway\LahzaGateway fake()
 * @method static array createPaymentIntent(array $data)
 * @method static array confirmPayment(string $paymentId)
 * @method static array refundPayment(string $paymentId, float $amount)
 * @method static array getPayment(string $paymentId)
 * @method static bool verifyWebhook(string $payload, string $signature)
 * @method static SuccessResponse createPaymentIntent(array $data)

 */
class Lahza extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'lahza';
    }
}
