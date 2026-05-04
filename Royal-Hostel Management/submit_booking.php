<?php
// submit_booking.php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hostel_system";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

// Get POST data
$roomNumber = $_POST['roomNumber'] ?? '';
$fullName = $_POST['fullName'] ?? '';
$regNumber = $_POST['regNumber'] ?? '';
$uniEmail = $_POST['uniEmail'] ?? '';
$academicBatch = $_POST['batch'] ?? '';
$whatsappNumber = $_POST['whatsappNumber'] ?? '';
$currentAddress = $_POST['address'] ?? '';
$checkInDate = $_POST['checkInDate'] ?? '';
$checkOutDate = $_POST['checkOutDate'] ?? '';
$specialRequests = $_POST['message'] ?? '';

if (empty($roomNumber) || empty($regNumber)) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields.']);
    exit;
}

try {
    $conn->begin_transaction();

    // Check availability
    $stmt = $conn->prepare("SELECT room_id FROM rooms WHERE room_number = ? AND current_status = 'available' FOR UPDATE");
    $stmt->bind_param("i", $roomNumber);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception("Room $roomNumber is no longer available.");
    }
    $row = $result->fetch_assoc();
    $room_id = $row['room_id'];
    $stmt->close();

    // Insert Booking
    $stmt = $conn->prepare("INSERT INTO bookings (room_id, full_name, reg_number, uni_email, academic_batch, whatsapp_number, current_address, check_in_date, check_out_date, special_requests) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssssssss", $room_id, $fullName, $regNumber, $uniEmail, $academicBatch, $whatsappNumber, $currentAddress, $checkInDate, $checkOutDate, $specialRequests);
    
    if (!$stmt->execute()) {
        throw new Exception("Database Error: " . $stmt->error);
    }
    $booking_id = $conn->insert_id;
    $stmt->close();

    // Update Room Status
    $stmt = $conn->prepare("UPDATE rooms SET current_status = 'booked' WHERE room_id = ?");
    $stmt->bind_param("i", $room_id);
    $stmt->execute();
    $stmt->close();

    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Booking confirmed!', 'booking_id' => $booking_id]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();
?>