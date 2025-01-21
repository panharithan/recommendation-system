<?php
session_start();

// Ensure the user is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Check if the user ID is provided
if (!isset($_GET['id'])) {
    die("No user ID specified.");
}

// Include the database connection
include 'db_connect.php';

// Prepare the DELETE query
$userId = $_GET['id'];
$query = "DELETE FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);

// Execute the query and handle the result
if ($stmt->execute()) {
    header('Location: admin_dashboard.php?message=User+deleted+successfully');
} else {
    echo "Error deleting user: " . $conn->error;
}

// Close connections
$stmt->close();
$conn->close();
?>