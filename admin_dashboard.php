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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
</head>
<body>
    <h2>Welcome, Admin</h2>
    <a href="logout.php">Logout</a>
    
    <h3>Registered Users</h3>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Role</th>
            <th>Registered At</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $users->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['username']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= $row['role'] ?></td>
            <td><?= $row['created_at'] ?></td>
            <td>
                <a href="update_user.php?id=<?= $row['id'] ?>">Edit</a> | 
                <a href="delete_user.php?id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <h3>Recommendation Submissions</h3>
    <table border="1">
        <tr>
            <th>Submission ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Status</th>
            <th>File</th>
            <th>Submitted At</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $submissions->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['username']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= htmlspecialchars($row['status']) ?></td>
            <td>
                <?php if ($row['file_path']): ?>
                    <a href="<?= $row['file_path'] ?>" download>Download</a>
                <?php else: ?>
                    N/A
                <?php endif; ?>
            </td>
            <td><?= $row['submitted_at'] ?: 'N/A' ?></td>
            <td>
                <a href="view_submission.php?id=<?= $row['id'] ?>">View Details</a>
            </td>
            <td>
                <a href="delete_submission.php?id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this submission?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>