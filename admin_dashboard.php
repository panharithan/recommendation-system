<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'user_management');

// Fetch all users
$userQuery = "SELECT id, username, email, role, created_at FROM users";
$users = $conn->query($userQuery);

// Fetch all recommendation submissions
$submissionQuery = "SELECT rr.id, u.username, rr.email, rr.status, rs.file_path, rs.submitted_at 
                    FROM recommendation_requests rr
                    LEFT JOIN recommendation_submissions rs ON rr.id = rs.request_id
                    JOIN users u ON rr.user_id = u.id";
$submissions = $conn->query($submissionQuery);
?>

<?php
$title = "Admin Dashboard"; // Dynamic title for the page
include 'header.php'; // Include the header
?>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-primary">Welcome, Admin</h2>
            <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
    </div>

        <!-- Registered Users -->
        <h3 class="text-secondary">Registered Users</h3>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Registered At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $users->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= $row['role'] ?></td>
                        <td><?= $row['created_at'] ?></td>
                        <td>
                            <a href="update_user.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="delete_user.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Recommendation Submissions -->
        <h3 class="text-secondary mt-5">Recommendation Submissions</h3>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Submission ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>File</th>
                        <th>Submitted At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $submissions->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['status']) ?></td>
                        <td>
                            <?php if ($row['file_path']): ?>
                                <a href="<?= $row['file_path'] ?>" download class="btn btn-sm btn-primary">Download</a>
                            <?php else: ?>
                                N/A
                            <?php endif; ?>
                        </td>
                        <td><?= $row['submitted_at'] ?: 'N/A' ?></td>
                        <td>
                            <a href="view_submission.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-info">View Details</a>
                            <a href="delete_submission.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this submission?')">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php
include 'footer.php'; // Include the footer
?>
