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

## Configure the Mailer Settings:
Create a file called ".env" and add configuration params below:
```
SMTP_HOST=[e.g smtp.gmail.com]
SMTP_USERNAME=[e.g Gmail]
SMTP_PASSWORD=[your-app-password]
SMTP_SECURE=STARTTLS
SMTP_PORT=587
FROM_EMAIL=[Your source email]
FROM_NAME="[Your name]"
```

**IMPORTANT** Protect .env file by creating file .htaccess at the same level. 

**.htaccess**
```
# Deny access to the .env file
<Files .env>
    Order Allow,Deny
    Deny from all
</Files> 
```

## Installer Dependencies
```
composer require vlucas/phpdotenv
composer require phpmailer/phpmailer
```
## Create "uploads" directory
1. Create "uploads" directory for file upload in the root php folder of this project. We can use
```mkdir uploads```
2. Create a .htaccess file in the uploads directory:

**IMPORTANT** Create a .htaccess file inside the uploads directory to prevent direct access to the files by unauthorized users. This will block all requests from non-authenticated users.

**.htaccess**:
```
<Files *>
# Deny all access to files in the uploads folder
    Order Deny,Allow
    Deny from all
</Files>
```
This will block direct access to files in the uploads directory.

## Running the System
1.	Start Apache and MySQL services in the XAMPP Control Panel.
2.	Open your browser and navigate to:
```http://localhost/php-login/index.html```
