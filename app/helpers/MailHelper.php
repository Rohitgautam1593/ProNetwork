<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

class MailHelper {
    private static function configureMailer() {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER;
        $mail->Password   = SMTP_PASS;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = SMTP_PORT;
        $mail->setFrom(SMTP_USER, SITENAME);

        return $mail;
    }

    public static function sendRegistrationAlert($userData) {
        $mail = null;
        try {
            $mail = self::configureMailer();
            $safeUser = array_map(
                fn($value) => htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'),
                $userData
            );

            $mail->addAddress(ADMIN_EMAIL, 'Admin');
            $mail->isHTML(true);
            $mail->Subject = 'New User Registration: ' . $userData['full_name'];
            
            $body = "
                <h2>New User Registered</h2>
                <p>A new user has registered and is awaiting approval.</p>
                <table border='1' cellpadding='10' cellspacing='0' style='border-collapse: collapse; width: 100%; max-width: 600px;'>
                    <tr><td><strong>Name:</strong></td><td>{$safeUser['full_name']}</td></tr>
                    <tr><td><strong>Email:</strong></td><td>{$safeUser['email']}</td></tr>
                    <tr><td><strong>Role:</strong></td><td>{$safeUser['role']}</td></tr>
                    <tr><td><strong>Location:</strong></td><td>{$safeUser['location']}</td></tr>
                    <tr><td><strong>Registered At:</strong></td><td>" . date('Y-m-d H:i:s') . "</td></tr>
                </table>
                <p><a href='" . URLROOT . "/admin/users' style='display: inline-block; padding: 10px 20px; background: #0A66C2; color: #fff; text-decoration: none; border-radius: 5px;'>View Admin Panel</a></p>
            ";

            $mail->Body = $body;
            $mail->AltBody = "New User Registered: {$userData['full_name']} ({$userData['email']}). View Admin Panel: " . URLROOT . "/admin/users";

            $mail->send();
            return true;
        } catch (Exception $e) {
            // Log error
            $mailerError = $mail ? $mail->ErrorInfo : $e->getMessage();
            error_log("Message could not be sent. Mailer Error: {$mailerError}");
            return false;
        }
    }

    public static function sendApprovalNotification($userData) {
        $mail = null;
        try {
            $mail = self::configureMailer();
            $safeName = htmlspecialchars((string)$userData['full_name'], ENT_QUOTES, 'UTF-8');
            $email = trim((string)$userData['email']);

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return false;
            }

            $mail->addAddress($email, $userData['full_name']);
            $mail->isHTML(true);
            $mail->Subject = 'Your ' . SITENAME . ' account has been approved';

            $loginUrl = URLROOT . '/auth/login';
            $mail->Body = "
                <h2>Your account is approved</h2>
                <p>Hello {$safeName},</p>
                <p>Your " . SITENAME . " account has been approved by an administrator. You can now sign in and start using your professional network.</p>
                <p><a href='{$loginUrl}' style='display: inline-block; padding: 10px 20px; background: #0A66C2; color: #fff; text-decoration: none; border-radius: 5px;'>Sign in to " . SITENAME . "</a></p>
            ";
            $mail->AltBody = "Hello {$userData['full_name']}, your " . SITENAME . " account has been approved. Sign in: {$loginUrl}";

            $mail->send();
            return true;
        } catch (Exception $e) {
            $mailerError = $mail ? $mail->ErrorInfo : $e->getMessage();
            error_log("Approval email could not be sent. Mailer Error: {$mailerError}");
            return false;
        }
    }
}
