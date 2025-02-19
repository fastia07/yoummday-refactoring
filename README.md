# Yoummday Permission Handler
A PHP-based permission handling service that verifies token permissions through a RESTful API endpoint.

## Overview
This service provides a simple yet robust way to validate tokens and their associated permissions. The main endpoint `/has_permission/{token}` determines if a given token exists and has the required permission level.

## Features
- Token validation and permission checking
- Support for multiple permission types (read/write)
- RESTful API endpoint
- Comprehensive error handling
- Full test coverage

## Requirements
- PHP 8.1 or higher
- Composer

## Installation
```shell
$ composer install
```

## Running the Server
```shell 
$ php src/main.php
```
The server will start on `http://127.0.0.1:1337`.

Expected output:
```shell
[INFO] Registering GET /has_permission/{token}
[INFO] Server running on 127.0.0.1:1337
```

## API Documentation

### Check Token Permission
**Endpoint:** `GET /has_permission/{token}`

**Query Parameters:**
- `permission` (optional): The permission to check for. Default is 'read'

**Response Format:**
```json
{
    "has_permission": boolean,
    "token": string,
    "permission": string
}
```

**Status Codes:**
- 200: Success
- 400: Bad Request (Missing token)
- 404: Not Found (Invalid token)

**Example Requests:**
```shell
# Check read permission (default)
curl http://127.0.0.1:1337/has_permission/tokenReadonly

# Check write permission
curl "http://127.0.0.1:1337/has_permission/token1234?permission=write"
```

## Testing
Run the test suite:
```shell
$ php vendor/bin/phpunit Test
```

The test suite covers:
- Permission validation
- Error handling
- Default behaviors
- Edge cases

## Code Structure
- `src/App/Handler/PermissionHandler.php`: Main endpoint handler
- `src/App/DTO/Token.php`: Token data transfer object
- `src/App/HTTP/Response/JSONResponse.php`: JSON response handler
- `Test/Handler/PermissionHandlerTest.php`: Test suite

## Design Principles
The codebase follows:
- KISS (Keep It Simple, Stupid)
- DRY (Don't Repeat Yourself)
- Single Responsibility Principle
- Clean Code practices

## Available Tokens
- `token1234`: Has read and write permissions
- `tokenReadonly`: Has read-only permission
