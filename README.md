# Library Management System

A complete full-stack Library Management System built with PHP, MySQL, HTML, CSS, and Bootstrap 5. This system is designed for librarians to manage books, students, and book circulation efficiently.

## Features

- **Admin Authentication**: Secure login system with session management
- **Dashboard**: Real-time statistics showing total books, students, issued/returned books
- **Book Management**: Add, edit, delete, and search books
- **Student Management**: Add, delete, and search students
- **Book Issue System**: Issue books to students with return date tracking
- **Book Return System**: Mark books as returned with automatic quantity updates
- **Reports**: View issued books, returned books, and overdue books
- **Responsive Design**: Mobile-friendly interface using Bootstrap 5
- **Security**: Prepared statements for SQL injection prevention

## Project Structure

```
library/
├── admin/
│   ├── login.php          # Admin login page
│   ├── logout.php         # Admin logout
│   ├── dashboard.php      # Main dashboard with statistics
│   ├── add_book.php       # Add new book
│   ├── manage_books.php   # View, search, delete books
│   ├── edit_book.php      # Edit book details
│   ├── add_student.php    # Add new student
│   ├── manage_students.php # View, search, delete students
│   ├── issue_book.php     # Issue book to student
│   ├── return_book.php    # Return issued book
│   └── reports.php        # View all reports
├── assets/
│   ├── css/
│   │   └── style.css      # Custom CSS styles
│   └── js/                # JavaScript files (if needed)
├── includes/
│   ├── db.php             # Database connection
│   ├── header.php         # Reusable header with navbar and sidebar
│   └── footer.php         # Reusable footer
└── database/
    └── library.sql        # SQL database schema and sample data
```

## Database Structure

### Tables

1. **admins**
   - id, name, email, password, created_at

2. **books**
   - id, title, author, category, isbn, quantity, available_quantity, added_date

3. **students**
   - id, name, email, phone, created_at

4. **issued_books**
   - id, book_id, student_id, issue_date, return_date, status, created_at

## Setup Instructions

### Prerequisites

- XAMPP (or any PHP/MySQL server)
- PHP 7.0 or higher
- MySQL 5.6 or higher

### Step 1: Install XAMPP

1. Download XAMPP from https://www.apachefriends.org/
2. Install XAMPP on your computer
3. Start Apache and MySQL services from XAMPP Control Panel

### Step 2: Setup Database

1. Open phpMyAdmin: http://localhost/phpmyadmin
2. Click on "New" to create a new database
3. Import the SQL file:
   - Click on "Import" tab
   - Choose the file: `database/library.sql`
   - Click "Go"
   
   Alternatively, you can run the SQL commands manually:
   ```sql
   -- Open the database/library.sql file in a text editor
   -- Copy all the SQL commands
   -- Paste them in the SQL tab of phpMyAdmin
   -- Click "Go"
   ```

### Step 3: Configure Database Connection

1. Open `includes/db.php`
2. Verify the database credentials:
   ```php
   $host = 'localhost';
   $username = 'root';
   $password = '';
   $database = 'library_management';
   ```
3. Update if your MySQL credentials are different

### Step 4: Deploy the Application

1. Copy the entire `library` folder to:
   ```
   C:\xampp\htdocs\library\
   ```

2. Or place it in your web server's document root

### Step 5: Access the Application

1. Open your web browser
2. Navigate to: http://localhost/library/admin/login.php
3. Login with default credentials:
   - Email: `admin@library.com`
   - Password: `admin123`

## Default Login Credentials

- **Email**: admin@library.com
- **Password**: admin123

**Note**: The password is hashed using PHP's `password_hash()` function. To change the password, update it in the database using:
```php
password_hash('your_new_password', PASSWORD_DEFAULT)
```

## Usage Guide

### Adding Books

1. Navigate to "Manage Books" from the sidebar
2. Click "Add New Book"
3. Fill in the book details (title, author, category, ISBN, quantity)
4. Click "Add Book"

### Managing Students

1. Navigate to "Manage Students" from the sidebar
2. Click "Add New Student"
3. Fill in student details (name, email, phone)
4. Click "Add Student"

### Issuing Books

1. Navigate to "Issue Book" from the sidebar
2. Select a student from the dropdown
3. Select a book from the dropdown (only available books are shown)
4. Set the issue date and return date
5. Click "Issue Book"
6. The book's available quantity will automatically decrease

### Returning Books

1. Navigate to "Return Book" from the sidebar
2. View all currently issued books
3. Click "Return" next to the book you want to return
4. The book's available quantity will automatically increase

### Viewing Reports

1. Navigate to "Reports" from the sidebar
2. Switch between tabs to view:
   - Issued Books
   - Returned Books
   - Overdue Books

## Security Features

- **Prepared Statements**: All database queries use prepared statements to prevent SQL injection
- **Session Management**: Secure session-based authentication
- **Password Hashing**: Admin passwords are hashed using PHP's `password_hash()`
- **Input Validation**: Form inputs are validated and sanitized

## Customization

### Changing Admin Password

1. Open phpMyAdmin
2. Go to the `library_management` database
3. Open the `admins` table
4. Edit the admin record
5. Generate a new password hash using:
   ```php
   echo password_hash('your_new_password', PASSWORD_DEFAULT);
   ```
6. Replace the existing password hash with the new one

### Adding More Admins

1. Open phpMyAdmin
2. Go to the `library_management` database
3. Open the `admins` table
4. Insert a new record with:
   - name: Admin name
   - email: Admin email
   - password: Hashed password (use password_hash())

## Troubleshooting

### Database Connection Error

If you see "Connection failed", check:
1. MySQL service is running in XAMPP
2. Database credentials in `includes/db.php` are correct
3. Database `library_management` exists

### Session Issues

If you're logged out frequently:
1. Check PHP session settings in php.ini
2. Ensure session.save_path is writable

### Blank Pages

If you see blank pages:
1. Enable error reporting in PHP
2. Check Apache error logs
3. Verify file permissions

## Technologies Used

- **Backend**: PHP (Core PHP, no frameworks)
- **Database**: MySQL
- **Frontend**: HTML5, CSS3
- **Framework**: Bootstrap 5
- **Icons**: Bootstrap Icons
- **Security**: Prepared Statements, Password Hashing

## Browser Compatibility

- Google Chrome (recommended)
- Mozilla Firefox
- Microsoft Edge
- Safari

## License

This project is open source and free to use for educational purposes.

## Support

For issues or questions, please check the troubleshooting section or review the code comments.

## Future Enhancements

- Email notifications for overdue books
- Book reservation system
- Fine calculation for late returns
- Export reports to PDF/Excel
- Multi-language support
- Book cover image upload
- Advanced search with filters
