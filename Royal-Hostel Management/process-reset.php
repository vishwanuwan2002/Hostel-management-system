<?php
session_start();

// Database path
$db_path = __DIR__ . '/data/users.sqlite';

try {
    $pdo = new PDO('sqlite:' . $db_path);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch (PDOException $e) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed!']));
}

// Check if reset_tokens table exists, if not create it
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS reset_tokens (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        token TEXT NOT NULL UNIQUE,
        expires_at DATETIME NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        used INTEGER DEFAULT 0,
        FOREIGN KEY(user_id) REFERENCES users(id)
    )");
} catch (PDOException $e) {
    // Table already exists
}

// Handle password reset request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    
    // REQUEST RESET TOKEN
    if ($_POST['action'] === 'request_reset') {
        $email = trim($_POST['email'] ?? '');
        
        if (empty($email)) {
            die(json_encode(['success' => false, 'message' => 'Email is required!']));
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            die(json_encode(['success' => false, 'message' => 'Invalid email format!']));
        }
        
        // Check if user exists
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            // For security, return generic message
            die(json_encode(['success' => true, 'message' => 'If an account exists with that email, you will receive reset instructions shortly.']));
        }
        
        // Generate reset token
        $token = bin2hex(random_bytes(32));
        $expires_at = date('Y-m-d H:i:s', strtotime('+24 hours'));
        
        // Invalidate existing tokens for this user
        $stmt = $pdo->prepare('UPDATE reset_tokens SET used = 1 WHERE user_id = ? AND used = 0');
        $stmt->execute([$user['id']]);
        
        // Store new token
        $stmt = $pdo->prepare('INSERT INTO reset_tokens (user_id, token, expires_at) VALUES (?, ?, ?)');
        $stmt->execute([$user['id'], $token, $expires_at]);
        
        // In a real application, you would send an email here with:
        // reset-password-form.html?token=TOKEN
        // For now, we'll store the token for demo purposes
        
        // For testing/development - log the token
        error_log("Reset token for $email: " . "reset-password-form.html?token=$token");
        
        // Send email (commented out - implement your own email service)
        /*
        $reset_link = "http://" . $_SERVER['HTTP_HOST'] . "/reset-password-form.html?token=$token";
        $subject = "Reset Your Password - StayEasy";
        $message = "Click the link below to reset your password:\n\n$reset_link\n\nThis link expires in 24 hours.\n\nIf you didn't request this, you can safely ignore this email.";
        
        mail($email, $subject, $message, "From: noreply@stayeasy.com");
        */
        
        die(json_encode(['success' => true, 'message' => 'If an account exists with that email, you will receive reset instructions shortly.']));
    }
    
    // RESET PASSWORD (using token)
    else if ($_POST['action'] === 'reset_password') {
        $token = trim($_POST['token'] ?? '');
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        if (empty($token)) {
            die(json_encode(['success' => false, 'message' => 'Invalid reset link!']));
        }
        
        if (empty($new_password) || empty($confirm_password)) {
            die(json_encode(['success' => false, 'message' => 'Password fields are required!']));
        }
        
        if ($new_password !== $confirm_password) {
            die(json_encode(['success' => false, 'message' => 'Passwords do not match!']));
        }
        
        if (strlen($new_password) < 6) {
            die(json_encode(['success' => false, 'message' => 'Password must be at least 6 characters!']));
        }
        
        // Verify token
        $stmt = $pdo->prepare('SELECT user_id, expires_at, used FROM reset_tokens WHERE token = ?');
        $stmt->execute([$token]);
        $reset_token = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$reset_token) {
            die(json_encode(['success' => false, 'message' => 'Invalid or expired reset link!']));
        }
        
        if ($reset_token['used'] == 1) {
            die(json_encode(['success' => false, 'message' => 'This reset link has already been used!']));
        }
        
        $expires_at = new DateTime($reset_token['expires_at']);
        $now = new DateTime();
        
        if ($now > $expires_at) {
            die(json_encode(['success' => false, 'message' => 'Reset link has expired! Please request a new one.']));
        }
        
        // Update password
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
        
        $stmt = $pdo->prepare('UPDATE users SET password = ? WHERE id = ?');
        $stmt->execute([$hashed_password, $reset_token['user_id']]);
        
        // Mark token as used
        $stmt = $pdo->prepare('UPDATE reset_tokens SET used = 1 WHERE token = ?');
        $stmt->execute([$token]);
        
        die(json_encode(['success' => true, 'message' => 'Password reset successfully! You can now login with your new password.']));
    }
}

// If not a POST request or no action
die(json_encode(['success' => false, 'message' => 'Invalid request!']));
?>
