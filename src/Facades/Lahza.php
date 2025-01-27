<?php

namespace Lahza\PaymentGateway\Facades;

use Illuminate\Support\Facades\Facade;
use Lahza\PaymentGateway\DataTransferObjects\Transaction;

/**
 * @method static \Lahza\PaymentGateway\LahzaGateway fake() Enable testing mode with mocked responses
* @method static \Lahza\PaymentGateway\LahzaGateway fake() Enable testing mode
 * @method static TransactionResponse initializeTransaction(array $data) Initialize new payment
 * @method static TransactionResponse verifyTransaction(string $reference) Verify payment status * @method static Transaction[] listTransactions(array $filters = []) List transactions with optional filters
 * @method static Transaction fetchTransaction(string $id) Get details of a specific transaction
 * @method static Transaction chargeAuthorization(array $data) Charge a reusable authorization
 * @method static Transaction captureTransaction(string $reference) Capture a pre-authorized transaction
 * @method static array viewTransactionTimeline(string $idOrReference) Get transaction timeline events
 * @method static bool verifyWebhookSignature(string $payload, string $signature) Validate webhook signature
 * @method static string getDefaultCurrency() Retrieve default currency∂
 * @method static getCallbackUrl() Retrieve callback URL
 * @see \Lahza\PaymentGateway\LahzaGateway
 */
class Lahza extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'lahza';
    }
}