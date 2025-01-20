<?php
session_start();

// Ensure the user is logged in before proceeding
if (!isset($_SESSION['username'])) {
    // Redirect to login page if not logged in
    header('Location: login.php');
    exit();
}

// Get the requested file from the URL
$file = $_GET['file'] ?? null;

// Ensure a valid file is provided
if (!$file || !preg_match('/^[a-zA-Z0-9_\-\.]+$/', $file)) {
    die("Invalid file.");
}

// Set the path to the uploads folder
$filePath = 'uploads/' . $file;

// Check if the file exists
if (file_exists($filePath)) {
    // Set the appropriate headers for downloading or viewing the file
    header('Content-Type: ' . mime_content_type($filePath));
    header('Content-Disposition: inline; filename="' . basename($filePath) . '"');
    header('Content-Length: ' . filesize($filePath));

    // Output the file contents
    readfile($filePath);
    exit();
} else {
    die("File not found.");
}