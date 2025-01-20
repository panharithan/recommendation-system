<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'user_management');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$message = "";

// Process the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Validate inputs (length and non-empty)
    if (empty($username) || empty($email) || empty($password)) {
        $message = "All fields are required.";
    } elseif (strlen($username) > 50) {
        $message = "Username must be less than 50 characters.";
    } elseif (strlen($email) > 100) {
        $message = "Email must be less than 100 characters.";
    } elseif (strlen($password) < 8 || strlen($password) > 20) {
        $message = "Password must be between 8 and 20 characters.";
    } else {
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert user into the database
        $query = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sss", $username, $email, $hashedPassword);

        if ($stmt->execute()) {
            $message = "Registration successful! <a href='login.php'>Login here</a>";
        } else {
            $message = "Error: " . $stmt->error;
        }

        $stmt->close();
    }
}

$conn->close();
?>

<?php include 'header.php'; // Include the header ?>

<div class="login-container">
    <h2>Register</h2>

    <!-- Display message if exists -->
    <?php if ($message): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form action="register.php" method="POST">
        <div class="mb-3">
            <label for="username" class="form-label">Username:</label>
            <input type="text" id="username" name="username" class="form-control" required maxlength="50">
            <!-- Set the maximum length of the username to 50 characters -->
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email:</label>
            <input type="email" id="email" name="email" class="form-control" required maxlength="100">
            <!-- Set the maximum length of the email to 100 characters -->
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password:</label>
            <input type="password" id="password" name="password" class="form-control" required minlength="8" maxlength="20">
            <!-- Set the minimum length to 8 and maximum length to 20 characters for the password -->
        </div>

        <button type="submit" class="btn btn-primary w-100">Register</button>
    </form>

    <!-- Link to Login page -->
    <div class="mt-3 text-center">
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
</div>

<?php include 'footer.php'; // Include the footer ?>