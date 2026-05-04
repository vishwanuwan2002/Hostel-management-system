<?php
// This file serves the Google Client ID to the frontend
// It prevents exposing credentials in the HTML source

header('Content-Type: application/javascript');
require_once 'config-google.php';

echo "window.GOOGLE_CLIENT_ID = '" . GOOGLE_CLIENT_ID . "';";
?>
