<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

require 'vendor/autoload.php'; // Ensure this is the correct path

// Load .env file
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

function sendRecommendationEmail($recipientEmail, $token, $studentName, $studentEmail)
{
    $mail = new PHPMailer(true);
    try {
        // Server settings - Use environment variables
        $mail->isSMTP();
        $mail->Host = $_ENV['SMTP_HOST']; // Gmail SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = $_ENV['SMTP_USERNAME']; // Your Gmail address
        $mail->Password = $_ENV['SMTP_PASSWORD'];   // Your Gmail app password
        $mail->SMTPSecure = $_ENV['SMTP_SECURE'];
        $mail->Port = $_ENV['SMTP_PORT'];

        // Recipients
        $mail->setFrom($_ENV['FROM_EMAIL'], $_ENV['FROM_NAME']);
        $mail->addAddress($recipientEmail);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Recommendation Request';
        $mail->Body    = "
            Dear Recommender,<br><br>
            A student has requested a recommendation letter.<br>
            <strong>Student Name:</strong> " . htmlspecialchars($studentName) . "<br>
            <strong>Student Email:</strong> " . htmlspecialchars($studentEmail) . "<br><br>
            Click the link below to respond to their request:<br>
            <a href='http://localhost/php-login/recommendation.php?token=$token'>Respond to Request</a><br><br>
            Thank you!";

        // Send email
        if ($mail->send()) {
            return "Invitation sent successfully!";
        } else {
            return "Failed to send email. Error: {$mail->ErrorInfo}";
        }
    } catch (Exception $e) {
        // More detailed error reporting
        return "Error: {$e->getMessage()}";
    }
}
?>