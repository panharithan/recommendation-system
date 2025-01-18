<?php
$conn = new mysqli('localhost', 'root', '', 'user_management');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$message = "";

// Process the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'];
    $relationship = trim($_POST['relationship']);
    $comments = trim($_POST['comments']);
    $file = $_FILES['recommendation_file'];

    // Validate token
    $query = "SELECT id FROM recommendation_requests WHERE token = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        // Validate file upload
        $allowedFileTypes = ['pdf', 'doc', 'docx'];
        $maxFileSize = 2 * 1024 * 1024; // 2 MB

        $fileName = $file['name'];
        $fileTmpName = $file['tmp_name'];
        $fileSize = $file['size'];
        $fileError = $file['error'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if ($fileError === 0 && in_array($fileExtension, $allowedFileTypes) && $fileSize <= $maxFileSize) {
            $uploadDir = 'uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true); // Create uploads directory if it doesn't exist
            }

            $uniqueFileName = uniqid('', true) . '.' . $fileExtension;
            $fileDestination = $uploadDir . $uniqueFileName;

            if (move_uploaded_file($fileTmpName, $fileDestination)) {
                // Update database
                $updateQuery = "UPDATE recommendation_requests SET status = 'completed' WHERE token = ?";
                $updateStmt = $conn->prepare($updateQuery);
                $updateStmt->bind_param("s", $token);
                $updateStmt->execute();

                // Save additional information
                $infoQuery = "INSERT INTO recommendation_submissions (request_id, relationship, comments, file_path) 
                              VALUES ((SELECT id FROM recommendation_requests WHERE token = ?), ?, ?, ?)";
                $infoStmt = $conn->prepare($infoQuery);
                $infoStmt->bind_param("ssss", $token, $relationship, $comments, $fileDestination);
                $infoStmt->execute();

                $message = "Thank you! The recommendation request has been completed successfully.";
            } else {
                $message = "File upload failed. Please try again.";
            }
        } else {
            $message = "Invalid file. Please upload a valid PDF, DOC, or DOCX file under 2 MB.";
        }
    } else {
        $message = "Invalid or expired token.";
    }
}

// If accessed via token
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Check if the token is valid
    $query = "SELECT id FROM recommendation_requests WHERE token = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $validToken = true;
    } else {
        $message = "Invalid or expired token.";
        $validToken = false;
    }
} else {
    $validToken = false;
    $message = "No token provided.";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recommendation Submission</title>
</head>
<body>
    <?php if ($validToken): ?>
    <h2>Submit Your Recommendation</h2>
    <?php if ($message): ?>
        <p><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>
    <form action="" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
        
        <label for="relationship">Relationship with Student:</label><br>
        <input type="text" id="relationship" name="relationship" required><br><br>
        
        <label for="comments">Comments:</label><br>
        <textarea id="comments" name="comments" rows="5" cols="40" required></textarea><br><br>
        
        <label for="recommendation_file">Upload Recommendation Letter (PDF, DOC, DOCX only, Max: 2MB):</label><br>
        <input type="file" id="recommendation_file" name="recommendation_file" required><br><br>
        
        <button type="submit">Submit Recommendation</button>
    </form>
    <?php else: ?>
        <p><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>
</body>
</html>