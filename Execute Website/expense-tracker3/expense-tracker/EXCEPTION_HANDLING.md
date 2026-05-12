# Exception Handling Guide

**دليل معالجة الاستثناءات**

## Overview

This document describes the comprehensive exception handling system implemented in the Expense Tracker application. The system provides centralized error management, logging, and user-friendly error messages.

## Exception Classes

### 1. AppException (Base Class)
The base exception class for all custom exceptions.

```php
throw new AppException(
    "Internal message",
    1000,
    "User-friendly message"
);
```

### 2. DatabaseException
Thrown when database operations fail.

```php
try {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    if (!$stmt) {
        throw new DatabaseException(
            "Prepare failed: " . $conn->error,
            1001,
            "فشل الاتصال بقاعدة البيانات"
        );
    }
} catch (DatabaseException $e) {
    ErrorLogger::error($e->getLogMessage());
    // Handle error
}
```

### 3. AuthException
Thrown when authentication fails.

```php
if (!verifyPassword($password, $hash)) {
    throw new AuthException(
        "Wrong password",
        2001,
        "كلمة المرور غير صحيحة"
    );
}
```

### 4. ValidationException
Thrown when input validation fails. Supports multiple field errors.

```php
if (empty($email)) {
    $errors = new ValidationException(
        "Missing required fields",
        3001,
        ['email' => 'البريد الإلكتروني مطلوب'],
        "يرجى ملء جميع الحقول"
    );
    $errors->addError('password', 'كلمة المرور مطلوبة');
    throw $errors;
}
```

### 5. AuthorizationException
Thrown when user lacks required permissions.

```php
if ($user['role'] !== 'admin') {
    throw new AuthorizationException(
        "User is not admin",
        4001,
        "ليس لديك صلاحيات كافية"
    );
}
```

### 6. NotFoundException
Thrown when a requested resource is not found.

```php
if ($result->num_rows === 0) {
    throw new NotFoundException(
        "User not found with ID: " . $userId,
        5001,
        "المستخدم غير موجود"
    );
}
```

### 7. InvalidInputException
Thrown when input is invalid or malformed.

```php
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    throw new InvalidInputException(
        "Invalid email format",
        6001,
        "البريد الإلكتروني غير صحيح"
    );
}
```

## Error Logger

The `ErrorLogger` class provides centralized logging functionality.

### Logging Methods

```php
// Log an error
ErrorLogger::error("Database connection failed", [
    'host' => DB_HOST,
    'database' => DB_NAME
]);

// Log a warning
ErrorLogger::warning("Failed login attempt", [
    'email' => $email,
    'ip' => $_SERVER['REMOTE_ADDR']
]);

// Log info
ErrorLogger::info("User registered successfully", [
    'user_id' => $userId,
    'email' => $email
]);

// Log debug (only in development mode)
ErrorLogger::debug("Query executed", [
    'query' => $sql,
    'duration' => $time
]);
```

### Log File Location

Logs are stored in `/logs/error.log` and automatically rotated when they exceed 5MB.

### Log Format

```
[2026-05-04 17:30:45] [ERROR] Database connection failed | Context: {"host":"localhost","database":"expense_tracker"}
[2026-05-04 17:31:12] [WARNING] Failed login attempt | Context: {"email":"user@example.com","ip":"192.168.1.1"}
[2026-05-04 17:32:00] [INFO] User registered successfully | Context: {"user_id":1,"email":"user@example.com"}
```

## Global Exception Handler

The application registers a global exception handler that catches all uncaught exceptions.

### Error Page Display

When an exception is thrown:
- In **development mode**: Shows detailed error information including file, line number, and stack trace
- In **production mode**: Shows a user-friendly error message without technical details

### Automatic Logging

All exceptions are automatically logged with:
- Timestamp
- Exception type
- Error message
- File and line number
- Stack trace

## Usage Examples

### Database Operations

```php
try {
    $stmt = $conn->prepare("SELECT * FROM expenses WHERE user_id = ?");
    
    if (!$stmt) {
        throw new DatabaseException(
            "Prepare failed: " . $conn->error,
            1001,
            "فشل استرجاع البيانات"
        );
    }
    
    $stmt->bind_param("i", $userId);
    
    if (!$stmt->execute()) {
        throw new DatabaseException(
            "Execute failed: " . $stmt->error,
            1001,
            "فشل استرجاع البيانات"
        );
    }
    
    $result = $stmt->get_result();
    
    if (!$result) {
        throw new DatabaseException(
            "Get result failed: " . $conn->error,
            1001,
            "فشل استرجاع البيانات"
        );
    }
    
    // Process result
} catch (DatabaseException $e) {
    $error = $e->getUserMessage();
    ErrorLogger::error($e->getLogMessage());
} catch (Exception $e) {
    $error = "حدث خطأ غير متوقع";
    ErrorLogger::error("Unexpected error: " . $e->getMessage());
}
```

### Form Validation

```php
try {
    if (empty($email) || empty($password)) {
        throw new ValidationException(
            "Missing required fields",
            3001,
            [
                'email' => empty($email) ? 'البريد الإلكتروني مطلوب' : '',
                'password' => empty($password) ? 'كلمة المرور مطلوبة' : ''
            ],
            "يرجى ملء جميع الحقول"
        );
    }
    
    if (!validateEmail($email)) {
        throw new ValidationException(
            "Invalid email format",
            3001,
            ['email' => 'البريد الإلكتروني غير صحيح'],
            "البريد الإلكتروني غير صحيح"
        );
    }
    
    // Process form
} catch (ValidationException $e) {
    $error = $e->getUserMessage();
    $fieldErrors = $e->getErrors();
    ErrorLogger::warning("Validation error", $fieldErrors);
}
```

### Authentication

```php
try {
    if (!isLoggedIn()) {
        throw new AuthException(
            "User not authenticated",
            2001,
            "يرجى تسجيل الدخول أولاً"
        );
    }
    
    $user = getCurrentUser();
    
    if (!$user) {
        throw new NotFoundException(
            "User not found in session",
            5001,
            "بيانات المستخدم غير موجودة"
        );
    }
    
    // Process authenticated request
} catch (AuthException $e) {
    header("Location: " . SITE_URL . "/login.php");
    exit;
} catch (NotFoundException $e) {
    ErrorLogger::error($e->getLogMessage());
    header("Location: " . SITE_URL . "/logout.php");
    exit;
}
```

## Error Codes

| Code | Exception Type | Description |
|------|---|---|
| 1001 | DatabaseException | Database connection or query error |
| 2001 | AuthException | Authentication failure |
| 3001 | ValidationException | Input validation failure |
| 4001 | AuthorizationException | Insufficient permissions |
| 5001 | NotFoundException | Resource not found |
| 6001 | InvalidInputException | Invalid input format |

## Best Practices

### 1. Always Use Try-Catch for Database Operations

```php
try {
    // Database operation
} catch (DatabaseException $e) {
    // Handle database error
} catch (Exception $e) {
    // Handle unexpected error
}
```

### 2. Provide User-Friendly Messages

```php
// Good: User-friendly message
throw new ValidationException(
    "Internal: Email format validation failed",
    3001,
    ['email' => 'البريد الإلكتروني غير صحيح'],
    "البريد الإلكتروني غير صحيح"
);

// Bad: Technical message shown to user
throw new ValidationException("Regex validation failed");
```

### 3. Log Important Events

```php
// Log successful operations
ErrorLogger::info('User logged in successfully', [
    'user_id' => $user['id'],
    'email' => $email
]);

// Log failed attempts
ErrorLogger::warning('Failed login attempt', [
    'email' => $email,
    'ip' => $_SERVER['REMOTE_ADDR']
]);
```

### 4. Validate Input Before Database Operations

```php
try {
    // Validate first
    if (empty($email)) {
        throw new ValidationException(...);
    }
    
    if (!validateEmail($email)) {
        throw new ValidationException(...);
    }
    
    // Then query database
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    // ...
} catch (ValidationException $e) {
    // Handle validation error
} catch (DatabaseException $e) {
    // Handle database error
}
```

### 5. Check Prepare and Execute Results

```php
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");

// Always check prepare result
if (!$stmt) {
    throw new DatabaseException(
        "Prepare failed: " . $conn->error,
        1001,
        "فشل الاتصال بقاعدة البيانات"
    );
}

// Always check execute result
if (!$stmt->execute()) {
    throw new DatabaseException(
        "Execute failed: " . $stmt->error,
        1001,
        "فشل الاتصال بقاعدة البيانات"
    );
}
```

## Configuration

### Environment Settings

Edit `includes/config.php`:

```php
// Set to 'production' for production environment
define('ENVIRONMENT', 'development');

// Debug mode - shows detailed errors in development
define('DEBUG_MODE', ENVIRONMENT === 'development');
```

### Log File Location

Edit `includes/exceptions.php`:

```php
// Change log file location
ErrorLogger::setLogFile('/custom/path/error.log');
```

## Monitoring and Maintenance

### Viewing Logs

```bash
# View recent errors
tail -f logs/error.log

# Search for specific errors
grep "ERROR" logs/error.log

# Count errors by type
grep -o "\[ERROR\]" logs/error.log | wc -l
```

### Log Rotation

Logs are automatically rotated when they exceed 5MB. The last 5 rotated logs are kept.

### Clearing Old Logs

```bash
# Remove logs older than 30 days
find logs/ -name "error.log.*" -mtime +30 -delete
```

## Security Considerations

1. **Never expose sensitive information** in error messages shown to users
2. **Always log detailed errors** for debugging purposes
3. **Use different messages** for internal logging and user display
4. **Sanitize error output** to prevent XSS attacks
5. **Restrict log file access** to authorized users only

## Troubleshooting

### Logs Not Being Created

- Check directory permissions: `chmod 755 logs/`
- Verify PHP has write access to the logs directory
- Check `php.ini` error_log settings

### Errors Not Being Logged

- Verify `ENVIRONMENT` is set correctly
- Check `ErrorLogger::setLogFile()` path is correct
- Ensure log file is writable

### Error Page Not Displaying

- Check if exception handler is registered in `config.php`
- Verify `includes/exceptions.php` is included
- Check PHP error_reporting settings

## Support

For issues or questions about exception handling, refer to the main README.md or contact support.
