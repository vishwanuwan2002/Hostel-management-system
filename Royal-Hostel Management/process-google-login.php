<?php
session_start();

// Load configuration
require_once 'config-google.php';

// Database path
$db_path = DB_PATH;

try {
    $pdo = new PDO('sqlite:' . $db_path);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch (PDOException $e) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed!']));
}

// Create google_accounts table if it doesn't exist
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS google_accounts (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        google_id TEXT NOT NULL UNIQUE,
        email TEXT NOT NULL,
        full_name TEXT,
        profile_picture TEXT,
        last_login DATETIME,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY(user_id) REFERENCES users(id)
    )");
} catch (PDOException $e) {
    // Table already exists
}

// Handle Google Login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (empty($input['token'])) {
        die(json_encode(['success' => false, 'message' => 'No token provided!']));
    }
    
    $token = $input['token'];
    
    // Verify and decode the JWT token
    $decoded = verifyGoogleToken($token);
    
    if (!$decoded) {
        die(json_encode(['success' => false, 'message' => 'Invalid or expired token!']));
    }
    
    $google_id = $decoded['sub'];
    $email = $decoded['email'];
    $full_name = $decoded['name'] ?? '';
    $profile_picture = $decoded['picture'] ?? '';
    
    // Validate email
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die(json_encode(['success' => false, 'message' => 'Invalid email from Google!']));
    }
    
    try {
        // Check if Google account exists
        $stmt = $pdo->prepare('SELECT user_id FROM google_accounts WHERE google_id = ?');
        $stmt->execute([$google_id]);
        $google_account = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($google_account) {
            // Existing Google account - update last login
            $user_id = $google_account['user_id'];
            
            $stmt = $pdo->prepare('UPDATE google_accounts SET last_login = CURRENT_TIMESTAMP WHERE google_id = ?');
            $stmt->execute([$google_id]);
        } else {
            // Check if user with this email exists
            $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                // Link Google account to existing user
                $user_id = $user['id'];
                
                $stmt = $pdo->prepare('INSERT INTO google_accounts (user_id, google_id, email, full_name, profile_picture, last_login) 
                                       VALUES (?, ?, ?, ?, ?, CURRENT_TIMESTAMP)');
                $stmt->execute([$user_id, $google_id, $email, $full_name, $profile_picture]);
            } else {
                // Create new user account
                $generated_password = bin2hex(random_bytes(16));
                $hashed_password = password_hash($generated_password, PASSWORD_BCRYPT);
                
                $stmt = $pdo->prepare('INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)');
                $stmt->execute([$full_name, $email, $hashed_password]);
                
                $user_id = $pdo->lastInsertId();
                
                // Link Google account to new user
                $stmt = $pdo->prepare('INSERT INTO google_accounts (user_id, google_id, email, full_name, profile_picture, last_login) 
                                       VALUES (?, ?, ?, ?, ?, CURRENT_TIMESTAMP)');
                $stmt->execute([$user_id, $google_id, $email, $full_name, $profile_picture]);
            }
        }
        
        // Get user details for session
        $stmt = $pdo->prepare('SELECT id, full_name, email FROM users WHERE id = ?');
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            // Set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['login_method'] = 'google';
            
            die(json_encode(['success' => true, 'message' => 'Login successful!']));
        } else {
            die(json_encode(['success' => false, 'message' => 'Failed to retrieve user information!']));
        }
        
    } catch (PDOException $e) {
        die(json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]));
    }
}

// Function to verify Google JWT token
function verifyGoogleToken($token) {
    // For development/testing: Decode without verification
    // WARNING: This is NOT secure for production! 
    // In production, use Google's official libraries or verify the signature properly
    
    $parts = explode('.', $token);
    if (count($parts) !== 3) {
        return false;
    }
    
    // Decode the payload
    $payload = json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true);
    
    if (!$payload) {
        return false;
    }
    
    // Verify audience (client ID)
    if (isset($payload['aud']) && $payload['aud'] !== GOOGLE_CLIENT_ID) {
        // Client ID might be in 'audPrincipalList' for mobile clients
        if (!isset($payload['audPrincipalList']) || !in_array(GOOGLE_CLIENT_ID, $payload['audPrincipalList'])) {
            return false;
        }
    }
    
    // Check if token is expired
    if (isset($payload['exp']) && $payload['exp'] < time()) {
        return false;
    }
    
    // Verify email is verified (email_verified should be true)
    if (!isset($payload['email_verified']) || $payload['email_verified'] !== true) {
        return false;
    }
    
    return $payload;
}

// If not POST request
die(json_encode(['success' => false, 'message' => 'Invalid request!']));
?>
