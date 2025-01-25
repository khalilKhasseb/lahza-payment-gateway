<?php

namespace Lahza\PaymentGateway;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

use Lahza\PaymentGateway\Exceptions\PaymentConnectionException;
use Lahza\PaymentGateway\Exceptions\PaymentException;
use Lahza\PaymentGateway\Exceptions\PaymentValidationException;
use Lahza\PaymentGateway\Exceptions\ErrorCodes;
use Lahza\PaymentGateway\DataTransferObjects\SuccessResponse;
class LahzaGateway implements Contracts\LahzaGateway
{
    protected $http;
    protected $fake = false;

    public function __construct(
        protected string $apiKey,
        protected string $baseUrl,
        protected int $timeout,
        protected int $retries,
        protected int $retryDelay
    ) {
        $this->http = $this->buildHttpClient();
    }


    public function createPaymentIntent(array $data)
    {
        $this->validate(
            $data,
            [
                'amount' => 'required|numeric|min:0.5',
                'currency' => 'required|in:' . implode(',', config('lahza.currencies')),
                'reference' => 'required|string|max:255',
                'customer.email' => 'required|email',
                'customer.name' => 'required|string|max:255',
            ],
            [
                'amount.min' => 'Amount must be at least 0.5 units',
                'currency.in' => 'Unsupported currency. Valid currencies: :values',
                'customer.email.required' => 'Customer email is required',
                // Add other custom messages as needed
            ]
        );



       $response =  $this->request('post', 'transaction/initialize', $data);

       $this->validateSuccessResponse($response);
        
       return SuccessResponse::fromArray($response);

    }

    public function confirmPayment(string $paymentId)
    {
        return $this->request('post', "payments/{$paymentId}/confirm");
    }

    public function refundPayment(string $paymentId, float $amount)
    {
        return $this->request('post', "payments/{$paymentId}/refund", [
            'amount' => $amount
        ]);
    }

    public function getPayment(string $paymentId)
    {
        return $this->request('get', "payments/{$paymentId}");
    }

    public function verifyWebhook(string $payload, string $signature)
    {
        $secret = config('lahza.webhook.secret');
        $computed = hash_hmac('sha256', $payload, $secret);

        return hash_equals($computed, $signature);
    }

    public function fake()
    {
        $this->fake = true;
        Http::fake();
        return $this;
    }

    protected function request(string $method, string $endpoint, array $data = [])
    {
        if ($this->fake) {
            return $this->fakeResponse($method, $endpoint);
        }

        try {
            $response = $this->http
                ->retry($this->retries, $this->retryDelay)
                ->{$method}($endpoint, $data);

            if ($response->failed()) {
                $this->handleError($response);
            }

            return $response->json();
        } catch (\Exception $e) {
            throw new PaymentConnectionException($e->getMessage(), $e->getCode(), $e);
        }
    }

    protected function buildHttpClient(): PendingRequest
    {
        return Http::baseUrl($this->baseUrl)
            ->withHeaders([
                // 'Authorization' => 'Bearer ' . $this->apiKey,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])
            ->withToken($this->apiKey)
            ->timeout($this->timeout);
    }

    protected function validate(array $data, array $rules, $messages = [])
    {
        $validator = Validator::make($data, $rules, $messages);

        if ($validator->fails()) {
            throw new PaymentValidationException($validator->errors());
        }
    }
    protected function validateSuccessResponse(array $response): void
    {
        $validator = Validator::make($response, [
            'status' => 'required|boolean',
            'message' => 'required|string',
            'data' => 'required|array',
            'data.authorization_url' => 'required|url',
            'data.access_code' => 'required|string',
            'data.reference' => 'required|string'
        ]);

        if ($validator->fails()) {
            throw new PaymentException(
                'invalid_success_response',
                $validator->errors()->toArray()
            );
        }
    }

    protected function handleError($response)
    {
        $status = $response->status();
        $error = $response->json('error');

        throw new PaymentException(
            $error['message'] ?? 'Payment processing failed',
            $status,
            $error['code'] ?? null
        );
    }

    // In LahzaGateway.php

    protected function validatePaymentRequest(array $data): void
    {
        $validator = Validator::make($data, [
            'amount' => [
                'required',
                'numeric',
                'min:0.01',
                function ($attribute, $value, $fail) {
                    if (!is_numeric($value) || $value <= 0) {
                        $fail(ErrorCodes::getMessage('invalid_amount'));
                    }
                }
            ],
            'currency' => [
                'required',
                'string',
                'size:3',
                function ($attribute, $value, $fail) {
                    if (!in_array(strtoupper($value), config('lahza.currencies'))) {
                        $fail(sprintf(
                            ErrorCodes::getMessage('currency_not_supported'),
                            $value
                        ));
                    }
                }
            ]
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            throw new PaymentException(
                'validation_error',
                ['errors' => $errors]
            );
        }
    }

    protected function fakeResponse($method, $endpoint)
    {
        return [
            'id' => 'fake_' . Str::uuid(),
            'status' => 'succeeded',
            'amount' => 100,
            'currency' => 'USD',
            'client_secret' => 'fake_secret_' . Str::random(40)
        ];
    }
}
