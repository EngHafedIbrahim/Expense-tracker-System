# Expense Tracker Website

**نظام إدارة المصروفات**

A complete PHP-based expense tracking system with Arabic language support (RTL layout), built with MySQL database and modern web technologies.

## Features

- 🔐 User authentication (login, register, logout)
- 💰 Expense management (create, read, update, delete)
- 📂 Category management with custom colors
- 💵 Monthly budget planning and tracking
- 📊 Dashboard with statistics and charts
- 📈 Expense reports and analytics
- 🌍 Full Arabic language support (RTL)
- 📱 Responsive design for mobile devices
- ✨ Modern, elegant UI with gradient backgrounds

## Project Structure

```
expense-tracker-website/
├── includes/
│   ├── config.php          # Database configuration
│   ├── auth.php            # Authentication functions
│   ├── header.php          # Header component
│   └── footer.php          # Footer component
├── assets/
│   ├── css/
│   │   └── style.css       # Main stylesheet
│   ├── js/
│   │   └── script.js       # JavaScript functionality
│   └── images/             # Image assets
├── pages/                  # Additional pages
├── admin/                  # Admin pages
├── login.php               # Login page
├── register.php            # Registration page
├── logout.php              # Logout page
├── dashboard.php           # Dashboard page
├── expenses.php            # Expenses management
├── categories.php          # Categories management
├── budget.php              # Budget management
├── reports.php             # Reports page
├── profile.php             # User profile
├── DATABASE_SETUP.sql      # Database schema
└── README.md               # This file
```

## Installation

### Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache, Nginx, etc.)
- Composer (optional)

### Setup Steps

1. **Clone or download the project**
   ```bash
   cd /path/to/expense-tracker-website
   ```

2. **Create MySQL database**
   ```bash
   mysql -u root -p < DATABASE_SETUP.sql
   ```

3. **Configure database connection**
   Edit `includes/config.php` and update:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', 'your_password');
   define('DB_NAME', 'expense_tracker');
   define('SITE_URL', 'http://localhost/expense-tracker-website');
   ```

4. **Set file permissions**
   ```bash
   chmod 755 includes/
   chmod 755 assets/
   ```

5. **Start your web server**
   - For Apache: Place the folder in `htdocs` and access via `http://localhost/expense-tracker-website`
   - For PHP built-in server: `php -S localhost:8000`

6. **Access the application**
   - Open `http://localhost:8000` in your browser
   - Create a new account or login

## Database Schema

### Users Table
- `id` - Primary key
- `name` - User full name
- `email` - Email address (unique)
- `password` - Hashed password
- `role` - User role (user/admin)
- `created_at` - Account creation date
- `updated_at` - Last update date

### Categories Table
- `id` - Primary key
- `user_id` - Foreign key to users
- `name` - Category name
- `color` - Hex color code
- `icon` - Category icon/emoji
- `description` - Category description
- `created_at` - Creation date
- `updated_at` - Last update date

### Expenses Table
- `id` - Primary key
- `user_id` - Foreign key to users
- `category_id` - Foreign key to categories
- `amount` - Expense amount
- `description` - Expense description
- `expense_date` - Date of expense
- `created_at` - Creation date
- `updated_at` - Last update date

### Budgets Table
- `id` - Primary key
- `user_id` - Foreign key to users
- `month` - Month (1-12)
- `year` - Year
- `budget_amount` - Budget amount
- `created_at` - Creation date
- `updated_at` - Last update date

## File Descriptions

### includes/config.php
Database configuration and connection setup. Contains database credentials and site settings.

### includes/auth.php
Authentication helper functions including:
- `isLoggedIn()` - Check if user is logged in
- `requireLogin()` - Redirect if not logged in
- `getCurrentUser()` - Get current user data
- `sanitize()` - Sanitize user input
- `hashPassword()` - Hash password
- `verifyPassword()` - Verify password

### includes/header.php
Header component with navigation menu, brand, and user actions. Included at the top of every page.

### includes/footer.php
Footer component with links and copyright information. Included at the bottom of every page.

### assets/css/style.css
Main stylesheet with:
- CSS variables for colors and spacing
- Responsive grid layouts
- Component styles (buttons, cards, tables)
- Arabic RTL support
- Mobile-first responsive design

### assets/js/script.js
JavaScript functionality including:
- Mobile menu toggle
- Currency and date formatting
- Alert notifications
- Form handling
- Table sorting
- Search functionality

## Usage

### Creating an Account
1. Click "إنشاء حساب" (Create Account) on the login page
2. Fill in your details
3. Click "إنشاء الحساب" (Create Account)
4. Login with your credentials

### Adding an Expense
1. Go to "المصروفات" (Expenses) in the navigation
2. Click "إضافة مصروف" (Add Expense)
3. Fill in the expense details
4. Click "حفظ" (Save)

### Managing Categories
1. Go to "التصنيفات" (Categories)
2. Create, edit, or delete categories
3. Assign colors and icons to categories

### Setting a Budget
1. Go to "الميزانية" (Budget)
2. Set your monthly budget
3. Track your spending against the budget

### Viewing Reports
1. Go to "التقارير" (Reports)
2. View expense breakdowns by category
3. See monthly trends and statistics

## Security Features

- Password hashing with bcrypt
- SQL injection prevention with prepared statements
- XSS protection with htmlspecialchars()
- CSRF protection with session tokens
- User ownership validation on all operations
- Secure session management

## Customization

### Changing Colors
Edit the CSS variables in `assets/css/style.css`:
```css
:root {
    --primary: #2563eb;
    --accent: #14b8a6;
    --danger: #ef4444;
    /* ... more colors ... */
}
```

### Changing Fonts
Modify the font-family in `assets/css/style.css`:
```css
body {
    font-family: 'Cairo', 'Tajawal', 'Segoe UI', Tahoma, sans-serif;
}
```

### Adding New Pages
1. Create a new PHP file in the root directory
2. Include the header: `require_once 'includes/header.php';`
3. Add your content
4. Include the footer: `require_once 'includes/footer.php';`
5. Add navigation link in `includes/header.php`

## Troubleshooting

### Database Connection Error
- Check database credentials in `includes/config.php`
- Ensure MySQL server is running
- Verify database exists

### Login Issues
- Clear browser cookies
- Check if user exists in database
- Verify password is correct

### Arabic Text Not Displaying
- Check charset is UTF-8MB4
- Verify Cairo font is loaded
- Check browser language settings

### Permission Denied Errors
- Run `chmod 755` on folders
- Check file ownership
- Verify web server has read/write permissions

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers (iOS Safari, Chrome Mobile)

## Performance Tips

- Use database indexes on frequently queried columns
- Cache category data in session
- Optimize images before uploading
- Use CDN for static assets
- Enable gzip compression on server

## Future Enhancements

- [ ] Export to PDF/CSV
- [ ] Recurring expenses
- [ ] Expense tags
- [ ] Multi-currency support
- [ ] Dark mode theme
- [ ] Mobile app
- [ ] API for third-party integrations
- [ ] Advanced analytics and charts
- [ ] Budget alerts and notifications
- [ ] Shared budgets for families

## Support

For issues, questions, or suggestions, please create an issue in the project repository.

## License

This project is provided as-is for educational and personal use.

## Credits

Built with:
- PHP
- MySQL
- HTML5
- CSS3
- JavaScript
- Cairo Font (Arabic typography)

---

**Version**: 1.0.0  
**Last Updated**: May 2026  
**Language**: Arabic (RTL) + English  
**Status**: Production Ready
