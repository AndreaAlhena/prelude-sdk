# Docker Development Environment

This project includes a Docker development environment with all necessary dependencies, including PCOV for code coverage.

## Prerequisites

- Docker
- Docker Compose

## Quick Start

1. **Build the Docker image:**
   ```bash
   composer docker:build
   # or
   docker-compose build
   ```

2. **Start the development environment:**
   ```bash
   composer docker:up
   # or
   docker-compose up -d
   ```

3. **Run tests:**
   ```bash
   composer docker:test
   # or
   docker-compose exec php composer test
   ```

4. **Run tests with coverage:**
   ```bash
   composer docker:coverage
   # or
   docker-compose exec php composer test:coverage
   ```

## Available Commands

### Docker Management
- `composer docker:build` - Build the Docker image
- `composer docker:up` - Start containers in detached mode
- `composer docker:down` - Stop and remove containers

### Testing
- `composer docker:test` - Run all tests
- `composer docker:coverage` - Run tests with coverage report

### Development
```bash
# Enter the container for interactive development
docker-compose exec php bash

# Install dependencies
docker-compose exec php composer install

# Run code style checks
docker-compose exec php composer cs-check

# Fix code style issues
docker-compose exec php composer cs-fix

# Run static analysis
docker-compose exec php composer analyse

# Generate HTML coverage report
docker-compose exec php composer test:coverage-html
```

## Features

- **PHP 8.3** with CLI
- **Composer** for dependency management
- **PCOV** extension for fast code coverage
- **Volume mounting** for live code editing
- **Persistent vendor directory** for faster rebuilds

## Coverage Reports

HTML coverage reports are generated in the `coverage/` directory when using:
```bash
docker-compose exec php composer test:coverage-html
```

Open `coverage/index.html` in your browser to view detailed coverage information.

## Troubleshooting

### Rebuild containers
If you encounter issues, try rebuilding:
```bash
docker-compose down
docker-compose build --no-cache
docker-compose up -d
```

### Clear vendor cache
```bash
docker-compose down
docker volume rm prelude-sdk_vendor
docker-compose up -d
docker-compose exec php composer install
```