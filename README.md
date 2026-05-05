# Royal Hostel Management System

[![PHP](https://img.shields.io/badge/PHP-8.0-777BB4?logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.0-7952B3?logo=bootstrap&logoColor=white)](https://getbootstrap.com/)
[![jQuery](https://img.shields.io/badge/jQuery-3.0-0769AD?logo=jquery&logoColor=white)](https://jquery.com/)
[![License](https://img.shields.io/badge/license-All%20Rights%20Reserved-red)](LICENSE)

A comprehensive full-stack hostel management system built with PHP, HTML, CSS, and JavaScript, developed to digitize and simplify hostel operations for both residents and administrators.

## Features

- User authentication (login/register)
- Room booking and management
- Admin dashboard for overseeing operations
- Maintenance request tracking
- Payment processing
- Room availability management
- User profile management

## Project Structure

```
Royal-Hostel Management
├── admin-dashboard.html
├── admin-dashboard-login.html
├── admin-dashboard-register.html
├── booking.php
├── contact.html
├── gallery.html
├── hostel-maintenance.html
├── index.php
├── payment_page.html
├── user-login.html
├── user-register.html
├── css/
├── data/
├── fonts/
├── image/
├── inc/
├── js/
├── royal-Doc/
├── scss/
└── vendors/
```

## Setup

1. Clone or download this repository
2. Ensure you have a PHP server environment (XAMPP, WAMP, LAMP, etc.)
3. Place the `royal-` folder in your server's web root directory (e.g., `htdocs` for XAMPP)
4. Import the database schema from `hostel_system.sql` into your MySQL database
5. Configure database connection in the appropriate PHP files (look for database connection settings)
6. Start your Apache and MySQL services
7. Access the system via `http://localhost/royal-/index.php`

## Usage

### As a User:
1. Register for an account or log in
2. Browse available rooms and make bookings
3. Submit maintenance requests if needed
4. View your booking history and profile

### As an Admin:
1. Access the admin dashboard via `admin-dashboard.html`
2. Log in with admin credentials
3. Manage rooms, bookings, users, and maintenance requests
4. Generate reports and oversee system operations

## Database

The system uses a MySQL database. The initial schema can be found in `hostel_system.sql`.


## Acknowledgments

- Bootstrap for responsive design
- jQuery for JavaScript functionality
- PHPMailer for email handling (if applicable)
- All contributors who have helped shape this project

## License
Copyright (c) 2026 Vishwa Nuwan. All rights reserved. See [LICENSE](LICENSE) for details.
