## [1.0.0] - 2023-09-20
### Added
- Initial package release by Khalil Khasseb

## [1.1.0] - 2023-10-15
### Added
- Transaction and TransactionResponse DTO classes for better type safety
- Currency conversion handling for ILS, JOD, and USD
- New config options: `inline_callback`, `callback_url`, `default_currency`
- Conditional webhook route loading based on config
- Enhanced error handling with detailed context

### Changed
- Renamed core methods for consistency:
  - `createPaymentIntent` → `initializeTransaction`
  - `confirmPayment` → `verifyTransaction`
- Updated currency validation to only support USD, ILS, JOD
- Improved error messages with dynamic placeholders
- Amounts now automatically convert to minor units (cents/agora)

### Fixed
- Service provider class name typo (`LahzaPaymentGatwayServiceProvider` → `LahzaPaymentGatewayServiceProvider`)
- Facade alias correction (`Lazha` → `Lahza`)
- Webhook secret configuration reference
- Currency conversion handling in Transaction DTO

### Removed
- Local package repository configuration from composer.json
- Redundant error handling middleware