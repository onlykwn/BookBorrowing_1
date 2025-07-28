CREATE DATABASE IF NOT EXISTS book_system;
USE book_system;

CREATE TABLE books (
  book_id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255),
  author VARCHAR(255),
  genre VARCHAR(100),
  status ENUM('Available', 'Borrowed') DEFAULT 'Available'
);

CREATE TABLE borrowers (
  borrower_id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255),
  contact VARCHAR(20)
);

CREATE TABLE borrow_records (
  record_id INT AUTO_INCREMENT PRIMARY KEY,
  book_id INT,
  borrower_name VARCHAR(255),
  contact VARCHAR(20),
  borrow_date DATE,
  due_date DATE,
  FOREIGN KEY (book_id) REFERENCES books(book_id) ON DELETE CASCADE
);
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin', 'student') NOT NULL DEFAULT 'student',
  status ENUM('pending', 'approved') NOT NULL DEFAULT 'pending'  -- ✅ Make sure this line exists
);