<?php
session_start();
if (!isset($_SESSION['role'])) {
    header('Location: login.php');
    exit();
}
// Include the email sending function
require 'send_email.php';

if (isset($_GET['id'])) {
    $submissionId = $_GET['id'];

    // Connect to the database
    $conn = new mysqli('localhost', 'root', '', 'user_management');

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check the status of the submission
    $query = "SELECT status, u.username, u.email 
              FROM recommendation_requests rr
              JOIN users u ON rr.user_id = u.id
              WHERE rr.id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $submissionId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    // If the status is pending, update it or perform resend action
    if ($row && $row['status'] === 'pending') {
        // Example: Update status to "resending"
        $updateQuery = "UPDATE recommendation_requests SET status = 'resending' WHERE id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("i", $submissionId);
        $updateStmt->execute();

        // Generate a unique token for the link (you could customize this logic)
        $token = bin2hex(random_bytes(16));

        // Send the email (call the email function)
        $emailResult = sendRecommendationEmail($row['email'], $token, $row['username'], $row['email']);

        // You may want to log or use this message elsewhere in your system
        echo $emailResult;  // Just for debugging, remove in production

        // Redirect back to the submissions page
        header('Location: recommendation_submissions.php?message=ResendSuccessful');
        exit();
    } else {
        // If status is not pending, redirect back with an error message
        header('Location: recommendation_submissions.php?error=NotPending');
        exit();
    }
}
?>