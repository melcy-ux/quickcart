# QuickCart — Online Shopping System

A full-stack e-commerce web application built with PHP and MySQL as part of the **BIT3208 Advanced Web Design and Development** unit.

## Project Overview

QuickCart is a simple online shopping system where customers can browse products, add items to their cart, and place orders. An admin panel allows the management of products and categories.

**Student:** Melcy Kate  
**Admission Number:** BSCCS/2024/53326  
**Unit:** BIT3208 — Advanced Web Design and Development  

---

## Features

- Product catalog with category filtering and search
- User registration and login with hashed passwords
- Session-based authentication with role management (customer / admin)
- Shopping cart with quantity controls
- Checkout and order placement
- Admin dashboard — product management, order overview

---

## Technology Stack

| Layer | Technology |
|---|---|
| Frontend | HTML5, CSS3, JavaScript (ES6), Bootstrap 5 |
| Backend | PHP 8.x |
| Database | MySQL 8.x |
| Local Server | XAMPP (Apache + MySQL) |
| Version Control | Git / GitHub |

---

## Project Structure

```
quickcart/
├── index.php            — Homepage with featured products
├── products.php         — Product catalog with filters
├── product.php          — Single product detail page
├── login.php            — Login form + PHP authentication
├── register.php         — Registration form with password hashing
├── cart.php             — Shopping cart (add, update, remove)
├── checkout.php         — Order placement form
├── logout.php           — Session destroy
├── includes/
│   ├── db.php           — Database connection (mysqli_connect)
│   ├── header.php       — Nav bar, session start, Bootstrap head
│   └── footer.php       — Footer HTML + JS includes
├── admin/
│   ├── index.php        — Admin dashboard (stats + recent orders)
│   └── products.php     — Add and delete products
├── assets/
│   ├── css/style.css    — Custom styles on top of Bootstrap
│   └── js/main.js       — Form validation + password strength checker
└── sql/
    └── quickcart.sql    — Full schema + sample data
```

---

## Setup Instructions

### Prerequisites
- [XAMPP](https://www.apachefriends.org/) installed (Apache + MySQL)
- A web browser

### Steps

1. **Clone the repository**
   ```
   git clone https://github.com/melcy-ux/quickcart.git
   ```
   Or download and extract the ZIP.

2. **Move to XAMPP htdocs**  
   Copy the `quickcart` folder into `C:\xampp\htdocs\`

3. **Start XAMPP**  
   Open XAMPP Control Panel and start **Apache** and **MySQL**.  
   If port 80 is in use, change Apache to port 8080 in `httpd.conf`.

4. **Import the database**  
   - Open phpMyAdmin: `http://localhost/phpmyadmin`
   - Click **SQL** and paste the contents of `sql/quickcart.sql`
   - Click **Go** to run — this creates the database and inserts sample data

5. **Open the site**  
   Navigate to `http://localhost/quickcart/`

### Default Login Credentials

| Role | Email | Password |
|---|---|---|
| Admin | admin@quickcart.com | Admin@123 |
| Customer | user@example.com | Test@1234 |

---

## Database Schema

**Database:** `quickcart_db`

| Table | Description |
|---|---|
| `users` | Registered users with hashed passwords and roles |
| `categories` | Product categories |
| `products` | Product listings with stock and category FK |
| `cart` | Per-user shopping cart items |
| `orders` | Placed orders with delivery details |
| `order_items` | Line items for each order |

---

## License

This project was created for educational purposes as part of BIT3208.
