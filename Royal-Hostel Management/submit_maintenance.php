<?php
header('Content-Type: application/json');

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hostel_system";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $room = $_POST['room'] ?? '';
    $category = $_POST['category'] ?? '';
    $priority = $_POST['priority'] ?? '';
    $description = $_POST['description'] ?? '';

    // Basic validation
    if (empty($name) || empty($email) || empty($category) || empty($priority) || empty($description)) {
        echo json_encode(['success' => false, 'message' => 'Please fill in all required fields.']);
        exit;
    }

    // Calculate scheduled date based on priority
    $scheduled_date = date('Y-m-d'); // Default to today
    if ($priority === 'low') {
        $scheduled_date = date('Y-m-d', strtotime('+5 days'));
    } elseif ($priority === 'medium') {
        $scheduled_date = date('Y-m-d', strtotime('+2 days'));
    } elseif ($priority === 'high') {
        $scheduled_date = date('Y-m-d', strtotime('+1 days'));
    }
    // emergency is today

    // Prepare SQL statement
    $stmt = $conn->prepare("INSERT INTO maintenance_requests (name, email, phone, room_number, category, priority, description, scheduled_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    
    if ($stmt) {
        $stmt->bind_param("ssssssss", $name, $email, $phone, $room, $category, $priority, $description, $scheduled_date);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Maintenance request submitted successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error submitting request: ' . $stmt->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}

$conn->close();
?>