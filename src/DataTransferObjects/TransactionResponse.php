<?php

namespace Lahza\PaymentGateway\DataTransferObjects;

class TransactionResponse
{
    public function __construct(
        public readonly string $reference,
        public readonly string $authorizationUrl,
        public readonly string $accessCode,
        public readonly ?float $amount = null,
        public readonly ?string $currency = null,
        public readonly ?string $status = null,
        public readonly ?string $transactionDate = null
    ) {}

    public static function fromInitializationResponse(array $response): self
    {
        $data = $response['data'];

        return new self(
            reference: $data['reference'],
            authorizationUrl: $data['authorization_url'],
            accessCode: $data['access_code']
        );
    }

    public static function fromVerificationResponse(array $response): self
    {
        $data = $response['data'];
        $amount = isset($data['amount']) ? $data['amount'] / 100 : null;

        return new self(
            reference: $data['reference'],
            authorizationUrl: '',
            accessCode: '',
            amount: $amount,
            currency: $data['currency'],
            status: $data['status'],
            transactionDate: $data['transaction_date'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'reference' => $this->reference,
            'authorization_url' => $this->authorizationUrl,
            'access_code' => $this->accessCode,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'status' => $this->status,
            'transaction_date' => $this->transactionDate
        ];
    }
}
