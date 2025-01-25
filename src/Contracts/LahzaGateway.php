<?php

namespace Lahza\PaymentGateway\Contracts;

interface LahzaGateway
{
    public function createPaymentIntent(array $data);
    public function confirmPayment(string $paymentId);
    public function refundPayment(string $paymentId, float $amount);
    public function getPayment(string $paymentId);
    public function verifyWebhook(string $payload, string $signature);
    public function fake();
}