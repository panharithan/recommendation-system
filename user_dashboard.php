<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

require 'send_email.php'; // Include the email-sending script

$conn = new mysqli('localhost', 'root', '', 'user_management');

// Define the number of records per page
$records_per_page = 5;

// Determine the current page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1); // Ensure the page is at least 1

// Calculate the offset for the SQL query
$offset = ($page - 1) * $records_per_page;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $token = bin2hex(random_bytes(16)); // Generate unique token

    $userQuery = $conn->prepare("SELECT id, username, email FROM users WHERE username = ?");
    $userQuery->bind_param("s", $_SESSION['username']);
    $userQuery->execute();
    $userResult = $userQuery->get_result();
    $user = $userResult->fetch_assoc();

    $insertQuery = $conn->prepare("INSERT INTO recommendation_requests (user_id, email, token) VALUES (?, ?, ?)");
    $insertQuery->bind_param("iss", $user['id'], $email, $token);

    if ($insertQuery->execute()) {
        // Call the function from send_email.php
        $feedback = sendRecommendationEmail($email, $token, $user['username'], $user['email']);
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
$requestQuery = $conn->prepare("SELECT email, status, created_at FROM recommendation_requests WHERE user_id = ? LIMIT ?, ?");
$requestQuery->bind_param("iii", $user['id'], $offset, $records_per_page);
$requestQuery->execute();
$requests = $requestQuery->get_result();

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

<?php include 'footer.php'; ?>