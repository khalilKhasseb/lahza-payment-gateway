<?php

namespace Lahza\PaymentGateway;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Lahza\PaymentGateway\Exceptions\PaymentConnectionException;
use Lahza\PaymentGateway\Exceptions\PaymentException;
use Lahza\PaymentGateway\Exceptions\PaymentValidationException;
use Lahza\PaymentGateway\DataTransferObjects\SuccessResponse;
use Lahza\PaymentGateway\Contracts\LahzaGateway as PaymentContract;
use Lahza\PaymentGateway\DataTransferObjects\TransactionResponse;
use Lahza\PaymentGateway\DataTransferObjects\Transaction;

class LahzaGateway implements PaymentContract
{
    protected PendingRequest $http;
    protected bool $fake = false;

    public function __construct(
        protected string $apiKey,
        protected string $baseUrl,
        protected int $timeout,
        protected int $retries,
        protected int $retryDelay
    ) {
        $this->http = $this->buildHttpClient();
    }


    public function initializeTransaction(array $data): TransactionResponse
    {
        $this->validate($data, [
            'email' => 'required|email',
            'amount' => 'required|numeric|min:0.1',
            'currency' => 'required|in:ILS,JOD,USD',
            'reference' => 'nullable|string|max:255',
            'callback_url' => 'nullable|url',
            'metadata' => 'nullable|json',
        ]);

        // Convert amount to minor units
        $data['amount'] = (int)($data['amount'] * 100);



        $response = $this->post('/transaction/initialize', $data);


        return TransactionResponse::fromInitializationResponse($response);
    }

    public function verifyTransaction(string $reference): TransactionResponse
    {
        $response = $this->get("/transaction/verify/{$reference}");
        return TransactionResponse::fromVerificationResponse($response);
    }



    public function listTransactions(array $filters = []): array
    {
        $validated = validator($filters, [
            'perPage' => 'nullable|integer',
            'page' => 'nullable|integer',
            'status' => 'nullable|string',
            'from' => 'nullable|date',
            'to' => 'nullable|date',
        ])->validate();

        $response = $this->get('/transaction', $validated);
        return array_map([Transaction::class, 'fromApiResponse'], $response['data']);
    }

    public function fetchTransaction(string $id): Transaction
    {
        $response = $this->get("/transaction/{$id}");
        return Transaction::fromApiResponse($response);
    }

    public function chargeAuthorization(array $data): Transaction
    {
        $this->validate($data, [
            'amount' => 'required|numeric',
            'email' => 'required|email',
            'authorization_code' => 'required|string',
            'currency' => 'required|in:ILS,JOD,USD',
            'reference' => 'nullable|string|max:255',
            'queue' => 'nullable|boolean',
        ]);

        $response = $this->post('/transaction/charge_authorization', $data);
        return Transaction::fromApiResponse($response);
    }

    public function captureTransaction(string $reference): Transaction
    {
        $response = $this->post("/transaction/capture/{$reference}");
        return Transaction::fromApiResponse($response);
    }

    public function viewTransactionTimeline(string $idOrReference): array
    {
        return $this->get("/transaction/timeline/{$idOrReference}");
    }

    public function verifyWebhookSignature(string $payload, string $signature): bool
    {
        $secret = config('lahza.webhook_secret');
        $computed = hash_hmac('sha256', $payload, $secret);
        return hash_equals($computed, $signature);
    }

    public function fake(): self
    {
        $this->fake = true;
        Http::fake();
        return $this;
    }

    private function get(string $endpoint, array $params = [])
    {
        return $this->request('GET', $endpoint, $params);
    }

    private function post(string $endpoint, array $data = []): array
    {
        return $this->request('POST', $endpoint, $data);
    }

    private function request(string $method, string $endpoint, array $data = []): array
    {
        if ($this->fake) {
            return $this->fakeResponse($method, $endpoint);
        }

        $response = $this->http->{$method}($this->baseUrl . $endpoint, $data);

        if ($response->failed()) {
            $this->handleError($response);
        }

        return $response->json();
    }




    /**
     * Verify webhook signature
     */
    public function verifyWebhook(string $payload, string $signature): bool
    {
        $secret = config('lahza.webhook.secret');
        $computed = hash_hmac('sha256', $payload, $secret);

        return hash_equals($computed, $signature);
    }

    /**
     * Enable fake mode for testing
     */

    /**
     * Build HTTP client
     */
    protected function buildHttpClient(): PendingRequest
    {
        return Http::baseUrl($this->baseUrl)
            ->withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])
            ->timeout($this->timeout);
    }

    /**
     * Make API request
     */


    /**
     * Validate request data
     */
    protected function validate(array $data, array $rules, array $messages = []): void
    {
        $validator = Validator::make($data, $rules, $messages);

        if ($validator->fails()) {
            throw new PaymentValidationException($validator->errors());
        }
    }

    /**
     * Validate success response
     */
    protected function validateSuccessResponse(array $response): void
    {
        $validator = Validator::make($response, [
            'status' => 'required|boolean',
            'message' => 'required|string',
            'data' => 'required|array',
        ]);

        if ($validator->fails()) {
            throw new PaymentException(
                'invalid_success_response',
                $validator->errors()->toArray()
            );
        }
    }

    /**
     * Handle API errors
     */
    protected function handleError($response): void
    {
        $error = $response->json('error') ?? [];
        $status = $response->status();

        throw new PaymentException(
            // Error code from API response or fallback
            $error['code'] ?? 'payment_error',
            // Context array with all relevant details
            [
                'status_code' => $status,
                'api_message' => $error['message'] ?? 'Unknown payment error',
                'response' => $error
            ]
        );
    }

    /**
     * Generate fake response for testing
     */
    protected function fakeResponse(string $method, string $endpoint): array
    {
        return [
            'status' => true,
            'message' => 'Fake response for testing',
            'data' => [
                'authorization_url' => 'https://checkout.lahza.io/fake',
                'access_code' => 'fake_access_code',
                'reference' => 'fake_reference',
            ],
        ];
    }


    /**
     * Get the default currency set in the config
     *
     * @return string
     */
    public  function getDefaultCurrency(): string | array
    {
        return config('lahza.default_currency');
    }

   public function getCallbackUrl(): string | null
    {
        if (config('lahza.inline_callback')) {
            return config('lahza.callback_url');
        }

        return null ;
        
        return config('lahza.callback_url');
    }
}
