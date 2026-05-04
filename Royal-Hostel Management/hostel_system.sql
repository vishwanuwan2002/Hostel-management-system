-- --------------------------------------------------------
-- Database: `hostel_system`
-- --------------------------------------------------------

-- 1. Drop the database if it exists (for clean re-installation)
DROP DATABASE IF EXISTS hostel_system;

-- 2. Create the new database
CREATE DATABASE hostel_system;
USE hostel_system;

-- --------------------------------------------------------
-- Table structure for table `rooms`
-- Stores static room details and current availability status.
-- --------------------------------------------------------
CREATE TABLE rooms (
    room_id INT AUTO_INCREMENT PRIMARY KEY,
    room_number INT NOT NULL UNIQUE COMMENT 'The visible room number (e.g., 101, 204).',
    room_type VARCHAR(50) NOT NULL COMMENT 'Type of room (e.g., Private).',
    price_per_month DECIMAL(10, 2) NOT NULL COMMENT 'Price in Rs. (e.g., 12000.00).',
    current_status ENUM('available', 'booked', 'cleaning') NOT NULL DEFAULT 'available' COMMENT 'The room''s current state.'
);

-- --------------------------------------------------------
-- Inserting initial data into `rooms` table
-- --------------------------------------------------------
INSERT INTO rooms (room_number, room_type, price_per_month, current_status) VALUES
(101, 'Private', 12000.00, 'available'),
(102, 'Private', 12000.00, 'booked'),
(103, 'Private', 12000.00, 'available'),
(104, 'Private', 12000.00, 'cleaning'),
(105, 'Private', 12000.00, 'available'),
(106, 'Private', 12000.00, 'available'),
(201, 'Private', 12000.00, 'available'),
(202, 'Private', 12000.00, 'available'),
(203, 'Private', 12000.00, 'booked'),
(204, 'Private', 12000.00, 'available');


-- --------------------------------------------------------
-- Table structure for table `bookings`
-- Stores the student's booking details collected from the form.
-- --------------------------------------------------------
CREATE TABLE bookings (
    booking_id INT AUTO_INCREMENT PRIMARY KEY,
    room_id INT NOT NULL COMMENT 'Foreign Key linking to the rooms table.',
    -- room_number INT NOT NULL COMMENT 'Added for easier viewing/searching.',
    full_name VARCHAR(100) NOT NULL,
    reg_number VARCHAR(50) NOT NULL UNIQUE COMMENT 'University registration number.',
    uni_email VARCHAR(100) NOT NULL UNIQUE COMMENT 'University email address.',
    academic_batch VARCHAR(20) NOT NULL COMMENT 'Academic batch/year (e.g., 2025A).',
    whatsapp_number VARCHAR(15) NOT NULL COMMENT '10-digit WhatsApp contact number.',
    current_address VARCHAR(255) NOT NULL,
    check_in_date DATE NOT NULL,
    check_out_date DATE NOT NULL,
    special_requests TEXT NULL,
    booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Date and time the booking was submitted.',
    payment_status ENUM('pending', 'paid', 'failed') NOT NULL DEFAULT 'pending' COMMENT 'Current status of the payment.',
    
    -- Define the Foreign Key constraint
    FOREIGN KEY (room_id) REFERENCES rooms(room_id)
);


--login table

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    phone_number VARCHAR(20) NULL,
    password_hash VARCHAR(255) NOT NULL, -- Stores the secure hashed password
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- --------------------------------------------------------
-- Table structure for table `maintenance_requests`
-- Stores maintenance issues reported by users.
-- --------------------------------------------------------
CREATE TABLE maintenance_requests (
    request_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    room_number VARCHAR(20),
    category VARCHAR(50) NOT NULL,
    priority VARCHAR(20) NOT NULL,
    description TEXT NOT NULL,
    status ENUM('Pending', 'In Progress', 'Completed', 'Scheduled') DEFAULT 'Pending',
    assigned_to VARCHAR(100) DEFAULT 'Unassigned',
    scheduled_date DATE DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- --------------------------------------------------------
-- Inserting initial dummy data into `maintenance_requests`
-- --------------------------------------------------------
INSERT INTO maintenance_requests (name, email, phone, room_number, category, priority, description, status, assigned_to, scheduled_date) VALUES
('John Doe', 'john@example.com', '0771234567', '101', 'plumbing', 'high', 'Leaking tap in bathroom', 'Scheduled', 'Mr. Perera', '2023-11-25'),
('Jane Smith', 'jane@example.com', '0777654321', '204', 'electrical', 'medium', 'Light bulb flickering', 'Pending', 'Unassigned', NULL),
('Sam Wilson', 'sam@example.com', '0712345678', '105', 'hvac', 'low', 'AC remote not working', 'In Progress', 'Technician A', '2023-11-26');


