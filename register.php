<?php include 'header.php'; // Include the header ?>

<div class="login-container">
    <h2>Register</h2>
    <form action="process_register.php" method="POST">
        <div class="mb-3">
            <label for="username" class="form-label">Username:</label>
            <input type="text" id="username" name="username" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email:</label>
            <input type="email" id="email" name="email" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password:</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary w-100">Register</button>
    </form>

    <!-- Link to Login page -->
    <div class="mt-3 text-center">
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
</div>

<?php include 'footer.php'; // Include the footer ?>