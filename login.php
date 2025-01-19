<?php include 'header.php'; // Include the header ?>

<div class="login-container">
    <h2>Login</h2>
    <form action="process_login.php" method="POST">
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