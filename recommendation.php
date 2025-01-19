<?php
$conn = new mysqli('localhost', 'root', '', 'user_management');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$message = "";
$formVisible = true;

// Process the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'];
    $relationship = trim($_POST['relationship']);
    $comments = trim($_POST['comments']);
    $file = $_FILES['recommendation_file'];

    // Validate token and check status
    $query = "SELECT id, status FROM recommendation_requests WHERE token = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    $request = $result->fetch_assoc();

    if ($request) {
        if ($request['status'] === 'completed') {
            $message = "This recommendation has already been submitted successfully.";
            $formVisible = false;
        } else {
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
                                  VALUES (?, ?, ?, ?)";
                    $infoStmt = $conn->prepare($infoQuery);
                    $infoStmt->bind_param("isss", $request['id'], $relationship, $comments, $fileDestination);
                    $infoStmt->execute();

                    $message = "Thank you! The recommendation request has been completed successfully.";
                    $formVisible = false;
                } else {
                    $message = "File upload failed. Please try again.";
                }
            } else {
                $message = "Invalid file. Please upload a valid PDF, DOC, or DOCX file under 2 MB.";
            }
        }
    } else {
        $message = "Invalid or expired token.";
        $formVisible = false;
    }
}

// If accessed via token
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Check if the token is valid and fetch status
    $query = "SELECT id, status FROM recommendation_requests WHERE token = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    $request = $result->fetch_assoc();

    if ($request) {
        if ($request['status'] === 'completed') {
            $message = "This recommendation has already been submitted successfully.";
            $formVisible = false;
        } else {
            $formVisible = true;
        }
    } else {
        $message = "Invalid or expired token.";
        $formVisible = false;
    }
} else {
    $message = "No token provided.";
    $formVisible = false;
}

$conn->close();
?>

<?php include 'header.php'; // Include the header ?>

<?php if ($message): ?>
    <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<?php if ($formVisible): ?>
    <h2>Submit Your Recommendation</h2>
    <form action="" method="POST" enctype="multipart/form-data" class="mt-3">
        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
        
        <div class="mb-3">
            <label for="relationship" class="form-label">Relationship with Student:</label>
            <input type="text" id="relationship" name="relationship" class="form-control" required>
        </div>
        
        <div class="mb-3">
            <label for="comments" class="form-label">Comments:</label>
            <textarea id="comments" name="comments" class="form-control" rows="5" required></textarea>
        </div>
        
        <div class="mb-3">
            <label for="recommendation_file" class="form-label">Upload Recommendation Letter (PDF, DOC, DOCX only, Max: 2MB):</label>
            <input type="file" id="recommendation_file" name="recommendation_file" class="form-control" required>
        </div>
        
        <button type="submit" class="btn btn-primary">Submit Recommendation</button>
    </form>
<?php endif; ?>

<?php include 'footer.php'; // Include the footer ?>