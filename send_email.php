<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Ensure this is the correct path

function sendRecommendationEmail($recipientEmail, $token, $studentName, $studentEmail)
{
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';       // Gmail SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'mr.anpanharith@gmail.com'; // Your Gmail address
        $mail->Password = 'zhlhqnnpmgkugrby';   // Your Gmail app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('theman@example.com', 'Test Only');
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

        $mail->send();
        return "Invitation sent successfully!";
    } catch (Exception $e) {
        return "Failed to send email. Error: {$mail->ErrorInfo}";
    }
}
?>