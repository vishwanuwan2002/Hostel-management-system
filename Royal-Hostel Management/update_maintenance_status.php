<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hostel_system";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Connection failed']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['id']) || !isset($input['status'])) {
    echo json_encode(['success' => false, 'message' => 'Missing parameters']);
    exit;
}

$id = $input['id'];
$statusRaw = $input['status'];

// Map status
$statusMap = [
    'pending' => 'Pending',
    'inprogress' => 'In Progress',
    'completed' => 'Completed',
    'rejected' => 'Rejected'
];

$dbStatus = isset($statusMap[$statusRaw]) ? $statusMap[$statusRaw] : 'Pending';

// Update query
$stmt = $conn->prepare("UPDATE maintenance_requests SET status = ? WHERE request_id = ?");
$stmt->bind_param("si", $dbStatus, $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => $conn->error]);
}

$conn->close();
?>