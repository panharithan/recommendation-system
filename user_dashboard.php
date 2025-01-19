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

    $insertQuery = $conn->prepare("INSERT INTO recommendation_requests (user_id, email, token) VALUES (?, ?, ?)");
    $insertQuery->bind_param("iss", $user['id'], $email, $token);

    if ($insertQuery->execute()) {
        $subject = "Recommendation Request";
        $message = "Click the link to respond:\n\nhttp://example.com/recommendation.php?token=$token\n\n";
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

// Fetch the logged-in user's ID
$userQuery = $conn->prepare("SELECT id FROM users WHERE username = ?");
$userQuery->bind_param("s", $_SESSION['username']);
$userQuery->execute();
$userResult = $userQuery->get_result();
$user = $userResult->fetch_assoc();

// Fetch recommendation requests for the logged-in user
$requestQuery = $conn->prepare("SELECT email, status, created_at FROM recommendation_requests WHERE user_id = ?");
$requestQuery->bind_param("i", $user['id']);
$requestQuery->execute();
$requests = $requestQuery->get_result();
?>

<?php
$title = "Manage Invitations";
include 'header.php'; // Include the header
?>

<h2>Welcome, <?= htmlspecialchars($_SESSION['username']); ?>!</h2>
<a href="logout.php" class="btn btn-danger mb-3">Logout</a>

<h3>Request a Recommendation Letter</h3>
<?php if (isset($feedback)) echo "<div class='alert alert-info'>$feedback</div>"; ?>
<form action="" method="POST" class="mb-4">
    <div class="mb-3">
        <label for="email" class="form-label">Enter Email of Reference Letter Writer:</label>
        <input type="email" id="email" name="email" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">Send Invitation</button>
</form>

<h3>Your Recommendation Requests</h3>
<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>Email</th>
            <th>Status</th>
            <th>Requested At</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $requests->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['email']); ?></td>
            <td><?= htmlspecialchars($row['status']); ?></td>
            <td><?= htmlspecialchars($row['created_at']); ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php include 'footer.php'; ?>