<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    die("You must be logged in to access this file.");
}

// Retrieve the file path from the URL parameter
$file = $_GET['file']; // Example: /uploads/filename.jpg

// Make sure the file exists in the uploads folder
$filePath = 'uploads/' . basename($file);
if (file_exists($filePath)) {
    // Send the file to the browser
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
    header('Content-Length: ' . filesize($filePath));
    readfile($filePath);
    exit;
} else {
    die("File not found.");
}
?>