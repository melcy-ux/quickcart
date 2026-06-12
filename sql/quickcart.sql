-- QuickCart Database Schema
-- Unit: BIT3208 — Advanced Web Design and Development
-- Run this file in phpMyAdmin: click your database > SQL tab > paste and execute

CREATE DATABASE IF NOT EXISTS quickcart_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE quickcart_db;

-- ───────────────────────────────────────────
-- Table: users
-- ───────────────────────────────────────────
CREATE TABLE IF NOT EXISTS users (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    username   VARCHAR(60)  NOT NULL,
    email      VARCHAR(120) NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,
    role       ENUM('customer','admin') NOT NULL DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ───────────────────────────────────────────
-- Table: categories
-- ───────────────────────────────────────────
CREATE TABLE IF NOT EXISTS categories (
    id   INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(80) NOT NULL UNIQUE
);

-- ───────────────────────────────────────────
-- Table: products
-- ───────────────────────────────────────────
CREATE TABLE IF NOT EXISTS products (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name        VARCHAR(150) NOT NULL,
    description TEXT,
    price       DECIMAL(10,2) NOT NULL,
    stock       INT NOT NULL DEFAULT 0,
    image       VARCHAR(255) DEFAULT '',
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

-- ───────────────────────────────────────────
-- Table: cart
-- ───────────────────────────────────────────
CREATE TABLE IF NOT EXISTS cart (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    user_id    INT NOT NULL,
    product_id INT NOT NULL,
    quantity   INT NOT NULL DEFAULT 1,
    added_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id)    REFERENCES users(id)    ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- ───────────────────────────────────────────
-- Table: orders
-- ───────────────────────────────────────────
CREATE TABLE IF NOT EXISTS orders (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    user_id      INT NOT NULL,
    full_name    VARCHAR(120) NOT NULL,
    address      TEXT NOT NULL,
    phone        VARCHAR(20)  NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status       ENUM('pending','processing','completed','cancelled') NOT NULL DEFAULT 'pending',
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ───────────────────────────────────────────
-- Table: order_items
-- ───────────────────────────────────────────
CREATE TABLE IF NOT EXISTS order_items (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    order_id   INT NOT NULL,
    product_id INT NOT NULL,
    quantity   INT          NOT NULL,
    price      DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id)   REFERENCES orders(id)   ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- ───────────────────────────────────────────
-- Sample Data
-- ───────────────────────────────────────────

-- Admin account  (password: Admin@123)
INSERT INTO users (username, email, password, role) VALUES
('admin', 'admin@quickcart.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Test customer  (password: Test@1234)
INSERT INTO users (username, email, password, role) VALUES
('testuser', 'user@example.com', '$2y$10$TKh8H1.PfbuKZ7.scO5YiuH8tIu5yCGFmQMJrOXJUB7aKYpWE0wTe', 'customer');

-- Categories
INSERT INTO categories (name) VALUES
('Electronics'),
('Clothing'),
('Books'),
('Home & Kitchen'),
('Sports');

-- Products
INSERT INTO products (category_id, name, description, price, stock, image) VALUES
(1, 'Wireless Bluetooth Earbuds',
 'High-quality wireless earbuds with active noise cancellation, 24-hour battery life, and comfortable fit.',
 3500.00, 25, 'https://images.unsplash.com/photo-1590658268037-6bf12165a8df?w=400'),

(1, 'USB-C Laptop Charger 65W',
 'Universal USB-C charger compatible with most laptops and tablets. Fast-charge technology included.',
 2200.00, 40, 'https://images.unsplash.com/photo-1625723044792-44de16ccb4e9?w=400'),

(1, 'Mechanical Gaming Keyboard',
 'Compact TKL mechanical keyboard with RGB backlighting and tactile blue switches.',
 5800.00, 15, 'https://images.unsplash.com/photo-1541140532154-b24cfe67fb84?w=400'),

(1, 'Portable Power Bank 20000mAh',
 'High-capacity power bank with dual USB ports and a USB-C input for fast recharging.',
 3200.00, 30, 'https://images.unsplash.com/photo-1616763355548-1b606f439f86?w=400'),

(2, 'Classic Crew-Neck T-Shirt',
 'Soft 100% cotton unisex t-shirt available in multiple colors. Machine washable.',
 850.00, 100, 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=400'),

(2, 'Slim-Fit Chino Trousers',
 'Comfortable slim-fit chino trousers suitable for casual and semi-formal occasions.',
 2100.00, 50, 'https://images.unsplash.com/photo-1624378439575-d8705ad7ae80?w=400'),

(3, 'Introduction to Algorithms (3rd Ed)',
 'Comprehensive textbook covering algorithms, data structures, and complexity analysis.',
 4500.00, 12, 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=400'),

(3, 'Clean Code by Robert C. Martin',
 'A guide to writing readable, maintainable, and professional code.',
 2800.00, 20, 'https://images.unsplash.com/photo-1532012197267-da84d127e765?w=400'),

(4, 'Stainless Steel Water Bottle 1L',
 'Double-walled insulated water bottle that keeps drinks cold for 24 hours and hot for 12 hours.',
 1400.00, 60, 'https://images.unsplash.com/photo-1602143407151-7111542de6e8?w=400'),

(4, 'Non-Stick Frying Pan 28cm',
 'Heavy-gauge aluminium non-stick frying pan suitable for all hob types including induction.',
 2600.00, 35, 'https://images.unsplash.com/photo-1556909114-44e3e7a61b5c?w=400'),

(5, 'Adjustable Dumbbell Set 20kg',
 'Space-saving adjustable dumbbells with cast-iron plates and chrome bar. Ideal for home workouts.',
 6500.00, 8, 'https://images.unsplash.com/photo-1576678927484-cc907957088c?w=400'),

(5, 'Yoga Mat with Carry Strap',
 'Thick non-slip yoga mat made from eco-friendly TPE material. Includes a carry strap.',
 1800.00, 45, 'https://images.unsplash.com/photo-1601925228847-4c87e2dcb5e9?w=400');
