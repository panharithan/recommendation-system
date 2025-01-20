<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

require 'send_email.php'; // Include the email-sending script

$conn = new mysqli('localhost', 'root', '', 'user_management');

// Check for a successful connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Define the number of records per page
$records_per_page = 5;

// Determine the current page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1); // Ensure the page is at least 1

// Calculate the offset for the SQL query
$offset = ($page - 1) * $records_per_page;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize email input
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    
    // Ensure the email is in a valid format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $feedback = "Invalid email format.";
    } else {
        // Generate a unique token
        $token = bin2hex(random_bytes(16)); // Generate unique token

        // Get user data securely using prepared statements
        $userQuery = $conn->prepare("SELECT id, username, email FROM users WHERE username = ?");
        $userQuery->bind_param("s", $_SESSION['username']);
        $userQuery->execute();
        $userResult = $userQuery->get_result();
        $user = $userResult->fetch_assoc();

        // Insert the recommendation request using prepared statements
        $insertQuery = $conn->prepare("INSERT INTO recommendation_requests (user_id, email, token) VALUES (?, ?, ?)");
        $insertQuery->bind_param("iss", $user['id'], $email, $token);

        if ($insertQuery->execute()) {
            // Call the function from send_email.php
            $feedback = sendRecommendationEmail($email, $token, $user['username'], $user['email']);

            // Redirect to refresh the page and clear the form data
            header("Location: ".$_SERVER['PHP_SELF']);
            exit();
        } else {
            $feedback = "Error: Could not save the request.";
        }
    }
}

// Fetch the logged-in user's ID
$userQuery = $conn->prepare("SELECT id FROM users WHERE username = ?");
$userQuery->bind_param("s", $_SESSION['username']);
$userQuery->execute();
$userResult = $userQuery->get_result();
$user = $userResult->fetch_assoc();

// Fetch the total number of recommendation requests
$totalQuery = $conn->prepare("SELECT COUNT(*) AS total FROM recommendation_requests WHERE user_id = ?");
$totalQuery->bind_param("i", $user['id']);
$totalQuery->execute();
$totalResult = $totalQuery->get_result();
$totalRow = $totalResult->fetch_assoc();
$total_records = $totalRow['total'];

// Calculate the total number of pages
$total_pages = ceil($total_records / $records_per_page);

// Fetch the recommendation requests with pagination
$requestQuery = $conn->prepare("SELECT id, email, status, created_at FROM recommendation_requests WHERE user_id = ? LIMIT ?, ?");
$requestQuery->bind_param("iii", $user['id'], $offset, $records_per_page);
$requestQuery->execute();
$requests = $requestQuery->get_result();

$title = "User Dashboard";
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

<!-- Refresh Button -->
<button onclick="window.location.reload();" class="btn btn-secondary">Refresh</button>

<h3>Your Recommendation Requests</h3>
<?php if (isset($_GET['message'])): ?>
    <div class="alert alert-success">
        <?= htmlspecialchars($_GET['message']); ?>
    </div>
<?php elseif (isset($_GET['error'])): ?>
    <div class="alert alert-danger">
        <?= htmlspecialchars($_GET['error']); ?>
    </div>
<?php endif; ?>

<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>Email</th>
            <th>Status</th>
            <th>Requested At</th>
            <th>Action</th> <!-- Column for Resend Button -->
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $requests->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['email']); ?></td>
            <td><?= htmlspecialchars($row['status']); ?></td>
            <td><?= htmlspecialchars($row['created_at']); ?></td>
            <td>
                <?php if ($row['status'] === 'pending'): ?>
                    <!-- Resend Button -->
                    <a href="resend_submission.php?id=<?= $row['id']; ?>" class="btn btn-warning btn-sm">Resend</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<!-- Pagination -->
<nav>
    <ul class="pagination">
        <?php if ($page > 1): ?>
            <li class="page-item"><a class="page-link" href="?page=<?= $page - 1; ?>">Previous</a></li>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <li class="page-item <?= $i == $page ? 'active' : ''; ?>">
                <a class="page-link" href="?page=<?= $i; ?>"><?= $i; ?></a>
            </li>
        <?php endfor; ?>

        <?php if ($page < $total_pages): ?>
            <li class="page-item"><a class="page-link" href="?page=<?= $page + 1; ?>">Next</a></li>
        <?php endif; ?>
    </ul>
</nav>

<?php include 'footer.php'; ?> <!-- Include the footer -->