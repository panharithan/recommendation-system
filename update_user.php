<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'user_management');

if (isset($_GET['id'])) {
    $userId = $_GET['id'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $role = $_POST['role'];

        $updateQuery = "UPDATE users SET username = ?, email = ?, role = ? WHERE id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("sssi", $username, $email, $role, $userId);

        if ($stmt->execute()) {
            header('Location: admin_dashboard.php');
            exit();
        } else {
            echo "Failed to update user.";
        }
    }

    $query = "SELECT username, email, role FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
    } else {
        echo "User not found.";
        exit();
    }
} else {
    echo "No user ID specified.";
    exit();
}
?>

<?php include 'header.php'; // Include the header ?>

<div class="container mt-4">
    <h2>Update User</h2>
    <form method="POST">
        <div class="mb-3">
            <label for="username" class="form-label">Username:</label>
            <input type="text" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email:</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="role" class="form-label">Role:</label>
            <select id="role" name="role" class="form-select" required>
                <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User</option>
                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
    </form>

    <a href="admin_dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
</div>

<?php include 'footer.php'; // Include the footer ?>