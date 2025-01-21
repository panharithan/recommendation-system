<?php
session_start();
include 'header.php';  // Include the header

?>

<div class="container">
    <h1 class="mt-5 text-center">Reference Letter Submission System</h1>
    
    <p class="lead text-center">Please log in to access your dashboard, or register if you're a new user.</p>
    
    <div class="d-flex justify-content-center">
        <a href="login.php" class="btn btn-primary mx-2">Login</a>
        <a href="register.php" class="btn btn-secondary mx-2">Register</a>
    </div>
</div>

<footer class="text-center mt-4">
    <p class="mb-0">Developed by <a href="https://github.com/panharithan" target="_blank" > Panharith An </a>, 2025, in CyberMACS programme</p>
</footer>


<?php include 'footer.php'; // Include the footer ?>