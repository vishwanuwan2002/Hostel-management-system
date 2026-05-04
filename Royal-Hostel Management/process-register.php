<?php
// Database path and initialization
$db_path = __DIR__ . '/data/users.sqlite';
$db_dir = dirname($db_path);

// Create data directory if it doesn't exist
if (!is_dir($db_dir)) {
    mkdir($db_dir, 0755, true);
}

// Connect to SQLite database
try {
    $pdo = new PDO('sqlite:' . $db_path);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create users table if it doesn't exist
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        full_name TEXT NOT NULL,
        email TEXT UNIQUE NOT NULL,
        phone TEXT,
        password TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
    
} catch (PDOException $e) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]));
}

// Handle registration
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['full_name'])) {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validation
    if (empty($full_name) || empty($email) || empty($password)) {
        die(json_encode(['success' => false, 'message' => 'All required fields must be filled!']));
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die(json_encode(['success' => false, 'message' => 'Invalid email format!']));
    }
    
    if ($password !== $confirm_password) {
        die(json_encode(['success' => false, 'message' => 'Passwords do not match!']));
    }
    
    if (strlen($password) < 6) {
        die(json_encode(['success' => false, 'message' => 'Password must be at least 6 characters!']));
    }
    
    // Check if email already exists
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE email = ?');
    $stmt->execute([$email]);
    if ($stmt->fetchColumn() > 0) {
        die(json_encode(['success' => false, 'message' => 'Email already registered! Please login instead.']));
    }
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    
    // Insert user
    try {
        $stmt = $pdo->prepare('INSERT INTO users (full_name, email, phone, password) VALUES (?, ?, ?, ?)');
        $stmt->execute([$full_name, $email, $phone, $hashed_password]);
        
        die(json_encode(['success' => true, 'message' => 'Registration successful! Redirecting to login...']));
    } catch (PDOException $e) {
        die(json_encode(['success' => false, 'message' => 'Registration failed: ' . $e->getMessage()]));
    }
}

die(json_encode(['success' => false, 'message' => 'Invalid request!']));
?>
