<?php


$key = "f6c383c9c980b7bbcbf5132384dab92a3d5a38be3d707e8f859841e634e7fa64";
$storagePath = __DIR__ . '/session_storage/' . $key . '.sess';

// Check if the session file exists
if (!file_exists($storagePath)) {
    die("Invalid or expired session key.");
}

// Read and unserialize the stored data
$serializedData = file_get_contents($storagePath);
$sessionData = unserialize($serializedData);

// Start a new session and populate it
session_start();
$_SESSION = array_merge($_SESSION, $sessionData);

echo "Session restored!";
print_r($_SESSION);
?>