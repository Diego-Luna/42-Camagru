<?php
    function sendConfirmationEmail($email, $token) {
        $subject = "Confirm your Camagru account";
        $confirmLink = "http://localhost:8080/verify.php?token=" . urlencode($token);
        $message = "Click here to confirm your account: " . $confirmLink;
        
        $headers = "From: camagru@example.com\r\n";
        $headers .= "Reply-To: camagru@example.com\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();

        // Use error_log instead of file_put_contents
        error_log("Sending mail to: " . $email . " with token: " . $token);
        
        return mail($email, $subject, $message, $headers);
    }

    function sendPasswordResetEmail($email, $token) {
        $subject = "Reset your Camagru password";
        $resetLink = "http://localhost:8080/reset-password.php?token=" . urlencode($token);
        $message = "Click here to reset your password: " . $resetLink;
        
        $headers = "From: camagru@example.com\r\n";
        $headers .= "Reply-To: camagru@example.com\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();
        
        return mail($email, $subject, $message, $headers);
    }

?>