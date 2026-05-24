<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

class MailHelper {
    private static function configureMailer() {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $smtpHost = gethostbyname(SMTP_HOST);
        $mail->Host       = $smtpHost !== SMTP_HOST ? $smtpHost : SMTP_HOST;
        $mail->Hostname   = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER;
        $mail->Password   = SMTP_PASS;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = SMTP_PORT;
        $mail->Timeout    = 15;
        $mail->SMTPKeepAlive = false;
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            ],
        ];
        $mail->setFrom(
            defined('SMTP_FROM_EMAIL') ? SMTP_FROM_EMAIL : SMTP_USER, 
            defined('SMTP_FROM_NAME') ? SMTP_FROM_NAME : SITENAME
        );

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

    public static function sendPasswordResetOTP($email, $name, $otp, $link) {
        $mail = null;
        try {
            $mail = self::configureMailer();
            $safeName = htmlspecialchars((string)$name, ENT_QUOTES, 'UTF-8');
            $email = trim((string)$email);

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return false;
            }

            $mail->addAddress($email, $name);
            $mail->isHTML(true);
            $mail->Subject = 'Your Security Reset Verification Key: ' . $otp;

            $mail->Body = "
                <div style='font-family: Arial, sans-serif; background-color: #f4f4f7; padding: 40px 20px; text-align: center;'>
                    <div style='max-width: 500px; margin: 0 auto; background: #ffffff; padding: 32px; border-radius: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); text-align: left;'>
                        <h2 style='color: #0A66C2; margin-top: 0; font-size: 22px; text-align: center;'>ProNetwork Security Notification</h2>
                        <p style='color: #4b5563; font-size: 14px; line-height: 1.5;'>Hello {$safeName},</p>
                        <p style='color: #4b5563; font-size: 14px; line-height: 1.5;'>A password reset request was initiated for your ProNetwork account. You can verify your identity and instantly update your password by clicking the button below:</p>
                        
                        <div style='text-align: center; margin: 24px 0;'>
                            <a href='{$link}' style='display: inline-block; padding: 12px 28px; background-color: #0A66C2; color: #ffffff; text-decoration: none; font-size: 14px; font-weight: bold; border-radius: 8px; box-shadow: 0 4px 6px rgba(10,102,194,0.2); transition: background-color 0.2s;'>Instant Password Reset</a>
                        </div>
                        
                        <p style='color: #4b5563; font-size: 14px; line-height: 1.5; text-align: center;'>Or use this 6-digit security code in your reset form:</p>
                        
                        <div style='background-color: #f3f4f6; border: 1px dashed #d1d5db; border-radius: 12px; padding: 16px; margin: 20px 0; text-align: center;'>
                            <span style='font-size: 32px; font-weight: bold; letter-spacing: 6px; color: #1e293b; font-family: monospace;'>{$otp}</span>
                        </div>
                        
                        <p style='color: #4b5563; font-size: 12px; line-height: 1.5;'>If the button above does not work, copy and paste this URL into your browser:</p>
                        <p style='color: #0A66C2; font-size: 11px; word-break: break-all; margin: 8px 0;'>{$link}</p>
                        
                        <p style='color: #ef4444; font-size: 12px; line-height: 1.5; font-weight: 500; margin-top: 20px;'>Warning: This security link and key are strictly confidential and will expire in 15 minutes. If you did not authorize this request, please change your security clearance credentials immediately.</p>
                        
                        <hr style='border: 0; border-top: 1px solid #e5e7eb; margin: 24px 0;'>
                        <p style='color: #9ca3af; font-size: 11px; text-align: center; margin: 0;'>ProNetwork Platform. Secure Enterprise Core.</p>
                    </div>
                </div>
            ";
            $mail->AltBody = "Hello {$name}, your ProNetwork password reset verification key is: {$otp}. Link: {$link}. This code and link expire in 15 minutes.";

            $mail->send();
            return true;
        } catch (Exception $e) {
            $mailerError = $mail ? $mail->ErrorInfo : $e->getMessage();
            error_log("OTP password reset email could not be sent. Mailer Error: {$mailerError}");
            return false;
        }
    }
}
