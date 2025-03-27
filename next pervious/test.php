<?php
// Start the session
session_start();

// Generate a unique key (e.g., secure token)
$key = bin2hex(random_bytes(32)); // 64-character hex string

$_SESSION['test'] = "message form the session";
// Get current session data
$sessionData = $_SESSION;

// Serialize the session data (convert to string)
$serializedData = serialize($sessionData);

// Save to a file (store outside web root for security)
$storagePath = __DIR__ . '/session_storage/' . $key . '.sess';
file_put_contents($storagePath, $serializedData);

// Destroy the current session
session_destroy();

// Return the key to the user (e.g., via URL or cookie)
echo "Your session key: " . $key;
?>