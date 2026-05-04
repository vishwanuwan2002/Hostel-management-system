<?php
// get_room_status.php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hostel_system";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

$sql = "SELECT room_number, room_type, price_per_month, current_status FROM rooms ORDER BY room_number ASC";
$result = $conn->query($sql);

$rooms = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $rooms[] = [
            'number' => (int)$row['room_number'],
            'type' => $row['room_type'],
            'status' => $row['current_status'],
            'price' => (float)$row['price_per_month']
        ];
    }
}

echo json_encode($rooms);
$conn->close();
?>