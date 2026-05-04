<?php
session_start();

// Database path
$db_path = __DIR__ . '/data/users.sqlite';

// Check if database exists and has users table
if (!file_exists($db_path)) {
    die(json_encode(['success' => false, 'message' => 'No users registered yet. Please register first!']));
}

try {
    $pdo = new PDO('sqlite:' . $db_path);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch (PDOException $e) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed!']));
}

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validation
    if (empty($email) || empty($password)) {
        die(json_encode(['success' => false, 'message' => 'Email and password are required!']));
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die(json_encode(['success' => false, 'message' => 'Invalid email format!']));
    }
    
    // Check if user exists
    $stmt = $pdo->prepare('SELECT id, full_name, email, password FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        die(json_encode(['success' => false, 'message' => 'User not registered, please register!']));
    }
    
    // Verify password
    if (!password_verify($password, $user['password'])) {
        die(json_encode(['success' => false, 'message' => 'Invalid password! Please try again.']));
    }
    
    // Set session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['full_name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['logged_in'] = true;
    
    die(json_encode(['success' => true, 'message' => 'Login successful! Redirecting...']));
}

die(json_encode(['success' => false, 'message' => 'Invalid request!']));
?>
