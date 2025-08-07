# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.2.0] - 2025-08-07

### Added
- **WebhookService**: Added centralized webhook processing service
  - `processWebhook()`: Process complete webhook data and return Event and typed payload
  - `parseWebhookData()`: Parse webhook data into an Event object with validation
  - `parseWebhookPayload()`: Parse payload into typed payload object
  - `isEventTypeSupported()`: Check if event type is supported
  - `isKnownEventType()`: Check if event type is a known enum case
  - `getEventTypeEnum()`: Get WebhookEventType enum for event type
  - `getSupportedEventTypes()`: Get all supported event type prefixes
  - Built-in validation of webhook structure with descriptive error messages
  - Consistent error handling with PreludeException
  - Maintains existing value object architecture while providing service-layer convenience
- **WebhookService Example**: Added comprehensive example demonstrating service usage
- **WebhookService Tests**: Complete test suite with 15 test cases covering all functionality
- **Webhook Event Class**: Added `Event` value object for webhook processing
  - Common properties: `id`, `type`, `created_at`, and `payload`
  - Generic `array` type for payload to handle variable structures across different API endpoints
  - Immutable `DateTimeImmutable` for timestamp handling
  - Type-safe getters for all properties
  - Optional payload parameter with empty array default
- **Webhook Example**: Added comprehensive webhook event processing example
- **Event Tests**: Complete test suite for Event class functionality
  - Tests for payload handling with different structures
  - Tests for optional payload parameter
- **GenericPayload Class**: Added generic payload handler for unsupported webhook event types
  - Flexible data access with `get()`, `has()`, and `getData()` methods
  - Support for complex nested structures
  - Fallback for unknown webhook event types
- **WebhookEventType Enum Integration**: Enhanced webhook event handling with type safety
  - `Event::isKnownEventType()`: Check if event type matches a known enum case
  - `Event::getEventTypeEnum()`: Get WebhookEventType enum case for known types
  - `EventPayloadFactory::_isKnownEventType()`: Internal enum validation support
  - Enhanced `EventPayloadFactory::isSupported()` to recognize enum cases
- **Comprehensive Test Coverage**: Added tests for enum integration functionality
  - Event enum detection and validation tests
  - Factory enum support and payload creation tests
  - All 225 tests passing with 773 assertions

### Changed
- **Coding Style**: Removed underscore prefixes from protected methods in webhook payload classes
  - Updated `AbstractEventPayload`, `GenericPayload`, `TransactionalPayload`, and `VerifyPayload`
  - Aligned with coding convention that only private methods should use underscores
  - All tests pass, functionality preserved

### Improved
- **Webhook Integration**: Better support for processing webhook responses from Verify API
- **Type Safety**: Strict typing for webhook event properties with flexible payload handling
- **Documentation**: Added example demonstrating webhook event filtering, processing, and payload access
- **Type Safety**: Enhanced webhook event type validation using WebhookEventType enum
- **Developer Experience**: Better IDE support and autocomplete for webhook event types
- **Code Examples**: Updated webhook processing example to demonstrate enum usage
  - Type-safe event handling with enum-based switch statements
  - Fallback handling for unknown event types
  - Enhanced debugging information with enum detection
- **Webhook Architecture**: Complete refactoring of webhook value objects
  - **AbstractEventPayload**: Enhanced base class with validation, parsing, and helper methods
  - **VerifyPayload**: Refactored with private properties, type-safe getters, and validation
  - **TransactionalPayload**: Refactored with private properties, type-safe getters, and validation
  - **Price**: Enhanced with validation, formatting, and utility methods
  - **EventPayloadFactory**: Improved with better error handling, type detection, and extensibility
- **Error Handling**: Comprehensive validation and meaningful error messages
- **Code Quality**: Consistent patterns, private properties with getters, and proper encapsulation
- **Test Coverage**: Complete test suites for all webhook classes (218 tests passing)

### Fixed
- **Data Parsing**: Improved handling of both nested and flat payload structures
- **Segment Count**: Fixed return type to always return integer (0 for missing values)
- **Price Formatting**: Enhanced string representation with proper decimal formatting

## [1.1.0] - 2025-08-05

### Added
- **LookupType Enum**: Added `LookupType` enum for type-safe lookup parameter specification
  - `LookupType::CNAM` for caller name lookup requests
  - Provides better type safety and IDE autocompletion

### Changed
- **LookupService**: Updated method signature documentation to use `LookupType[]` instead of generic array
- **Examples**: Updated lookup examples to demonstrate proper enum usage
- **README**: Enhanced lookup service documentation with enum usage examples

### Improved
- **Type Safety**: Lookup operations now benefit from compile-time type checking
- **Developer Experience**: Better IDE support and autocompletion for lookup types

## [1.0.0] - 2025-08-03

### Added

#### Core SDK
- **PreludeClient**: Main SDK client with service access
- **HttpClient**: HTTP client wrapper with authentication and error handling
- **Configuration**: SDK configuration management with API key and base URL

#### Services
- **LookupService**: Phone number lookup and validation
  - Basic phone number information retrieval
  - CNAM (Caller Name) lookup support
  - Network information retrieval
  - Line type detection
- **TransactionalService**: Transactional messaging
  - Template-based message sending
  - Variable substitution support
  - Message status tracking
- **VerificationService**: Phone number verification
  - OTP generation and sending
  - Code verification
  - Resend functionality
  - Expiration handling
- **WatchService**: Fraud detection and monitoring
  - Outcome prediction
  - Event dispatching
  - Feedback submission

#### Data Models
- **LookupResponse**: Phone number lookup results
- **TransactionalResponse**: Transactional message results
- **VerificationResponse**: Verification operation results
- **WatchResponse**: Watch service results
- **NetworkInfo**: Network carrier information

#### Value Objects
- **Transactional\Options**: Transactional message options
- **Verification\Options**: Verification options
- **Watch\Event**: Watch event data
- **Watch\Feedback**: Watch feedback data
- **Watch\Options**: Watch service options

#### Enums
- **Channel**: Communication channels (SMS, Voice, WhatsApp)
- **Confidence**: Confidence levels (High, Medium, Low)
- **Flag**: Phone number flags (Ported, Prepaid, etc.)
- **LineType**: Phone line types (Mobile, FixedLine, Voip, etc.)
- **LookupType**: Lookup data types (CNAM)

#### Exception Handling
- **PreludeException**: Base SDK exception
- **ApiException**: API-specific exceptions with detailed error information
- **ConfigurationException**: Configuration-related exceptions
- **ValidationException**: Input validation exceptions

#### Examples
- **lookup.php**: Comprehensive phone number lookup examples
- **transactional.php**: Transactional messaging examples
- **verification.php**: Phone verification examples
- **watch.php**: Fraud detection and monitoring examples

#### Testing
- **Pest PHP**: Modern testing framework integration
- **PHPStan**: Static analysis for code quality
- **PHP_CodeSniffer**: Code style enforcement
- **Code Coverage**: 80% minimum coverage requirement
- **GitHub Actions**: Automated CI/CD pipeline
- **Codecov**: Code coverage reporting

#### Documentation
- **README.md**: Comprehensive usage guide with examples
- **API Documentation**: Links to official Prelude.so API docs
- **Installation Guide**: Composer installation instructions
- **Configuration Guide**: SDK setup and configuration
- **Error Handling**: Exception handling best practices

#### Development Tools
- **Docker**: Containerized development environment
- **Composer Scripts**: Automated testing and coverage commands
- **EditorConfig**: Consistent code formatting
- **Git Hooks**: Pre-commit quality checks

### Requirements
- **PHP**: 8.1 or higher
- **ext-json**: JSON extension
- **GuzzleHttp**: ^7.0 for HTTP client functionality

### Security
- **API Key Authentication**: Secure API key handling
- **HTTPS**: All API communications over HTTPS
- **Input Validation**: Comprehensive input sanitization
- **Error Sanitization**: Sensitive data protection in error messages

[1.1.0]: https://github.com/prelude-so/sdk/releases/tag/v1.1.0
[1.0.0]: https://github.com/prelude-so/sdk/releases/tag/v1.0.0