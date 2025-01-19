<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'user_management');
$userId = $_SESSION['user_id'];

// Fetch reference submissions
$query = "SELECT reference_email, status, submitted_at FROM recommendation_submissions WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Reference Letter Status</title>
</head>
<body>
    <h2>Reference Letter Submissions</h2>
    <table border="1">
        <tr>
            <th>Reference Email</th>
            <th>Status</th>
            <th>Submitted At</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['reference_email']) ?></td>
            <td><?= htmlspecialchars($row['status']) ?></td>
            <td><?= $row['submitted_at'] ? htmlspecialchars($row['submitted_at']) : 'Not Submitted' ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>