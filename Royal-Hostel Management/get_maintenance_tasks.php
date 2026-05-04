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
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Fetch all tasks for the admin dashboard and public view
$sql = "SELECT request_id as id, category, room_number, description, priority, scheduled_date, status, assigned_to, created_at FROM maintenance_requests ORDER BY created_at DESC";
$result = $conn->query($sql);

$tasks = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Format date if it exists
        $date = $row['scheduled_date'] ? date('M d, Y', strtotime($row['scheduled_date'])) : 'Pending';
        
        $tasks[] = [
            'id' => $row['id'],
            'category' => ucfirst($row['category']),
            'description' => $row['description'],
            'room_number' => $row['room_number'],
            'priority' => $row['priority'],
            'scheduled_date' => $row['scheduled_date'], // Raw date for admin
            'formatted_date' => $date, // Formatted for display
            'status' => $row['status'],
            'assigned_to' => $row['assigned_to'],
            'created_at' => $row['created_at'],
            
            // Keep these for backward compatibility with hostel-maintenance.html if needed, 
            // or we can update hostel-maintenance.html to use the new keys.
            // Let's check hostel-maintenance.html usage.
            'task' => ucfirst($row['category']), 
            'location' => 'Room ' . $row['room_number'],
            'date' => $date
        ];
    }
}

echo json_encode($tasks);

$conn->close();
?>