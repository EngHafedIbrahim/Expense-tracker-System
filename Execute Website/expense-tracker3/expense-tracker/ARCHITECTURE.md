# Expense Tracker - Architecture Documentation

**وثائق معمارية متتبع النفقات**

## Project Structure

```
expense-tracker-website/
├── includes/                 # Shared PHP files
│   ├── config.php           # Database configuration
│   ├── auth.php             # Authentication functions
│   ├── exceptions.php       # Exception handling
│   ├── header.php           # Header component
│   └── footer.php           # Footer component
│
├── controllers/             # Business logic (MVC pattern)
│   ├── LoginController.php
│   ├── RegisterController.php
│   ├── DashboardController.php
│   ├── ExpenseController.php
│   ├── CategoryController.php
│   └── BudgetController.php
│
├── views/                   # HTML templates
│   ├── login.html
│   ├── register.html
│   ├── dashboard.html
│   ├── expenses.html
│   ├── categories.html
│   └── budgets.html
│
├── api/                     # API endpoints
│   ├── expenses.php
│   ├── categories.php
│   └── budgets.php
│
├── assets/                  # Static files
│   ├── css/
│   │   └── style.css
│   └── js/
│       └── script.js
│
├── logs/                    # Error logs
│   └── error.log
│
├── login.php                # Login entry point
├── register.php             # Register entry point
├── dashboard.php            # Dashboard entry point
├── logout.php               # Logout handler
├── expenses.php             # Expenses entry point
├── categories.php           # Categories entry point
├── budgets.php              # Budgets entry point
│
├── DATABASE_SETUP.sql       # Database schema
├── EXCEPTION_HANDLING.md    # Exception handling guide
├── ARCHITECTURE.md          # This file
└── README.md                # Project documentation
```

## MVC Architecture Pattern

The application follows the Model-View-Controller (MVC) pattern:

### 1. **Model Layer** (Database)
- Database schema in `DATABASE_SETUP.sql`
- Database connection in `includes/config.php`
- Query helpers in controllers

### 2. **View Layer** (HTML Templates)
- Located in `views/` directory
- Pure HTML with PHP variable interpolation
- Separated from business logic
- Reusable components in `includes/`

### 3. **Controller Layer** (Business Logic)
- Located in `controllers/` directory
- Handles form processing
- Manages data validation
- Communicates with database
- Passes data to views

## File Organization

### Entry Point Files (Root Directory)

Each page has an entry point PHP file that:
1. Includes configuration and dependencies
2. Instantiates the controller
3. Processes the request
4. Loads and renders the view

**Example: login.php**
```php
<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'controllers/LoginController.php';

$controller = new LoginController($conn);
$controller->handleLogin();

ob_start();
include 'views/login.html';
$content = ob_get_clean();
echo $content;
?>
```

### Controller Files

Controllers contain:
- Form processing logic
- Data validation
- Database operations
- Error handling
- Data preparation for views

**Example: LoginController.php**
```php
class LoginController {
    private $conn;
    private $error = '';
    
    public function handleLogin() {
        // Validation
        // Database query
        // Error handling
        // Session management
    }
    
    public function getError() {
        return $this->error;
    }
}
```

### View Files

Views contain:
- HTML markup
- CSS styling
- Form elements
- Data display
- PHP variable interpolation only

**Example: login.html**
```html
<?php if ($controller->hasError()): ?>
    <div class="alert alert-error">
        <?php echo htmlspecialchars($controller->getError()); ?>
    </div>
<?php endif; ?>
```

### Shared Components

Located in `includes/`:
- `header.php` - Navigation and header
- `footer.php` - Footer content
- `config.php` - Configuration and database connection
- `auth.php` - Authentication functions
- `exceptions.php` - Exception classes and handlers

## Data Flow

### User Registration Flow

```
1. User visits register.php
   ↓
2. register.php includes RegisterController
   ↓
3. RegisterController::handleRegister() processes POST data
   ↓
4. Validation and error checking
   ↓
5. Database insertion if valid
   ↓
6. register.php includes views/register.html
   ↓
7. View displays form with error/success messages
   ↓
8. User sees result
```

### User Login Flow

```
1. User visits login.php
   ↓
2. login.php includes LoginController
   ↓
3. LoginController::handleLogin() processes POST data
   ↓
4. Email and password validation
   ↓
5. Database query for user
   ↓
6. Password verification
   ↓
7. Session creation if valid
   ↓
8. Redirect to dashboard or show error
```

## Exception Handling Architecture

### Exception Hierarchy

```
Exception (PHP Built-in)
    ↓
AppException (Base custom exception)
    ├── DatabaseException
    ├── AuthException
    ├── ValidationException
    ├── AuthorizationException
    ├── NotFoundException
    └── InvalidInputException
```

### Exception Flow

```
1. Exception thrown in controller or database operation
   ↓
2. Caught in try-catch block
   ↓
3. Logged via ErrorLogger
   ↓
4. User-friendly message extracted
   ↓
5. Passed to view for display
   ↓
6. User sees appropriate error message
```

## Database Architecture

### Tables

1. **users**
   - id (PK)
   - openId (unique)
   - name
   - email (unique)
   - password
   - role (enum: user, admin)
   - createdAt
   - updatedAt
   - lastSignedIn

2. **categories**
   - id (PK)
   - user_id (FK)
   - name
   - description
   - color
   - icon
   - createdAt
   - updatedAt

3. **expenses**
   - id (PK)
   - user_id (FK)
   - category_id (FK)
   - amount
   - description
   - date
   - createdAt
   - updatedAt

4. **budgets**
   - id (PK)
   - user_id (FK)
   - month
   - year
   - amount
   - createdAt
   - updatedAt

### Relationships

```
users (1) ──→ (many) categories
users (1) ──→ (many) expenses
users (1) ──→ (many) budgets
categories (1) ──→ (many) expenses
```

## Security Architecture

### Authentication

- Session-based authentication
- Password hashing with bcrypt
- Session validation on each request
- Logout functionality

### Authorization

- User ownership validation
- Role-based access control
- Protected routes requiring login

### Input Validation

- Server-side validation
- Prepared statements for SQL injection prevention
- Input sanitization
- Email format validation
- Password strength requirements

### Error Handling

- No sensitive information in user-facing errors
- Detailed logging for debugging
- Different messages for development/production

## API Architecture

### RESTful Endpoints

Located in `api/` directory:

```
POST /api/expenses.php          # Create expense
GET  /api/expenses.php          # Get expenses
PUT  /api/expenses.php          # Update expense
DELETE /api/expenses.php        # Delete expense

POST /api/categories.php        # Create category
GET  /api/categories.php        # Get categories
PUT  /api/categories.php        # Update category
DELETE /api/categories.php      # Delete category

POST /api/budgets.php           # Create budget
GET  /api/budgets.php           # Get budgets
PUT  /api/budgets.php           # Update budget
DELETE /api/budgets.php         # Delete budget
```

### API Response Format

```json
{
    "success": true,
    "data": { /* response data */ },
    "message": "Operation successful"
}
```

## Dependency Injection

Controllers receive database connection via constructor:

```php
$controller = new LoginController($conn);
```

This allows for:
- Easy testing with mock connections
- Loose coupling between components
- Centralized configuration

## Configuration Management

All configuration in `includes/config.php`:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'expense_tracker');
define('SITE_URL', 'http://localhost/expense-tracker-website');
define('ENVIRONMENT', 'development');
define('DEBUG_MODE', true);
```

## Logging Architecture

### Log Levels

- **ERROR**: Critical errors requiring attention
- **WARNING**: Potential issues (failed login attempts)
- **INFO**: Important events (user registration)
- **DEBUG**: Development information (queries, timing)

### Log Format

```
[2026-05-04 17:30:45] [ERROR] Database connection failed | Context: {...}
```

### Log Rotation

- Automatic rotation at 5MB
- Keeps last 5 rotated files
- Located in `logs/` directory

## Best Practices

### 1. Separation of Concerns
- Controllers handle logic
- Views handle presentation
- Models handle data

### 2. DRY (Don't Repeat Yourself)
- Reusable components in `includes/`
- Shared CSS in `assets/css/style.css`
- Common functions in `includes/auth.php`

### 3. Error Handling
- Try-catch blocks in controllers
- Specific exception types
- User-friendly error messages

### 4. Security
- Input validation before database operations
- Prepared statements for queries
- Password hashing
- Session validation

### 5. Code Organization
- One controller per feature
- One view per page
- Logical directory structure
- Clear naming conventions

## Extending the Application

### Adding a New Feature

1. **Create Controller**
   ```php
   // controllers/FeatureController.php
   class FeatureController {
       // Business logic
   }
   ```

2. **Create View**
   ```html
   <!-- views/feature.html -->
   <!-- HTML template -->
   ```

3. **Create Entry Point**
   ```php
   // feature.php
   require_once 'controllers/FeatureController.php';
   $controller = new FeatureController($conn);
   include 'views/feature.html';
   ```

4. **Create API Endpoint** (if needed)
   ```php
   // api/feature.php
   // RESTful endpoint
   ```

## Performance Considerations

### Database
- Use indexes on frequently queried columns
- Limit query results with pagination
- Cache expensive queries

### Frontend
- Minimize CSS/JS files
- Use CSS Grid/Flexbox for layouts
- Lazy load images
- Minify assets in production

### Caching
- Browser caching for static assets
- Server-side caching for queries
- Session caching for user data

## Testing

### Unit Tests
- Test controllers in isolation
- Mock database connections
- Test validation logic

### Integration Tests
- Test full request/response cycle
- Test database operations
- Test error handling

### Manual Testing
- Test all user flows
- Test error scenarios
- Test on multiple browsers

## Deployment

### Production Checklist

- [ ] Set ENVIRONMENT to 'production'
- [ ] Disable DEBUG_MODE
- [ ] Update database credentials
- [ ] Update SITE_URL
- [ ] Set proper file permissions
- [ ] Enable HTTPS
- [ ] Configure error logging
- [ ] Set up database backups
- [ ] Configure email notifications

## Support and Maintenance

### Regular Tasks
- Monitor error logs
- Update dependencies
- Backup database
- Review security logs

### Troubleshooting
- Check error logs in `logs/error.log`
- Verify database connection
- Check file permissions
- Review exception messages

## References

- [PHP Best Practices](https://www.php.net/manual/en/security.php)
- [MVC Pattern](https://en.wikipedia.org/wiki/Model%E2%80%93view%E2%80%93controller)
- [Exception Handling](./EXCEPTION_HANDLING.md)
- [Database Schema](./DATABASE_SETUP.sql)
