# User Management System

This is a PHP-based User Management System designed to handle user authentication, recommendation requests, and admin submissions. Follow the steps below to set up and run the system on a local **XAMPP** server.

---

## Prerequisites

1. **XAMPP** (Download from [XAMPP Official Site](https://www.apachefriends.org/)).
2. **PHP >= 7.4** (Included with XAMPP).
3. **MySQL** (Included with XAMPP).
4. **A text editor** (e.g., VS Code or Notepad++).

---

## Configure the Database

1. Open the `db_connect.php` file in the project directory.
2. Update the database credentials to match your XAMPP setup:
   ```php
   $conn = new mysqli('localhost', 'root', '', 'user_management');

## Run SQL: 
```Run script.sql```

## Configure the Mailer Settings
```
$mail->Host = 'smtp.example.com'; // Replace with your SMTP host.
$mail->Username = 'your-email@example.com'; // Replace with your email address.
$mail->Password = 'your-email-password';   // Replace with your email password.
$mail->Port = 587;                         // Adjust the port if needed.
```

## Running the System
1.	Start Apache and MySQL services in the XAMPP Control Panel.
2.	Open your browser and navigate to:
```http://localhost/php-login/index.html```
