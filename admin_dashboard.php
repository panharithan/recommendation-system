<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Include the database connection
include 'db_connect.php';

// Pagination settings
$limit = 5;  // Number of records per page

// Get the current page number
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Registered Users pagination - Use prepared statements
$userQuery = "SELECT id, username, email, role, created_at FROM users LIMIT ? OFFSET ?";
$stmt = $conn->prepare($userQuery);
$stmt->bind_param("ii", $limit, $offset);
$stmt->execute();
$users = $stmt->get_result();

// Get total number of users for pagination - Use prepared statements
$totalUsersQuery = "SELECT COUNT(*) AS total FROM users";
$totalUsersResult = $conn->query($totalUsersQuery);
$totalUsers = $totalUsersResult->fetch_assoc()['total'];
$totalPagesUsers = ceil($totalUsers / $limit);

// Recommendation Submissions pagination - Use prepared statements
$submissionQuery = "SELECT rr.id, u.username, rr.email, rr.status, rs.file_path, rs.submitted_at 
                    FROM recommendation_requests rr
                    LEFT JOIN recommendation_submissions rs ON rr.id = rs.request_id
                    JOIN users u ON rr.user_id = u.id
                    LIMIT ? OFFSET ?";
$stmt = $conn->prepare($submissionQuery);
$stmt->bind_param("ii", $limit, $offset);
$stmt->execute();
$submissions = $stmt->get_result();

// Get total number of submissions for pagination - Use prepared statements
$totalSubmissionsQuery = "SELECT COUNT(*) AS total FROM recommendation_requests";
$totalSubmissionsResult = $conn->query($totalSubmissionsQuery);
$totalSubmissions = $totalSubmissionsResult->fetch_assoc()['total'];
$totalPagesSubmissions = ceil($totalSubmissions / $limit);
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
<!-- Recommendation Submissions Link -->
<h3 class="text-secondary mt-5 mb-4">Recommendation Submissions</h3>
<a href="recommendation_submissions.php" class="btn btn-primary mb-3">View All Recommendation Submissions</a>
        <!-- Registered Users -->
        <h3 class="text-secondary mb-4">Registered Users</h3>
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

        <!-- Pagination for Registered Users -->
        <nav>
            <ul class="pagination justify-content-center">
                <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                    <a class="page-link" href="admin_dashboard.php?page=<?= $page - 1 ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
                <?php for ($i = 1; $i <= $totalPagesUsers; $i++): ?>
                    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                        <a class="page-link" href="admin_dashboard.php?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= ($page >= $totalPagesUsers) ? 'disabled' : '' ?>">
                    <a class="page-link" href="admin_dashboard.php?page=<?= $page + 1 ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>

<?php
include 'footer.php'; // Include the footer
?>