<?php
session_start();

// Ensure the user is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Check if the submission ID is provided
if (!isset($_GET['id'])) {
    die("No submission ID specified.");
}

$conn = new mysqli('localhost', 'root', '', 'user_management');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve the file path for deletion
$submissionId = $_GET['id'];
$fileQuery = "SELECT file_path FROM recommendation_submissions WHERE id = ?";
$stmt = $conn->prepare($fileQuery);
$stmt->bind_param("i", $submissionId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $fileData = $result->fetch_assoc();
    $filePath = $fileData['file_path'];

    // Delete the file if it exists
    if ($filePath && file_exists($filePath)) {
        unlink($filePath);
    }

    // Delete the submission record from the database
    $deleteQuery = "DELETE FROM recommendation_submissions WHERE id = ?";
    $deleteStmt = $conn->prepare($deleteQuery);
    $deleteStmt->bind_param("i", $submissionId);

    if ($deleteStmt->execute()) {
        header('Location: admin_dashboard.php?message=Submission+deleted+successfully');
    } else {
        echo "Error deleting submission: " . $conn->error;
    }

    $deleteStmt->close();
} else {
    echo "Submission not found.";
}

// Close connections
$stmt->close();
$conn->close();
?>