# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

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