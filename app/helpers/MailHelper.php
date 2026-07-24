<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailHelper {
    private static $lastError = '';

    public static function lastError(): string {
        return self::$lastError;
    }

    public static function publicUrl(string $path = ''): string {
        $base = defined('MAIL_BASE_URL') ? MAIL_BASE_URL : URLROOT;
        $base = rtrim((string)$base, '/');
        $base = preg_replace('#/public$#', '', $base);

        if (preg_match('#^https?://(localhost|127\.0\.0\.1)(:\d+)?#i', $base)) {
            $base = 'https://pronetwork.site.je';
        }

        return $path === '' ? $base : $base . '/' . ltrim($path, '/');
    }

    private static function validEmail(?string $email): bool {
        return filter_var(trim((string)$email), FILTER_VALIDATE_EMAIL) !== false;
    }

    private static function senderEmail(): string {
        $smtpUser = trim((string)(defined('SMTP_USER') ? SMTP_USER : ''));
        $configuredFrom = trim((string)(defined('SMTP_FROM_EMAIL') ? SMTP_FROM_EMAIL : ''));

        if (stripos((string)SMTP_HOST, 'gmail') !== false && self::validEmail($smtpUser)) {
            return $smtpUser;
        }

        if (self::validEmail($configuredFrom)) {
            return $configuredFrom;
        }

        if (self::validEmail($smtpUser)) {
            return $smtpUser;
        }

        return (defined('ADMIN_EMAIL') && self::validEmail(ADMIN_EMAIL)) ? ADMIN_EMAIL : 'no-reply@pronetwork.site.je';
    }

    private static function replyToAddress(bool $replyToAdmin): string {
        if ($replyToAdmin && defined('ADMIN_EMAIL') && self::validEmail(ADMIN_EMAIL)) {
            return ADMIN_EMAIL;
        }

        return 'no-reply@pronetwork.site.je';
    }

    private static function configureMailer(): PHPMailer {
        $smtpHost = trim((string)(defined('SMTP_HOST') ? SMTP_HOST : ''));
        $smtpUser = trim((string)(defined('SMTP_USER') ? SMTP_USER : ''));
        $smtpPass = trim((string)(defined('SMTP_PASS') ? SMTP_PASS : ''));

        if ($smtpHost === '' || !self::validEmail($smtpUser) || $smtpPass === '') {
            throw new Exception('SMTP is not fully configured. Check SMTP_HOST, SMTP_USER, and SMTP_PASS.');
        }

        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = $smtpHost;
        $mail->Hostname   = $_SERVER['HTTP_HOST'] ?? parse_url(self::publicUrl(), PHP_URL_HOST) ?: SITENAME;
        $mail->SMTPAuth   = true;
        $mail->Username   = $smtpUser;
        $mail->Password   = $smtpPass;
        $mail->SMTPSecure = ((int)SMTP_PORT === 465) ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = (int)SMTP_PORT;
        $mail->Timeout    = 20;
        $mail->SMTPKeepAlive = false;
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            ],
        ];

        $mail->setFrom(self::senderEmail(), defined('SMTP_FROM_NAME') ? SMTP_FROM_NAME : SITENAME);
        return $mail;
    }

    private static function sendMessage(string $toEmail, string $toName, string $subject, string $htmlBody, string $textBody, string $context, bool $replyToAdmin = false): bool {
        $toEmail = trim($toEmail);
        if (!self::validEmail($toEmail)) {
            self::$lastError = $context . ': invalid recipient email.';
            error_log(self::$lastError);
            return false;
        }

        $mail = null;
        try {
            $mail = self::configureMailer();
            $mail->addAddress($toEmail, $toName ?: $toEmail);
            $mail->addReplyTo(self::replyToAddress($replyToAdmin), $replyToAdmin ? 'ProNetwork Admin' : 'ProNetwork No Reply');
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $htmlBody;
            $mail->AltBody = $textBody;
            $mail->send();
            self::$lastError = '';
            return true;
        } catch (Exception $e) {
            $smtpError = $mail ? $mail->ErrorInfo : $e->getMessage();
            self::$lastError = $context . ' SMTP failed: ' . $smtpError;
            error_log(self::$lastError);
        }

        if (self::sendWithPhpMail($toEmail, $subject, $htmlBody, $textBody, $context, $replyToAdmin)) {
            return true;
        }

        return false;
    }

    private static function sendWithPhpMail(string $toEmail, string $subject, string $htmlBody, string $textBody, string $context, bool $replyToAdmin = false): bool {
        $from = self::senderEmail();
        $encodedSubject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
        $headers = [
            'MIME-Version: 1.0',
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . (defined('SMTP_FROM_NAME') ? SMTP_FROM_NAME : SITENAME) . ' <' . $from . '>',
            'Reply-To: ' . self::replyToAddress($replyToAdmin),
        ];

        $ok = @mail($toEmail, $encodedSubject, $htmlBody, implode("\r\n", $headers));
        if (!$ok) {
            self::$lastError .= ' PHP mail fallback also failed.';
            error_log($context . ' PHP mail fallback failed for ' . $toEmail . '. Text fallback: ' . $textBody);
        }
        return $ok;
    }

    public static function sendRegistrationAlert($userData, string $approvalUrl = '') {
        $safeUser = array_map(
            fn($value) => htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'),
            $userData
        );
        $adminUrl = self::publicUrl('/admin/users');
        $adminEmail = trim((string)(defined('ADMIN_EMAIL') ? ADMIN_EMAIL : ''));
        $approveButton = $approvalUrl !== ''
            ? "<a href='{$approvalUrl}' style='display: inline-block; margin-right: 10px; margin-bottom: 8px; padding: 10px 20px; background: #16a34a; color: #fff; text-decoration: none; border-radius: 5px; font-weight: bold;'>Approve Directly</a>"
            : '';

        $subject = 'Approval needed: ' . ($userData['full_name'] ?? 'New ProNetwork user');
        $body = "
            <h2>New User Awaiting Approval</h2>
            <p>A new user has registered and is waiting for admin approval.</p>
            <table border='1' cellpadding='10' cellspacing='0' style='border-collapse: collapse; width: 100%; max-width: 600px;'>
                <tr><td><strong>Name:</strong></td><td>{$safeUser['full_name']}</td></tr>
                <tr><td><strong>Email:</strong></td><td>{$safeUser['email']}</td></tr>
                <tr><td><strong>Role:</strong></td><td>{$safeUser['role']}</td></tr>
                <tr><td><strong>Location:</strong></td><td>{$safeUser['location']}</td></tr>
                <tr><td><strong>Registered At:</strong></td><td>" . date('Y-m-d H:i:s') . "</td></tr>
            </table>
            <p style='margin-top: 18px;'>{$approveButton}<a href='{$adminUrl}' style='display: inline-block; margin-bottom: 8px; padding: 10px 20px; background: #0A66C2; color: #fff; text-decoration: none; border-radius: 5px; font-weight: bold;'>Open Admin Panel</a></p>
            <p style='color: #64748b; font-size: 12px;'>The direct approval link is one-time use and expires in 24 hours.</p>
        ";
        $text = "New User Registered: {$userData['full_name']} ({$userData['email']})." . ($approvalUrl !== '' ? " Approve directly: {$approvalUrl}." : '') . " Open Admin Panel: {$adminUrl}";

        return self::sendMessage($adminEmail, 'Admin', $subject, $body, $text, 'Registration alert email', true);
    }

    public static function sendApprovalNotification($userData) {
        $safeName = htmlspecialchars((string)($userData['full_name'] ?? 'there'), ENT_QUOTES, 'UTF-8');
        $email = trim((string)($userData['email'] ?? ''));
        $loginUrl = self::publicUrl('/auth/login');
        $subject = 'Your ' . SITENAME . ' account has been approved';

        $body = "
            <h2>Your account is approved</h2>
            <p>Hello {$safeName},</p>
            <p>Your " . SITENAME . " account has been approved by an administrator. You can now sign in and start using your professional network.</p>
            <p><a href='{$loginUrl}' style='display: inline-block; padding: 10px 20px; background: #0A66C2; color: #fff; text-decoration: none; border-radius: 5px;'>Sign in to " . SITENAME . "</a></p>
            <p style='color: #64748b; font-size: 12px;'>This inbox is not monitored. Please do not reply to this email.</p>
        ";
        $text = "Hello {$userData['full_name']}, your " . SITENAME . " account has been approved. Sign in: {$loginUrl}";

        return self::sendMessage($email, $userData['full_name'] ?? '', $subject, $body, $text, 'Approval email');
    }

    public static function sendPasswordResetOTP($email, $name, $otp, $link = '') {
        $safeName = htmlspecialchars((string)$name, ENT_QUOTES, 'UTF-8');
        $email = trim((string)$email);
        $link = $link ?: self::publicUrl('/auth/login');
        $subject = 'Your ProNetwork password reset code: ' . $otp;

        $body = "
            <div style='font-family: Arial, sans-serif; background-color: #f4f4f7; padding: 40px 20px; text-align: center;'>
                <div style='max-width: 500px; margin: 0 auto; background: #ffffff; padding: 32px; border-radius: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); text-align: left;'>
                    <h2 style='color: #0A66C2; margin-top: 0; font-size: 22px; text-align: center;'>ProNetwork Password Reset</h2>
                    <p style='color: #4b5563; font-size: 14px; line-height: 1.5;'>Hello {$safeName},</p>
                    <p style='color: #4b5563; font-size: 14px; line-height: 1.5;'>A password reset request was initiated for your ProNetwork account. Click the button below to open the live reset page, or use the 6-digit code in the reset form.</p>
                    <div style='text-align: center; margin: 24px 0;'>
                        <a href='{$link}' style='display: inline-block; padding: 12px 28px; background-color: #0A66C2; color: #ffffff; text-decoration: none; font-size: 14px; font-weight: bold; border-radius: 8px;'>Reset Password</a>
                    </div>
                    <p style='color: #4b5563; font-size: 14px; line-height: 1.5; text-align: center;'>Your 6-digit security code:</p>
                    <div style='background-color: #f3f4f6; border: 1px dashed #d1d5db; border-radius: 12px; padding: 16px; margin: 20px 0; text-align: center;'>
                        <span style='font-size: 32px; font-weight: bold; letter-spacing: 6px; color: #1e293b; font-family: monospace;'>{$otp}</span>
                    </div>
                    <p style='color: #4b5563; font-size: 12px; line-height: 1.5;'>If the button does not work, copy and paste this URL into your browser:</p>
                    <p style='color: #0A66C2; font-size: 11px; word-break: break-all; margin: 8px 0;'>{$link}</p>
                    <p style='color: #ef4444; font-size: 12px; line-height: 1.5; font-weight: 500; margin-top: 20px;'>This security link and code expire in 15 minutes. If you did not request this, you can ignore this email.</p>
                    <p style='color: #9ca3af; font-size: 11px; margin-top: 20px;'>This inbox is not monitored. Please do not reply to this email.</p>
                </div>
            </div>
        ";
        $text = "Hello {$name}, your ProNetwork password reset code is: {$otp}. Link: {$link}. This code and link expire in 15 minutes.";

        return self::sendMessage($email, $name, $subject, $body, $text, 'Password reset email');
    }
}
