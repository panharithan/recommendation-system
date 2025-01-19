<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'user_management');
$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['resend_email'])) {
    $email = $conn->real_escape_string($_POST['reference_email']);
    $token = bin2hex(random_bytes(16));

    // Update token in the database
    $query = "UPDATE invitations SET token = ?, sent_at = NOW() WHERE user_id = ? AND reference_email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sis", $token, $userId, $email);
    $stmt->execute();

    // Resend email
    $link = "http://yourwebsite.com/reference_form.php?token=" . $token;
    mail($email, "Recommendation Letter Invitation", "Please complete the form: $link");

    $message = "Invitation resent to $email.";
}

// Fetch invitations
$query = "SELECT reference_email, status, sent_at FROM invitations WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Invitations</title>
</head>
<body>
    <h2>Manage Invitations</h2>
    <?php if (isset($message)) echo "<p>$message</p>"; ?>
    <table border="1">
        <tr>
            <th>Reference Email</th>
            <th>Status</th>
            <th>Sent At</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['reference_email']) ?></td>
            <td><?= htmlspecialchars($row['status']) ?></td>
            <td><?= htmlspecialchars($row['sent_at']) ?></td>
            <td>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="reference_email" value="<?= htmlspecialchars($row['reference_email']) ?>">
                    <button type="submit" name="resend_email">Resend Email</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>