-- Library Management System Database
-- Create database
CREATE DATABASE IF NOT EXISTS library_management;
USE library_management;

-- Create admins table
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create books table
CREATE TABLE IF NOT EXISTS books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(100) NOT NULL,
    category VARCHAR(100) NOT NULL,
    isbn VARCHAR(20) NOT NULL UNIQUE,
    quantity INT NOT NULL DEFAULT 1,
    available_quantity INT NOT NULL DEFAULT 1,
    added_date DATE NOT NULL
);

-- Create students table
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create issued_books table
CREATE TABLE IF NOT EXISTS issued_books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    book_id INT NOT NULL,
    student_id INT NOT NULL,
    issue_date DATE NOT NULL,
    return_date DATE NOT NULL,
    status ENUM('issued', 'returned') DEFAULT 'issued',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);

-- Insert default admin (password: admin123)
INSERT INTO admins (name, email, password) VALUES 
('Admin', 'admin@library.com', '$2y$10$EmB7hsmt2oj3EVd7W1F93Ou8NlW2dsLd5cZgohTiwm.mMX4.M3QsO');

-- Insert sample books
INSERT INTO books (title, author, category, isbn, quantity, available_quantity, added_date) VALUES 
('The Great Gatsby', 'F. Scott Fitzgerald', 'Fiction', '978-0743273565', 5, 5, CURDATE()),
('To Kill a Mockingbird', 'Harper Lee', 'Fiction', '978-0061120084', 3, 3, CURDATE()),
('1984', 'George Orwell', 'Fiction', '978-0451524935', 4, 4, CURDATE()),
('Clean Code', 'Robert C. Martin', 'Programming', '978-0132350884', 2, 2, CURDATE()),
('The Pragmatic Programmer', 'Andrew Hunt', 'Programming', '978-0201616224', 3, 3, CURDATE());

-- Insert sample students
INSERT INTO students (name, email, phone) VALUES 
('John Doe', 'john@example.com', '1234567890'),
('Jane Smith', 'jane@example.com', '0987654321'),
('Bob Johnson', 'bob@example.com', '5555555555');
