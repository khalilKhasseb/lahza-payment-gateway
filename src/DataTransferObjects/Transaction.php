<?php

namespace Lahza\PaymentGateway\DataTransferObjects;

class Transaction
{
    public function __construct(
        public readonly string $id,
        public readonly string $reference,
        public readonly float $amount,
        public readonly string $currency,
        public readonly string $status,
        public readonly array $authorization,
        public readonly array $customer,
        public readonly ?string $authorizationUrl = null,
        public readonly ?string $accessCode = null,
    ) {}

    public static function fromApiResponse(array $response): self
    {
        $data = $response['data'];
       $divisor = self::getDivisor($data['currency']);
        return new self(
            id: $data['id'] ?? $data['reference'],
            reference: $data['reference'],
            amount: $data['amount'] / $divisor,
            currency: $data['currency'],
            status: $data['status'],
            authorization: $data['authorization'] ?? [],
            customer: $data['customer'],
            authorizationUrl: $data['authorization_url'] ?? null,
            accessCode: $data['access_code'] ?? null,
        );
    }


    private static function getDivisor(string $currency): int
    {
        return match($currency) {
            'ILS' => 100,  // Convert agora to ILS
            'JOD' => 100,  // Convert qirsh to JOD
            'USD' => 100,  // Convert cents to USD
            default => 1,
        };
    }
}