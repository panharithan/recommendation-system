<?php
session_start();
include 'header.php';  // Include the header

$conn = new mysqli('localhost', 'root', '', 'user_management');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Process login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login']);
    $password = $_POST['password'];

    // Check for username or email
    $query = "SELECT * FROM users WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $login, $login);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            // Store session data
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on role
            if ($user['role'] === 'admin') {
                header('Location: admin_dashboard.php');
            } else {
                header('Location: user_dashboard.php');
            }
            exit();
        } else {
            // Redirect with error message
            header('Location: login.php?error=Invalid password');
            exit();
        }
    } else {
        // Redirect with error message
        header('Location: login.php?error=User not found');
        exit();
    }

    $stmt->close();
}

$conn->close();
?>

<div class="login-container">
    <h2>Login</h2>
    
    <?php
    // Display error message if it exists
    if (isset($_GET['error'])) {
        echo "<div class='alert alert-danger'>{$_GET['error']}</div>";
    }
    ?>
    
    <form action="login.php" method="POST">
        <div class="mb-3">
            <label for="login" class="form-label">Username or Email:</label>
            <input type="text" id="login" name="login" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password:</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary w-100">Login</button>
    </form>

    <!-- Link to Registration page -->
    <div class="mt-3 text-center">
        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>
</div>

<?php include 'footer.php'; // Include the footer ?>