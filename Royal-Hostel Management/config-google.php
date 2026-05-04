<?php
/**
 * Google OAuth Configuration
 * 
 * Update these values with your Google Cloud Project credentials
 * Get your Client ID from: https://console.cloud.google.com/apis/credentials
 */

// Your Google OAuth Client ID
// Get this from Google Cloud Console > APIs & Services > Credentials
define('GOOGLE_CLIENT_ID', '638455154555-u1m9k5f3h8q7j2l9p4c6r8t0v2w4y6z8.apps.googleusercontent.com');

// Your Google OAuth Client Secret (keep this private!)
// Store this securely, not in version control
define('GOOGLE_CLIENT_SECRET', 'GOCSPX-xxxxxxxxxxxxxxxxxx');

// Your application's redirect URI
// Must match authorized redirect URIs in Google Cloud Console
define('REDIRECT_URI', 'http://localhost/process-google-login.php');

// Database configuration
define('DB_PATH', __DIR__ . '/data/users.sqlite');

?>
