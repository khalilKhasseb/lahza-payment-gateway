# Lahza Payment Gateway

[![Latest Version on Packagist](https://img.shields.io/packagist/v/lahza/payment-gateway.svg)](https://packagist.org/packages/lahza/payment-gateway)
[![GitHub Tests Action Status](https://github.com/yourusername/lahza-payment-gateway/actions/workflows/tests.yml/badge.svg)](https://github.com/yourusername/lahza-payment-gateway/actions)
[![MIT License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](https://opensource.org/licenses/MIT)

A Laravel package for seamless integration with the Lahza Payment Gateway. Process payments, handle webhooks, and manage transactions with ease.

## Author

**Khalil Khasseb**  
📍 Palestine - Aroura  
✉️ [khalil.khasseb@proton.me](mailto:khalil.khasseb@proton.me)  
💻 [GitHub Profile](https://github.com/khalilkhasseb)

## Features

- 💳 Create payment intents
- 🔄 Handle payment confirmations
- ↩️ Process refunds
- 🕸️ Webhook verification
- 🛡️ Robust error handling
- ⚙️ Configurable settings
- 🧪 Fake mode for testing

## Installation

Install via Composer:

```bash
composer require lahza/payment-gateway
```

Publish config file:

```bash
php artisan vendor:publish --provider="Lahza\PaymentGateway\LahzaServiceProvider" --tag="lahza-config"
```

## Configuration

Add to your `.env` file:

```env
LAHZA_API_KEY=your_api_key
LAHZA_BASE_URL=https://api.lahza.io/v1/
LAHZA_WEBHOOK_SECRET=your_webhook_secret
LAHZA_TIMEOUT=15
LAHZA_RETRIES=3
LAHZA_CURRENCIES=USD,EUR,GBP
```

### Config Options (`config/lahza.php`)

| Key | Type | Description |
|-----|------|-------------|
| api_key | string | Your Lahza API key |
| base_url | string | API base URL |
| timeout | int | Request timeout in seconds |
| retries | int | Number of request retries |
| retry_delay | int | Delay between retries in milliseconds |
| webhook.secret | string | Webhook verification secret |
| webhook.middleware | array | Middleware for webhook routes |
| currencies | array | Supported currencies |

## Usage

### Create Payment Intent

```php
use Lahza\Facades\Lahza;

try {
    $payment = Lahza::createPaymentIntent([
        'amount' => 100.50,
        'currency' => 'USD',
        'reference' => 'ORDER-123',
        'customer' => [
            'email' => 'customer@example.com',
            'name' => 'John Doe'
        ]
    ]);
    
    return redirect()->away($payment->getAuthorizationUrl());
    
} catch (\Lahza\PaymentGateway\Exceptions\PaymentException $e) {
    // Handle error
}
```

### Confirm Payment

```php
$confirmedPayment = Lahza::confirmPayment($paymentId);
```

### Handle Webhooks

Add to `routes/web.php`:

```php
Route::post('/lahza/webhook', function (Request $request) {
    // Handle webhook
})->middleware('lahza.webhook');
```

### Error Handling

```php
try {
    // Payment operations
} catch (\Lahza\PaymentGateway\Exceptions\PaymentValidationException $e) {
    // Validation errors
    $errors = $e->getContext()['errors'];
} catch (\Lahza\PaymentGateway\Exceptions\PaymentException $e) {
    // General errors
    logger()->error($e->getDocumentationUrl(), $e->getContext());
}
```

Sample error response:
```json
{
    "error": "validation_error",
    "message": "Validation failed for 2 fields",
    "error_code": 422,
    "documentation": "https://api-docs.lahza.io/errors/422",
    "errors": {
        "amount": ["Must be at least 0.5"],
        "currency": ["Invalid currency"]
    }
}
```

## Testing

Enable fake mode:

```php
Lahza::fake();

// Test successful response
Lahza::fake([
    'createPaymentIntent' => [
        'status' => true,
        'data' => [
            'authorization_url' => 'https://checkout.lahza.io/fake'
        ]
    ]
]);
```

## TODO: Future Enhancements

- [ ] Add support for recurring payments
- [ ] Implement payment method management
- [ ] Add currency conversion utilities
- [ ] Support 3D Secure payments
- [ ] Create admin dashboard integration
- [ ] Add more test coverage
- [ ] Implement rate limiting
- [ ] Add payment dispute handling
- [ ] Support multiple API versions
- [ ] Add PCI-compliant card storage
- [ ] Develop mobile SDK integration
- [ ] Create webhook event factory
- [ ] Add payment analytics
- [ ] Support marketplace split payments
- [ ] Implement payment retry logic

## Documentation

Full API documentation available at [Lahza API Docs](https://api-docs.lahza.io)

## Contributing

1. Fork the project
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit changes (`git commit -m 'Add some amazing feature'`)
4. Push to branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## Security

If you discover any security issues, please email:  
🔒 [khalil.khasseb@proton.me](mailto:khalil.khasseb@proton.me)  
instead of using the issue tracker.


## License

The MIT License (MIT). See [LICENSE](LICENSE) for more information.