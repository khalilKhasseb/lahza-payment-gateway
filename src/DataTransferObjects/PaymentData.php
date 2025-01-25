<?php
namespace Lahza\PaymentGateway\DataTransferObjects;

class PaymentData
{
    public function __construct(
        public readonly string $authorizationUrl,
        public readonly string $accessCode,
        public readonly string $reference
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            authorizationUrl: $data['authorization_url'],
            accessCode: $data['access_code'],
            reference: $data['reference']
        );
    }

    public function toArray(): array
    {
        return [
            'authorization_url' => $this->authorizationUrl,
            'access_code' => $this->accessCode,
            'reference' => $this->reference
        ];
    }
}