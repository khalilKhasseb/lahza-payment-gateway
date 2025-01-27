<?php

namespace Lahza\PaymentGateway\Contracts;

interface LahzaGateway
{
    public function initializeTransaction(array $data);
    public function verifyTransaction(string $reference);
    public function listTransactions(array $filters = []);
    public function fetchTransaction(string $id);
    public function chargeAuthorization(array $data);
    public function captureTransaction(string $reference);
    public function viewTransactionTimeline(string $idOrReference);
    
    // Webhooks
    public function verifyWebhookSignature(string $payload, string $signature): bool;
    
    // Testing
    public function fake();
}