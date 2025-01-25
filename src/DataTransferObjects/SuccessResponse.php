<?php

namespace Lahza\PaymentGateway\DataTransferObjects;
use Lahza\PaymentGateway\DataTransferObjects\PaymentData;
class SuccessResponse
{
    public function __construct(
        public readonly bool $status,
        public readonly string $message,
        public readonly PaymentData $data
    ) {}


    public function hasAuthorizationUrl(): bool
    {
        return !empty($this->data->authorizationUrl);
    }

    public function getAuthorizationUrl(): string
    {
        return $this->data->authorizationUrl;
    }

    public function getReference(): string
    {
        return $this->data->reference;
    }

    public function getAccessCode(): string
    {
        return $this->data->accessCode;
    }
    public static function fromArray(array $data): self
    {
        return new self(
            status: $data['status'],
            message: $data['message'],
            data: PaymentData::fromArray($data['data'])
        );
    }



    public function toArray(): array
    {
        return [
            'status' => $this->status,
            'message' => $this->message,
            'data' => $this->data->toArray()
        ];
    }
}
