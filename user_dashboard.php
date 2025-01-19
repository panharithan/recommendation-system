<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'user_management');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $token = bin2hex(random_bytes(16)); // Generate unique token

    $userQuery = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $userQuery->bind_param("s", $_SESSION['username']);
    $userQuery->execute();
    $userResult = $userQuery->get_result();
    $user = $userResult->fetch_assoc();

    // Insert recommendation request
    $insertQuery = $conn->prepare("INSERT INTO recommendation_requests (user_id, email, token) VALUES (?, ?, ?)");
    $insertQuery->bind_param("iss", $user['id'], $email, $token);

    if ($insertQuery->execute()) {
        // Send Email
        $subject = "Recommendation Request";
        $message = "You have been requested to write a recommendation letter. Click the link below to respond:\n\n";
        $message .= "http://example.com/recommendation.php?token=$token\n\n";
        $headers = "From: noreply@example.com";

        if (mail($email, $subject, $message, $headers)) {
            $feedback = "Invitation sent successfully!";
        } else {
            $feedback = "Failed to send email.";
        }
    } else {
        $feedback = "Error: Could not save the request.";
    }
}

// Fetch recommendation requests for the logged-in user
$userQuery = $conn->prepare("SELECT id FROM users WHERE username = ?");
$userQuery->bind_param("s", $_SESSION['username']);
$userQuery->execute();
$userResult = $userQuery->get_result();
$user = $userResult->fetch_assoc();

$requestQuery = $conn->prepare("SELECT email, token, status, created_at FROM recommendation_requests WHERE user_id = ?");
$requestQuery->bind_param("i", $user['id']);
$requestQuery->execute();
$requests = $requestQuery->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
</head>
<body>
    <h2>Welcome, <?= htmlspecialchars($_SESSION['username']); ?>!</h2>
    <a href="logout.php">Logout</a>

    <!-- User Quick Links -->
    <h3>User Quick Links</h3>
    <nav>
        <a href="profile.php">View/Update Profile</a> |
        <a href="reference_status.php">Check Reference Status</a> |
        <a href="manage_invitations.php">Manage Invitations</a>
    </nav>

    <!-- Request a Recommendation -->
    <h3>Request a Recommendation Letter</h3>
    <?php if (isset($feedback)) echo "<p>$feedback</p>"; ?>
    <form action="" method="POST">
        <label for="email">Enter Email of Reference Letter Writer:</label>
        <input type="email" id="email" name="email" required>
        <button type="submit">Send Invitation</button>
    </form>

    <!-- Recommendation Requests -->
    <h3>Your Recommendation Requests</h3>
    <table border="1">
        <tr>
            <th>Email</th>
            <th>Status</th>
            <th>Token</th>
            <th>Requested At</th>
        </tr>
        <?php while ($row = $requests->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['email']); ?></td>
            <td><?= htmlspecialchars($row['status']); ?></td>
            <td><?= htmlspecialchars($row['token']); ?></td>
            <td><?= htmlspecialchars($row['created_at']); ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>