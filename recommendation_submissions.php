<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'user_management');

// Pagination settings
$limit = 5;  // Number of records per page

// Get the current page number
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Recommendation Submissions pagination
$submissionQuery = "SELECT rr.id, u.username, rr.email, rr.status, rs.file_path, rs.submitted_at 
                    FROM recommendation_requests rr
                    LEFT JOIN recommendation_submissions rs ON rr.id = rs.request_id
                    JOIN users u ON rr.user_id = u.id
                    LIMIT $limit OFFSET $offset";
$submissions = $conn->query($submissionQuery);

// Get total number of submissions for pagination
$totalSubmissionsQuery = "SELECT COUNT(*) AS total FROM recommendation_requests";
$totalSubmissionsResult = $conn->query($totalSubmissionsQuery);
$totalSubmissions = $totalSubmissionsResult->fetch_assoc()['total'];
$totalPagesSubmissions = ceil($totalSubmissions / $limit);

$title = "Recommendation Submissions"; // Dynamic title for the page
include 'header.php'; // Include the header
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary">Recommendation Submissions</h2>
        <a href="admin_dashboard.php" class="btn btn-secondary btn-sm">Back to Dashboard</a>
    </div>

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

    <!-- Pagination for Recommendation Submissions -->
    <nav>
        <ul class="pagination justify-content-center">
            <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                <a class="page-link" href="recommendation_submissions.php?page=<?= $page - 1 ?>" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
            <?php for ($i = 1; $i <= $totalPagesSubmissions; $i++): ?>
                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                    <a class="page-link" href="recommendation_submissions.php?page=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
            <li class="page-item <?= ($page >= $totalPagesSubmissions) ? 'disabled' : '' ?>">
                <a class="page-link" href="recommendation_submissions.php?page=<?= $page + 1 ?>" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        </ul>
    </nav>
</div>

<?php
include 'footer.php'; // Include the footer
?>