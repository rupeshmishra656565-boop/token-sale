<?php
namespace Controllers;

require_once(__DIR__ . '/../config/config.php');
require_once(__DIR__ . '/../core/Database.php');

// --- NEW: INCLUDE PHPMAILER ---
// Make sure this path is correct based on your installation
require_once(__DIR__ . '/../vendor/autoload.php'); 

use Core\Database;
use PDO;
use PDOException;
// --- NEW: USE PHPMAILER ---
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class AuthController {
    private $pdo;

    public function __construct($start_session = true) {
        $this->pdo = Database::getInstance()->getConnection();
        if ($start_session && session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    // --- NEW HELPER FUNCTION: To Send Email ---
    /**
     * Sends the OTP email using PHPMailer.
     * YOU MUST CONFIGURE YOUR SMTP SETTINGS HERE.
     */
    private function send_otp_email($email, $otp_code) {
        $mail = new PHPMailer(true);
        $subject = "Your Verification Code for " . APP_NAME;
        $body = "
            <div style='font-family: Arial, sans-serif; line-height: 1.6;'>
                <h2>Email Verification</h2>
                <p>Hi,</p>
                <p>Thank you for registering with " . APP_NAME . ". Use the code below to verify your email address:</p>
                <p style='font-size: 24px; font-weight: bold; letter-spacing: 3px; background: #f4f4f4; padding: 10px 20px; display: inline-block; border-radius: 5px;'>
                    $otp_code
                </p>
                <p>This code will expire in 10 minutes.</p>
                <p>If you did not request this, please ignore this email.</p>
                <br>
                <p>Thanks,<br>The " . APP_NAME . " Team</p>
            </div>
        ";

        try {
            // --- SERVER SETTINGS: CONFIGURE THIS ---
            // $mail->SMTPDebug = 2;                      // Enable verbose debug output
            $mail->isSMTP();                                // Send using SMTP
            $mail->Host       = 'smtp.gmail.com';       // Set the SMTP server to send through
            $mail->SMTPAuth   = true;                       // Enable SMTP authentication
            $mail->Username   = 'pithosprotocol@gmail.com'; // SMTP username
            $mail->Password   = 'wgeo dlrh hczx qakn';    // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Enable implicit TLS encryption
            $mail->Port       = 465;                      // TCP port to connect to
            // --- END SERVER SETTINGS ---

            //Recipients
            $mail->setFrom('no-reply@example.com', APP_NAME); // Sender
            $mail->addAddress($email);                        // Add a recipient
            
            //Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;
            $mail->AltBody = "Your verification code is: $otp_code. This code will expire in 10 minutes.";

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("PHPMailer Error: {$mail->ErrorInfo}");
            return false;
        }
    }


    // --- NEW PUBLIC FUNCTION: To Send OTP ---
    /**
     * Generates, stores, and sends an OTP for registration.
     */
    public function sendRegistrationOtp($data) {
        $email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
        $username = filter_var($data['username'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        if (!$email || !$username) {
            return ['success' => false, 'message' => 'Invalid email or username.'];
        }

        try {
            // 1. Check if email or username is already in use in the main users table
            $stmtCheck = $this->pdo->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
            $stmtCheck->execute([$email, $username]);
            if ($stmtCheck->fetch()) {
                return ['success' => false, 'message' => 'Username or Email already taken.'];
            }

            // 2. Generate OTP
            $otp_code = (string)random_int(100000, 999999); // 6-digit code
            $otp_hash = password_hash($otp_code, PASSWORD_DEFAULT);
            $expires_at = date('Y-m-d H:i:s', time() + (10 * 60)); // 10-minute expiry

            // 3. Store OTP hash in the new table (REPLACE any existing one for this email)
            $stmtStore = $this->pdo->prepare(
                "INSERT INTO otp_verifications (email, otp_hash, expires_at) 
                 VALUES (?, ?, ?)
                 ON DUPLICATE KEY UPDATE otp_hash = ?, expires_at = ?"
            );
            $stmtStore->execute([$email, $otp_hash, $expires_at, $otp_hash, $expires_at]);

            // 4. Send the email
            if ($this->send_otp_email($email, $otp_code)) {
                return ['success' => true, 'message' => 'OTP sent to your email! Please check your inbox.'];
            } else {
                return ['success' => false, 'message' => 'Could not send OTP email. Please try again.'];
            }

        } catch (PDOException $e) {
            error_log("Send OTP failed: " . $e->getMessage());
            return ['success' => false, 'message' => 'A database error occurred.'];
        } catch (\Exception $e) {
            error_log("OTP generation failed: " . $e->getMessage());
            return ['success' => false, 'message' => 'A server error occurred.'];
        }
    }

    // --- NEW HELPER FUNCTION: Send Password Reset OTP Email ---
    private function send_password_reset_otp_email($email, $otp_code) {
        $mail = new PHPMailer(true);
        $subject = "Password Reset Request for " . APP_NAME;
        $resetLink = SITE_URL . 'reset_password.php?email=' . urlencode($email); // Link includes email
        $body = "
        <!DOCTYPE html>
        <html lang='en'><head><meta charset='UTF-8'><title>Password Reset</title></head>
        <body style='margin: 0; padding: 0; background-color: #0a0a0f; font-family: Inter, Arial, sans-serif; color: #e5e7eb;'>
        <table width='100%' border='0' cellspacing='0' cellpadding='0' style='background-color: #0a0a0f;'><tr><td align='center' style='padding: 40px 20px;'>
        <table width='600' border='0' cellspacing='0' cellpadding='0' style='max-width: 600px; background-color: rgba(20, 20, 30, 0.85); border: 1px solid rgba(153, 69, 255, 0.2); border-radius: 24px; overflow: hidden;'>
            <tr><td align='center' style='padding: 30px 30px 20px; background: linear-gradient(135deg, rgba(153, 69, 255, 0.1), rgba(20, 20, 30, 0.85)); border-bottom: 1px solid rgba(153, 69, 255, 0.2);'>
                <h1 style='margin: 0; font-family: \"Space Grotesk\", Arial, sans-serif; font-size: 26px; font-weight: 700; color: #ffffff;'>Password Reset Request</h1>
            </td></tr>
            <tr><td style='padding: 35px 30px; color: #d1d5db; font-size: 16px; line-height: 1.7;'>
                <p style='margin: 0 0 20px;'>Hello,</p>
                <p style='margin: 0 0 20px;'>We received a request to reset the password for your account associated with this email address.</p>
                <p style='margin: 0 0 25px;'>Use the following One-Time Password (OTP) to set a new password:</p>
                <p style='font-size: 24px; font-weight: bold; letter-spacing: 3px; background: #2d2d3a; padding: 12px 22px; display: inline-block; border-radius: 8px; margin: 10px 0 25px; color: #ffffff;'>
                    $otp_code
                </p>
                <p style='margin: 0 0 25px;'>Enter this code on the password reset page. You can access the page here:</p>
                <table border='0' cellspacing='0' cellpadding='0' align='center' style='margin: 30px auto;'><tr><td align='center' style='border-radius: 12px; background: linear-gradient(135deg, #9945FF, #7a35cc); box-shadow: 0 8px 20px rgba(153, 69, 255, 0.3);'>
                    <a href='" . $resetLink . "' target='_blank' style='font-size: 16px; font-weight: 600; color: #ffffff; text-decoration: none; border-radius: 12px; padding: 14px 30px; border: 1px solid #9945FF; display: inline-block;'>Reset Password Now</a>
                </td></tr></table>
                <p style='margin: 25px 0 0;'>This OTP will expire in 10 minutes. If you did not request a password reset, please ignore this email.</p>
            </td></tr>
            <tr><td style='padding: 25px 30px; border-top: 1px solid rgba(153, 69, 255, 0.1); text-align: center; font-size: 12px; color: #9ca3af;'>
                <p style='margin: 0;'>&copy; " . date("Y") . " " . APP_NAME . ". All Rights Reserved.</p>
            </td></tr>
        </table></td></tr></table></body></html>
        ";

        try {
            // --- SERVER SETTINGS: Use the SAME settings ---
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';       // **REPLACE**
            $mail->SMTPAuth   = true;
            $mail->Username   = 'pithosprotocol@gmail.com'; // **REPLACE**
            $mail->Password   = 'wgeo dlrh hczx qakn';    // **REPLACE**
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;                      // **REPLACE** if needed
            // --- END SERVER SETTINGS ---

            $mail->setFrom('pithosprotocol@gmail.com', APP_NAME); // **REPLACE** Sender
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;
            $mail->AltBody = "Your password reset OTP is: $otp_code. It expires in 10 minutes. Go to $resetLink to reset your password.";

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("PHPMailer Password Reset Error: {$mail->ErrorInfo}");
            return false;
        }
    }


    // --- MODIFIED FUNCTION: Reset Password using OTP ---
    // Renamed from resetPasswordWithToken
    public function resetPasswordWithOtp($data) {
        $email = filter_var($data['email'] ?? null, FILTER_VALIDATE_EMAIL);
        $otp_code = $data['otp_code'] ?? '';
        $newPassword = $data['new_password'] ?? '';
        $confirmPassword = $data['confirm_password'] ?? '';

        // Basic validation
        if (!$email || empty($otp_code) || empty($newPassword) || $newPassword !== $confirmPassword || strlen($newPassword) < 6) {
            return ['success' => false, 'message' => 'Invalid input. Check email, OTP, and passwords (min 6 characters, must match).'];
        }

        try {
            // 1. Verify OTP
            $stmtVerify = $this->pdo->prepare("SELECT otp_hash, expires_at FROM otp_verifications WHERE email = ?");
            $stmtVerify->execute([$email]);
            $verification = $stmtVerify->fetch();

            if (!$verification) {
                 return ['success' => false, 'message' => 'No OTP found for this email, or it has already been used. Please request again.'];
            }

            if (new \DateTime() > new \DateTime($verification['expires_at'])) {
                // Optionally delete expired OTP here
                // $this->pdo->prepare("DELETE FROM otp_verifications WHERE email = ?")->execute([$email]);
                return ['success' => false, 'message' => 'OTP has expired. Please request a new one.'];
            }

            if (!password_verify($otp_code, $verification['otp_hash'])) {
                 return ['success' => false, 'message' => 'Invalid OTP code entered.'];
            }

            // 2. OTP is valid, proceed to update password
            $this->pdo->beginTransaction();

            // Hash the new password
            $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);

            // Update the user's password in the users table
            $stmtUpdate = $this->pdo->prepare("UPDATE users SET password_hash = ? WHERE email = ?");
            $updated = $stmtUpdate->execute([$newPasswordHash, $email]);
            $rowsAffected = $stmtUpdate->rowCount();

            if ($rowsAffected === 0) {
                 $this->pdo->rollBack();
                 // This could happen if the user was deleted between OTP request and reset attempt
                 return ['success' => false, 'message' => 'User account not found for this email.'];
            }

            // 3. Delete the used OTP
            $stmtDeleteOtp = $this->pdo->prepare("DELETE FROM otp_verifications WHERE email = ?");
            $stmtDeleteOtp->execute([$email]);

            $this->pdo->commit();

            return ['success' => true, 'message' => 'Password has been successfully reset! You can now log in with your new password.'];

        } catch (PDOException $e) {
            if ($this->pdo->inTransaction()) { $this->pdo->rollBack(); }
            error_log("Reset Password with OTP failed: " . $e->getMessage());
            return ['success' => false, 'message' => 'A database error occurred during password reset.'];
        }
    }



    // --- NEW PUBLIC FUNCTION: Send Password Reset OTP ---
    public function sendPasswordResetOtp($data) {
        $email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);

        if (!$email) {
            return ['success' => false, 'message' => 'Invalid email address provided.'];
        }

        try {
            // 1. Check if user exists
            $stmtCheck = $this->pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmtCheck->execute([$email]);
            $user = $stmtCheck->fetch();

            if (!$user) {
                // Security: Don't reveal if email exists or not
                return ['success' => true, 'message' => 'If an account exists for this email, an OTP has been sent.'];
            }

            // 2. Generate OTP
            $otp_code = (string)random_int(100000, 999999);
            $otp_hash = password_hash($otp_code, PASSWORD_DEFAULT);
            $expires_at = date('Y-m-d H:i:s', time() + (10 * 60)); // 10 minutes

            // 3. Store OTP hash (Replace existing for this email)
            $stmtStore = $this->pdo->prepare(
                "INSERT INTO otp_verifications (email, otp_hash, expires_at)
                 VALUES (?, ?, ?)
                 ON DUPLICATE KEY UPDATE otp_hash = ?, expires_at = ?"
            );
            $stmtStore->execute([$email, $otp_hash, $expires_at, $otp_hash, $expires_at]);

            // 4. Send the password reset email
            if ($this->send_password_reset_otp_email($email, $otp_code)) {
                return ['success' => true, 'message' => 'Password reset OTP sent! Please check your email.'];
            } else {
                return ['success' => false, 'message' => 'Could not send reset email. Please try again.'];
            }

        } catch (PDOException $e) {
            error_log("Send Password Reset OTP failed: " . $e->getMessage());
            return ['success' => false, 'message' => 'A database error occurred.'];
        } catch (\Exception $e) {
            error_log("OTP generation/send failed: " . $e->getMessage());
            return ['success' => false, 'message' => 'A server error occurred sending the OTP.'];
        }
    }

    // --- UPDATED HELPER FUNCTION: To Send Welcome Email with Better Design ---
    /**
     * Sends a welcome email after successful registration.
     */
    private function send_welcome_email($email, $username) {
        $mail = new PHPMailer(true);
        $subject = "Welcome to " . APP_NAME . "!";
        // --- Improved Email Body ---
        $body = "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Welcome to " . APP_NAME . "</title>
        </head>
        <body style='margin: 0; padding: 0; background-color: #0a0a0f; font-family: Inter, Arial, sans-serif; color: #e5e7eb;'>
            <table width='100%' border='0' cellspacing='0' cellpadding='0' style='background-color: #0a0a0f;'>
                <tr>
                    <td align='center' style='padding: 40px 20px;'>
                        <table width='600' border='0' cellspacing='0' cellpadding='0' style='max-width: 600px; background-color: rgba(20, 20, 30, 0.85); border: 1px solid rgba(153, 69, 255, 0.2); border-radius: 24px; overflow: hidden;'>
                            <tr>
                                <td align='center' style='padding: 30px 30px 20px; background: linear-gradient(135deg, rgba(153, 69, 255, 0.1), rgba(20, 20, 30, 0.85)); border-bottom: 1px solid rgba(153, 69, 255, 0.2);'>
                                    <h1 style='margin: 0; font-family: \"Space Grotesk\", Arial, sans-serif; font-size: 28px; font-weight: 700; color: #ffffff; letter-spacing: -0.03em;'>
                                        Welcome to <span style='background: linear-gradient(135deg, #ffffff 0%, #b565ff 100%); -webkit-background-clip: text; background-clip: text; color: transparent;'>" . APP_NAME . "</span>!
                                    </h1>
                                </td>
                            </tr>
                            <tr>
                                <td style='padding: 35px 30px; color: #d1d5db; font-size: 16px; line-height: 1.7;'>
                                    <p style='margin: 0 0 20px;'>Hi " . htmlspecialchars($username) . ",</p>
                                    <p style='margin: 0 0 20px;'>Thank you for joining <strong style='color: #b565ff;'>" . APP_NAME . "</strong>. We're thrilled to have you as part of our community building the future of secure digital assets on Solana!</p>
                                    <p style='margin: 0 0 25px;'>As a welcome gift, <strong style='color: #14F195;'>" . number_format(KYC_BONUS, 0) . " " . TOKEN_SYMBOL . "</strong> bonus tokens have been credited to your account.</p>
                                    <p style='margin: 0 0 25px;'>Access your dashboard now to view your balance, purchase tokens, and manage your profile:</p>
                                    <table border='0' cellspacing='0' cellpadding='0' align='center' style='margin: 30px auto;'>
                                        <tr>
                                            <td align='center' style='border-radius: 12px; background: linear-gradient(135deg, #14F195 0%, #0ea770 100%); box-shadow: 0 8px 20px rgba(20, 241, 149, 0.3);'>
                                                <a href='" . SITE_URL . "' target='_blank' style='font-size: 16px; font-weight: 600; color: #000000; text-decoration: none; border-radius: 12px; padding: 14px 30px; border: 1px solid #14F195; display: inline-block;'>
                                                    Go to Your Dashboard
                                                </a>
                                            </td>
                                        </tr>
                                    </table>
                                    <p style='margin: 25px 0 0;'>If you have any questions, please don't hesitate to reach out.</p>
                                </td>
                            </tr>
                            <tr>
                                <td style='padding: 25px 30px; border-top: 1px solid rgba(153, 69, 255, 0.1); text-align: center; font-size: 12px; color: #9ca3af;'>
                                    <p style='margin: 0;'>&copy; " . date("Y") . " " . APP_NAME . ". All Rights Reserved.</p>
                                    <p style='margin: 5px 0 0;'>You received this email because you registered an account.</p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </body>
        </html>
        ";
        // --- End Improved Email Body ---

        try {
            // --- SERVER SETTINGS: Use the SAME settings as send_otp_email ---
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';       // **REPLACE**
            $mail->SMTPAuth   = true;
            $mail->Username   = 'pithosprotocol@gmail.com'; // **REPLACE**
            $mail->Password   = 'wgeo dlrh hczx qakn';    // **REPLACE**
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;                      // **REPLACE** port if needed
            // --- END SERVER SETTINGS ---

            //Recipients
            $mail->setFrom('pithosprotocol@gmail.com', APP_NAME); // **REPLACE** Sender
            $mail->addAddress($email, $username);             // Add recipient

            //Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;
            $mail->AltBody = "Welcome to " . APP_NAME . ", " . $username . "! Your account is ready. Log in at " . SITE_URL;

            $mail->send();
            return true; // Email sent successfully
        } catch (Exception $e) {
            error_log("PHPMailer Welcome Email Error for $email: {$mail->ErrorInfo}");
            return false; // Email sending failed
        }
    }


    // --- MODIFIED Registration Process ---
    public function register($data) {
        $email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
        $username = filter_var($data['username'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $password = $data['password'] ?? '';
        $refIdInput = $data['referrer_id'] ?? '';
        $otp_code = $data['otp_code'] ?? ''; // NEW OTP field
        $refId = null;

        // --- 1. Basic Input validation ---
        if (empty($otp_code)) {
             return ['success' => false, 'message' => 'Please enter the OTP sent to your email.'];
        }
        if (!empty($refIdInput)) {
            $filteredId = filter_var($refIdInput, FILTER_VALIDATE_INT);
            if ($filteredId !== false && $filteredId > 0) {
                $refId = $filteredId;
            }
        }
        if (!$email || !$username || empty($password) || strlen($password) < 6) {
            return ['success' => false, 'message' => 'Invalid input. Check fields and password length (min 6).'];
        }

        // --- 2. Check for existing user (redundant if sendOTP check, but good for safety) ---
        $stmtCheck = $this->pdo->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
        $stmtCheck->execute([$email, $username]);
        if ($stmtCheck->fetch()) {
            return ['success' => false, 'message' => 'Username or Email already taken.'];
        }

        try {
            // --- 3. Verify OTP ---
            $stmtVerify = $this->pdo->prepare("SELECT otp_hash, expires_at FROM otp_verifications WHERE email = ?");
            $stmtVerify->execute([$email]);
            $verification = $stmtVerify->fetch();

            if (!$verification) {
                 return ['success' => false, 'message' => 'OTP not found. Please send an OTP first.'];
            }

            if (new \DateTime() > new \DateTime($verification['expires_at'])) {
                return ['success' => false, 'message' => 'OTP has expired. Please request a new one.'];
            }

            if (!password_verify($otp_code, $verification['otp_hash'])) {
                 return ['success' => false, 'message' => 'Invalid OTP code.'];
            }

            // --- 4. OTP is valid, proceed with registration ---
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $initialBonus = KYC_BONUS;

            $this->pdo->beginTransaction();

            // Insert user
            // NOTE: Corrected the INSERT statement from your provided snippet which was missing columns
            $stmtInsert = $this->pdo->prepare(
                "INSERT INTO users (username, email, password_hash, tokens, bonus_tokens, referral_tokens, kyc_claimed, referrer_id)
                 VALUES (?, ?, ?, 0.00, ?, 0.00, TRUE, ?)"
            );
            $stmtInsert->execute([$username, $email, $passwordHash, $initialBonus, $refId]);
            $newUserId = $this->pdo->lastInsertId();

            // Give referral bonus
            if ($refId !== null && $refId != $newUserId) {
                $stmtRef = $this->pdo->prepare("UPDATE users SET referral_tokens = referral_tokens + ? WHERE id = ?");
                $stmtRef->execute([REFERRAL_BONUS, $refId]);
            }

            // Log transactions
            $stmtLogSignup = $this->pdo->prepare("INSERT INTO transactions (user_id, type, amount, status, details) VALUES (?, 'SIGNUP', ?, 'Complete', ?)");
            $stmtLogSignup->execute([$newUserId, $initialBonus, 'Signup Bonus Credited']);

            if ($refId !== null && $refId != $newUserId) {
                 $stmtLogRef = $this->pdo->prepare("INSERT INTO transactions (user_id, type, amount, status, details) VALUES (?, 'REFERRAL', ?, 'Complete', ?)");
                 $stmtLogRef->execute([$refId, REFERRAL_BONUS, 'Referral Reward for user ' . $newUserId]);
            }

            // --- 5. Clean up OTP table ---
            $stmtDeleteOtp = $this->pdo->prepare("DELETE FROM otp_verifications WHERE email = ?");
            $stmtDeleteOtp->execute([$email]);

            $this->pdo->commit(); // Commit database changes

            // --- ADDED: SEND WELCOME EMAIL ---
            $this->send_welcome_email($email, $username);
            // --- END ADDED ---

            // Set session and return success
            $_SESSION['user_id'] = $newUserId;
            $_SESSION['username'] = $username;
            // Updated success message to use constants for consistency
            return ['success' => true, 'message' => 'Registration successful! You received ' . number_format(KYC_BONUS) . ' Bonus ' . TOKEN_SYMBOL . ' tokens.'];

        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Registration failed: " . $e->getMessage());
            return ['success' => false, 'message' => 'Registration failed due to a database error.'];
        }
    }


    // --- User Login (No changes needed) ---
    public function login($data) {
        // ... (this function remains unchanged)
        $loginId = $data['login_id'] ?? ''; $password = $data['password'] ?? '';
        if (empty($loginId) || empty($password)) { return ['success' => false, 'message' => 'Credentials required.']; }
        try {
            $stmt = $this->pdo->prepare("SELECT id, username, password_hash FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$loginId, $loginId]); $user = $stmt->fetch();
            if ($user && password_verify($password, $user['password_hash'])) {
                session_regenerate_id(true); $_SESSION['user_id'] = $user['id']; $_SESSION['username'] = $user['username'];
                return ['success' => true, 'message' => 'Login successful! Redirecting...'];
            } else { return ['success' => false, 'message' => 'Invalid username/email or password.']; }
        } catch (PDOException $e) { error_log("Login failed: " . $e->getMessage()); return ['success' => false, 'message' => 'DB error during login.']; }
    }

    // ... (All other functions from AuthController.php remain the same) ...


    // --- NEW HELPER FUNCTION: Send Purchase Confirmation Email ---
    /**
     * Sends an email confirming a successful token purchase.
     */
    private function send_purchase_confirmation_email($email, $username, $usdAmount, $tokensReceived, $orderId, $payCurrency) {
        $mail = new PHPMailer(true);
        $subject = "Your " . TOKEN_SYMBOL . " Purchase Confirmation - Order #" . $orderId;
        $formattedTokens = number_format($tokensReceived, 2);
        $formattedUsd = number_format($usdAmount, 2);
        $currencyUpper = strtoupper($payCurrency);

        $body = "
        <!DOCTYPE html>
        <html lang='en'><head><meta charset='UTF-8'><title>Purchase Confirmation</title></head>
        <body style='margin: 0; padding: 0; background-color: #0a0a0f; font-family: Inter, Arial, sans-serif; color: #e5e7eb;'>
        <table width='100%' border='0' cellspacing='0' cellpadding='0' style='background-color: #0a0a0f;'><tr><td align='center' style='padding: 40px 20px;'>
        <table width='600' border='0' cellspacing='0' cellpadding='0' style='max-width: 600px; background-color: rgba(20, 20, 30, 0.85); border: 1px solid rgba(20, 241, 149, 0.2); border-radius: 24px; overflow: hidden;'>
            <tr><td align='center' style='padding: 30px 30px 20px; background: linear-gradient(135deg, rgba(20, 241, 149, 0.1), rgba(20, 20, 30, 0.85)); border-bottom: 1px solid rgba(20, 241, 149, 0.2);'>
                <h1 style='margin: 0; font-family: \"Space Grotesk\", Arial, sans-serif; font-size: 28px; font-weight: 700; color: #ffffff;'>
                   <span style='color: #14F195;'>Purchase Successful!</span>
                </h1>
            </td></tr>
            <tr><td style='padding: 35px 30px; color: #d1d5db; font-size: 16px; line-height: 1.7;'>
                <p style='margin: 0 0 20px;'>Hi " . htmlspecialchars($username) . ",</p>
                <p style='margin: 0 0 20px;'>Thank you for your purchase! Your order <strong style='color: #ffffff;'>#" . htmlspecialchars($orderId) . "</strong> is complete.</p>
                <p style='margin: 0 0 25px;'>Your tokens have been credited to your account.</p>
                <table width='100%' border='0' cellspacing='0' cellpadding='0' style='margin-bottom: 25px; border-collapse: collapse;'>
                    <tr><td style='padding: 10px; border-bottom: 1px solid rgba(255,255,255,0.1); color: #9ca3af;'>Amount Paid:</td>
                        <td style='padding: 10px; border-bottom: 1px solid rgba(255,255,255,0.1); text-align: right; color: #ffffff; font-weight: bold;'>$" . $formattedUsd . " USD</td></tr>
                    <tr><td style='padding: 10px; border-bottom: 1px solid rgba(255,255,255,0.1); color: #9ca3af;'>Payment Method:</td>
                        <td style='padding: 10px; border-bottom: 1px solid rgba(255,255,255,0.1); text-align: right; color: #ffffff;'>{$currencyUpper}</td></tr>
                    <tr><td style='padding: 10px; color: #9ca3af;'>Tokens Received:</td>
                        <td style='padding: 10px; text-align: right; color: #14F195; font-weight: bold; font-size: 18px;'>" . $formattedTokens . " " . TOKEN_SYMBOL . "</td></tr>
                </table>
                <p style='margin: 0 0 25px;'>You can view your updated balance and transaction history in your dashboard:</p>
                <table border='0' cellspacing='0' cellpadding='0' align='center' style='margin: 30px auto;'><tr><td align='center' style='border-radius: 12px; background: linear-gradient(135deg, #14F195 0%, #0ea770 100%); box-shadow: 0 8px 20px rgba(20, 241, 149, 0.3);'>
                    <a href='" . SITE_URL . "?p=dashboard' target='_blank' style='font-size: 16px; font-weight: 600; color: #000000; text-decoration: none; border-radius: 12px; padding: 14px 30px; border: 1px solid #14F195; display: inline-block;'>View Dashboard</a>
                </td></tr></table>
                <p style='margin: 25px 0 0;'>If you have any questions about this purchase, please contact support.</p>
            </td></tr>
            <tr><td style='padding: 25px 30px; border-top: 1px solid rgba(20, 241, 149, 0.1); text-align: center; font-size: 12px; color: #9ca3af;'>
                <p style='margin: 0;'>&copy; " . date("Y") . " " . APP_NAME . ". All Rights Reserved.</p>
            </td></tr>
        </table></td></tr></table></body></html>
        ";

        try {
            // --- SERVER SETTINGS: Use the SAME settings ---
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';       // **REPLACE**
            $mail->SMTPAuth   = true;
            $mail->Username   = 'pithosprotocol@gmail.com'; // **REPLACE**
            $mail->Password   = 'wgeo dlrh hczx qakn';    // **REPLACE**
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;                      // **REPLACE** if needed
            // --- END SERVER SETTINGS ---

            $mail->setFrom('pithosprotocol@gmail.com', APP_NAME); // **REPLACE** Sender
            $mail->addAddress($email, $username);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;
            $mail->AltBody = "Your purchase of {$formattedTokens} " . TOKEN_SYMBOL . " for \${$formattedUsd} USD (Order #{$orderId}) is complete. Visit your dashboard: " . SITE_URL . "?p=dashboard";

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("PHPMailer Purchase Confirmation Error for $email (Order $orderId): {$mail->ErrorInfo}");
            return false; // Don't block IPN processing if email fails
        }
    }


    
    // --- User Logout (No changes needed) ---
    public function logout() {
        session_unset(); session_destroy();
        return ['success' => true, 'message' => 'Logged out successfully.'];
    }

    // --- Update Password (No changes needed) ---
    public function updatePassword($data) {
        if (!isset($_SESSION['user_id'])) { return ['success' => false, 'message' => 'Auth required.']; }
        $userId = $_SESSION['user_id']; $currentPassword = $data['current_password'] ?? ''; $newPassword = $data['new_password'] ?? ''; $confirmPassword = $data['confirm_password'] ?? '';
        if (empty($currentPassword) || empty($newPassword) || $newPassword !== $confirmPassword || strlen($newPassword) < 6) { return ['success' => false, 'message' => 'Invalid input. Check passwords (min 6).']; }
        try {
            $stmt = $this->pdo->prepare("SELECT password_hash FROM users WHERE id = ?"); $stmt->execute([$userId]); $user = $stmt->fetch();
            if (!$user || !password_verify($currentPassword, $user['password_hash'])) { return ['success' => false, 'message' => 'Current password incorrect.']; }
            $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmtUpdate = $this->pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?"); $stmtUpdate->execute([$newPasswordHash, $userId]);
            return ['success' => true, 'message' => 'Password updated!'];
        } catch (PDOException $e) { error_log("Password update failed: " . $e->getMessage()); return ['success' => false, 'message' => 'Server error updating password.']; }
    }

    // --- Payment Flow ---

    // --- createNowPaymentsInvoice (No changes needed, already sets 'Processing') ---
    public function createNowPaymentsInvoice($data) {
        if (!isset($_SESSION['user_id'])) { return ['success' => false, 'message' => 'Auth required.']; }
        $userId = $_SESSION['user_id']; $usdAmount = filter_var($data['usd_amount'], FILTER_VALIDATE_FLOAT); $bonusPercent = filter_var($data['bonus_percent'], FILTER_VALIDATE_FLOAT); $payCurrency = filter_var($data['pay_currency'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $planKey = 'CUSTOM'; if ($usdAmount == 15 && $bonusPercent == 0) $planKey = 'STARTER'; else if ($usdAmount == 100 && $bonusPercent == 0.25) $planKey = 'PRO'; else if ($usdAmount == 50 && $bonusPercent == 0.10) $planKey = 'ENTERPRISE';
        if (!$usdAmount || $usdAmount <= 0) { return ['success' => false, 'message' => 'Invalid amount.']; } if (empty($payCurrency)) { return ['success' => false, 'message' => 'No currency selected.']; }
        $allowed_currencies = ['usdttrc20', 'usdtbsc', 'trx']; if (!in_array($payCurrency, $allowed_currencies)) { return ['success' => false, 'message' => 'Invalid currency.']; }
        $baseTokens = $usdAmount * TOKEN_RATE; $totalTokens = $baseTokens + ($baseTokens * $bonusPercent); $orderId = $userId . '-' . time(); $description = "Purchase of " . number_format($totalTokens) . " GALAXY tokens";
        $transaction_id = null; $payment_record_id = null;
        $initialStatus = 'Processing'; 
        try {
            $this->pdo->beginTransaction();
            $stmtTx = $this->pdo->prepare("INSERT INTO transactions (user_id, type, amount, status, details) VALUES (?, 'PURCHASE', ?, ?, ?)"); 
            $stmtTx->execute([$userId, $totalTokens, $initialStatus, $orderId]); 
            $transaction_id = $this->pdo->lastInsertId(); 
            if (!$transaction_id) { $this->pdo->rollBack(); return ['success' => false, 'message' => 'DB error (TX).']; }
            $stmtPay = $this->pdo->prepare("INSERT INTO payments (user_id, order_id, plan_key, usd_amount, tokens, pay_currency, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())"); 
            $stmtPay->execute([$userId, $orderId, $planKey, $usdAmount, $totalTokens, $payCurrency, $initialStatus]); 
            $payment_record_id = $this->pdo->lastInsertId(); 
            if (!$payment_record_id) { $this->pdo->rollBack(); return ['success' => false, 'message' => 'DB error (PAY).']; }
            $payload = ['price_amount' => $usdAmount, 'price_currency' => 'usd', 'pay_currency' => $payCurrency, 'order_id' => $orderId, 'order_description' => $description, 'ipn_callback_url' => IPN_URL, 'success_url' => SITE_URL . '?p=wallet&payment=success&orderId=' . $orderId, 'cancel_url' => SITE_URL . '?p=dashboard&payment=cancelled'];
            $ch = curl_init(); 
            curl_setopt($ch, CURLOPT_URL, NOWPAYMENTS_API_URL . '/payment'); curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); curl_setopt($ch, CURLOPT_POST, 1); curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload)); curl_setopt($ch, CURLOPT_HTTPHEADER, ['x-api-key: ' . NOWPAYMENTS_API_KEY, 'Content-Type: application/json']);
            $response = curl_exec($ch); $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); $curl_error = curl_error($ch); curl_close($ch);
            if ($http_code == 201 || $http_code == 200) {
                $responseData = json_decode($response, true);
                if (isset($responseData['pay_address']) && isset($responseData['pay_amount'])) {
                    $gatewayPaymentId = $responseData['payment_id'] ?? null;
                    if ($gatewayPaymentId && $payment_record_id) { $stmtUpdatePayId = $this->pdo->prepare("UPDATE payments SET gateway_payment_id = ?, updated_at = NOW() WHERE id = ?"); $stmtUpdatePayId->execute([$gatewayPaymentId, $payment_record_id]); }
                    $this->pdo->commit();
                    return ['success' => true, 'transaction_id' => $transaction_id, 'pay_address' => $responseData['pay_address'], 'pay_amount' => $responseData['pay_amount'], 'pay_currency' => $responseData['pay_currency'] ?? $payCurrency];
                } else { $this->pdo->rollBack(); error_log("NP Response Missing Details: " . $response); return ['success' => false, 'message' => 'Gateway Error: Invalid response structure: ' . $response]; }
            }
            $this->pdo->rollBack(); $errorData = json_decode($response, true); $errorMessage = $errorData['message'] ?? $response ?? "API error (HTTP: $http_code)"; if (!empty($curl_error)) { $errorMessage = "cURL Error: " . $curl_error; } error_log("NP API Error: " . $errorMessage); return ['success' => false, 'message' => 'Gateway Error: ' . $errorMessage];
        } catch (PDOException $e) { if ($this->pdo->inTransaction()) { $this->pdo->rollBack(); } error_log("Create Invoice DB Error: " . $e->getMessage()); return ['success' => false, 'message' => 'Database error.']; }
    }

    // --- getPaymentStatus (No changes needed) ---
    public function getPaymentStatus($data) {
        if (!isset($_SESSION['user_id'])) { return ['success' => false, 'message' => 'Auth required.']; } 
        $userId = $_SESSION['user_id']; 
        $transactionId = filter_var($data['transaction_id'] ?? null, FILTER_VALIDATE_INT);
        if (!$transactionId) { return ['success' => false, 'message' => 'Invalid Tx ID.']; }
        try { 
            $stmt = $this->pdo->prepare("SELECT status FROM transactions WHERE id = ? AND user_id = ?"); 
            $stmt->execute([$transactionId, $userId]); 
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) { 
                return ['success' => true, 'status' => $result['status']]; 
            } else { 
                return ['success' => false, 'message' => 'Tx not found.']; 
            }
        } catch (PDOException $e) { 
            error_log("Get Status DB Error: " . $e->getMessage()); 
            return ['success' => false, 'message' => 'DB error.']; 
        }
    }
    
     // --- cancelPayment (No changes needed) ---
     public function cancelPayment($data) {
         if (!isset($_SESSION['user_id'])) { return ['success' => false, 'message' => 'Auth required.']; }
         $userId = $_SESSION['user_id'];
         $transactionId = filter_var($data['transaction_id'] ?? null, FILTER_VALIDATE_INT);

         if (!$transactionId) {
             return ['success' => false, 'message' => 'Invalid Transaction ID provided for cancellation.'];
         }

         try {
             $this->pdo->beginTransaction();
             $stmtGetOrder = $this->pdo->prepare("SELECT details FROM transactions WHERE id = ? AND user_id = ? AND status = 'Processing'");
             $stmtGetOrder->execute([$transactionId, $userId]);
             $orderId = $stmtGetOrder->fetchColumn();

             if (!$orderId) {
                 $this->pdo->rollBack();
                 return ['success' => true, 'message' => 'Payment already processed or not found. No action taken.'];
             }

             $stmtUpdateTx = $this->pdo->prepare("UPDATE transactions SET status = 'Failed' WHERE id = ? AND user_id = ? AND status = 'Processing'");
             $updatedTx = $stmtUpdateTx->execute([$transactionId, $userId]);
             $txRowsAffected = $stmtUpdateTx->rowCount();

             $stmtUpdatePay = $this->pdo->prepare("UPDATE payments SET status = 'Failed', updated_at = NOW() WHERE order_id = ? AND user_id = ? AND status = 'Processing'");
             $updatedPay = $stmtUpdatePay->execute([$orderId, $userId]);
             $payRowsAffected = $stmtUpdatePay->rowCount();

             if ($txRowsAffected > 0 || $payRowsAffected > 0) {
                 $this->pdo->commit();
                 error_log("User $userId cancelled Transaction ID: $transactionId (Order: $orderId)");
                 return ['success' => true, 'message' => 'Payment cancelled by user.'];
             } else {
                 $this->pdo->rollBack();
                 return ['success' => true, 'message' => 'Payment status was not Processing. No action taken.'];
             }

         } catch (PDOException $e) {
             if ($this->pdo->inTransaction()) { $this->pdo->rollBack(); }
             error_log("Cancel Payment DB Error for TxID $transactionId: " . $e->getMessage());
             return ['success' => false, 'message' => 'Database error during cancellation.'];
         }
     }


    /** Handles NowPayments IPN */
    /** Handles NowPayments IPN */
    public function handleNowPaymentsIPN() {
         error_log("====== IPN Handler Started ======");
         // ... (Signature validation and data decoding remain the same) ...
         try {
             // ... (Signature check, JSON decode) ...

             $payment_status = $data['payment_status'] ?? 'unknown';
             $orderId = $data['order_id'] ?? null;
             // ... (other data extraction) ...

             $finalDbStatus = 'Processing';
             if ($payment_status === 'finished') { $finalDbStatus = 'Complete'; }
             // ... (failed status handling) ...

             // ... (Intermediate status handling) ...

             $this->pdo->beginTransaction();

             // ... (UPDATE payments table) ...
             // ... (UPDATE transactions table) ...

             // --- MODIFICATION: Credit tokens AND Send Email on Complete ---
             if ($finalDbStatus === 'Complete' && ($payRowsAffected > 0 || $txRowsAffected > 0)) {
                  // Get payment data (user_id, tokens, amount, currency)
                  $stmtGetData = $this->pdo->prepare("SELECT user_id, tokens, usd_amount, pay_currency FROM payments WHERE order_id = ?");
                  $stmtGetData->execute([$orderId]);
                  $paymentData = $stmtGetData->fetch();

                  if ($paymentData && $paymentData['tokens'] > 0) {
                       $userId = $paymentData['user_id'];
                       $purchasedTokenAmount = $paymentData['tokens'];
                       $usdAmountPaid = $paymentData['usd_amount']; // Get USD amount
                       $paidCurrency = $paymentData['pay_currency'] ?? $actualPayCurrency ?? 'N/A'; // Get paid currency

                       // Update user's token balance
                       $stmtUser = $this->pdo->prepare("UPDATE users SET tokens = tokens + ? WHERE id = ?");
                       $updatedUser = $stmtUser->execute([$purchasedTokenAmount, $userId]);
                       error_log("IPN Credited Purchased Tokens (User: $userId, Order: $orderId): " . ($updatedUser ? 'Success' : 'FAILURE'));

                       // --- NEW: Fetch User Details & Send Confirmation Email ---
                       if ($updatedUser) { // Only send if token update was successful
                           $stmtGetUser = $this->pdo->prepare("SELECT username, email FROM users WHERE id = ?");
                           $stmtGetUser->execute([$userId]);
                           $userData = $stmtGetUser->fetch();

                           if ($userData) {
                               $this->send_purchase_confirmation_email(
                                   $userData['email'],
                                   $userData['username'],
                                   $usdAmountPaid,
                                   $purchasedTokenAmount,
                                   $orderId,
                                   $paidCurrency
                               );
                               error_log("Purchase confirmation email initiated for User: $userId, Order: $orderId");
                           } else {
                               error_log("IPN Email Error: Could not fetch user data for User ID: $userId to send confirmation.");
                           }
                       }
                       // --- END NEW ---

                  } else { error_log("IPN Error: Could not find payment data or token amount for Order ID: " . $orderId . " during Complete step."); }
             }
             // --- END MODIFICATION ---


             if (($payRowsAffected > 0 || $txRowsAffected > 0)) {
                 $this->pdo->commit();
                 error_log("IPN Processed: Order $orderId updated to $finalDbStatus. Rows affected (Pay/Tx): $payRowsAffected/$txRowsAffected");
                 echo "IPN OK: Processed.";
             } else {
                 $this->pdo->rollBack();
                 // ... (No update needed log) ...
                 echo "IPN OK: No update needed or order mismatch.";
             }
             exit();
         } catch (\Exception $e) { /* ... error handling ... */ exit('IPN Error: Server exception.');}
    }

    // --- Other User Functions ---

    // --- MODIFIED: getDetailedBalance ---
    /** Fetches detailed token balances for the logged-in user. */
    public function getDetailedBalance() {
        if (!isset($_SESSION['user_id'])) { 
            return ['success' => false, 'message' => 'Not authenticated.']; 
        }
        $userId = $_SESSION['user_id'];
        
        try {
            // Fetch the three balance types
            $stmt = $this->pdo->prepare("SELECT tokens, bonus_tokens, referral_tokens FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $balances = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($balances) {
                $purchased = (float)($balances['tokens'] ?? 0.00);
                $bonus = (float)($balances['bonus_tokens'] ?? 0.00);
                $referral = (float)($balances['referral_tokens'] ?? 0.00);

                // Calculate total and withdrawable balances
                $total_balance = $purchased + $bonus + $referral;
                $withdrawable_balance = $purchased + $bonus; // Only purchased and bonus are withdrawable

                return [
                    'success' => true,
                    'balances' => [
                        'purchased' => number_format($purchased, 2, '.', ','),
                        'bonus' => number_format($bonus, 2, '.', ','),
                        'referral' => number_format($referral, 2, '.', ','),
                        'total' => number_format($total_balance, 2, '.', ','),
                        'withdrawable' => number_format($withdrawable_balance, 2, '.', ',')
                    ]
                ];
            } else {
                return ['success' => false, 'message' => 'User not found.'];
            }
        } catch (PDOException $e) {
            error_log("Get Detailed Balance failed for user $userId: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error fetching balances.'];
        }
    }

    // --- getBalance (Kept for potential navbar use - returns TOTAL balance) ---
    public function getBalance() {
        if (!isset($_SESSION['user_id'])) { return ['success' => false, 'message' => 'Not auth.']; } 
        $userId = $_SESSION['user_id'];
        try { 
            // Calculate total on the fly
            $stmt = $this->pdo->prepare("SELECT (tokens + bonus_tokens + referral_tokens) as total_tokens FROM users WHERE id = ?"); 
            $stmt->execute([$userId]); 
            $data = $stmt->fetch();
            if ($data) { 
                return ['success' => true, 'balance' => number_format((float)$data['total_tokens'], 2, '.', ',')]; 
            } else { 
                return ['success' => false, 'message' => 'User not found.']; 
            }
        } catch (PDOException $e) { error_log("Get Balance failed: " . $e->getMessage()); return ['success' => false, 'message' => 'DB error.']; }
    }
    
    // --- getTransactionHistory (No changes needed) ---
    public function getTransactionHistory() {
        if (!isset($_SESSION['user_id'])) { return ['success' => false, 'message' => 'Not auth.']; } $userId = $_SESSION['user_id'];
        try { $stmt = $this->pdo->prepare("SELECT id, type, amount, status, created_at, details FROM transactions WHERE user_id = ? ORDER BY created_at DESC"); $stmt->execute([$userId]); $history = $stmt->fetchAll();
            return ['success' => true, 'history' => $history];
        } catch (PDOException $e) { error_log("Tx history failed: " . $e->getMessage()); return ['success' => false, 'message' => 'DB error.']; }
    }
    
    // --- getReferralHistory (No changes needed) ---
    public function getReferralHistory() {
        if (!isset($_SESSION['user_id'])) { return ['success' => false, 'message' => 'Not auth.']; } $userId = $_SESSION['user_id'];
        try { 
            // Calculate earnings based on referral_tokens column now
            $stmtUser = $this->pdo->prepare("SELECT referral_tokens FROM users WHERE id = ?");
            $stmtUser->execute([$userId]);
            $userData = $stmtUser->fetch();
            $totalEarnings = $userData ? (float)$userData['referral_tokens'] : 0.00;

            $stmtStats = $this->pdo->prepare("SELECT COUNT(id) as total_referrals FROM users WHERE referrer_id = ?"); 
            $stmtStats->execute([$userId]); 
            $stats = $stmtStats->fetch();
            $totalReferrals = $stats['total_referrals'] ?? 0; 
            
            $stmtHistory = $this->pdo->prepare("SELECT username, created_at FROM users WHERE referrer_id = ? ORDER BY created_at DESC"); $stmtHistory->execute([$userId]); $history = $stmtHistory->fetchAll();
            return ['success' => true, 'stats' => ['total_referrals' => (int)$totalReferrals, 'total_earnings' => number_format($totalEarnings, 2, '.', ',')], 'history' => $history];
        } catch (PDOException $e) { error_log("Ref history failed: " . $e->getMessage()); return ['success' => false, 'message' => 'DB error.']; }
    }
    
    // --- getUserDetails (No changes needed, already returns required user info) ---
     public function getUserDetails() {
         if (!isset($_SESSION['user_id'])) { return ['success' => false, 'message' => 'Not auth.']; } $userId = $_SESSION['user_id'];
         try { $stmtUser = $this->pdo->prepare("SELECT id, username, email, created_at, referrer_id FROM users WHERE id = ?"); $stmtUser->execute([$userId]); $user = $stmtUser->fetch();
             if (!$user) { return ['success' => false, 'message' => 'User not found.']; }
             $stmtMetrics = $this->pdo->prepare("SELECT COALESCE(SUM(CASE WHEN type IN ('PURCHASE', 'SIGNUP', 'REFERRAL', 'ADJUST_IN') AND status='Complete' THEN amount ELSE 0 END), 0) AS total_acquired, COALESCE(SUM(CASE WHEN type = 'REFERRAL' AND status='Complete' THEN amount ELSE 0 END), 0) AS total_ref_earned, COALESCE(COUNT(CASE WHEN type = 'PURCHASE' AND status = 'Complete' THEN 1 ELSE NULL END), 0) AS total_purchases FROM transactions WHERE user_id = ?"); 
             $stmtMetrics->execute([$userId]); 
             $metrics = $stmtMetrics->fetch();
             $stmtRecentTx = $this->pdo->prepare("SELECT id, type, amount, status, created_at, details FROM transactions WHERE user_id = ? ORDER BY created_at DESC LIMIT 5"); 
             $stmtRecentTx->execute([$userId]); 
             $recentTransactions = $stmtRecentTx->fetchAll();
             $totalAcquired = (float)($metrics['total_acquired'] ?? 0); $totalRefEarned = (float)($metrics['total_ref_earned'] ?? 0); $totalPurchases = (int)($metrics['total_purchases'] ?? 0);
             $referrerIdValue = $user['referrer_id'] ? $user['referrer_id'] : null;
             return ['success' => true, 
                'details' => [
                    'id' => $user['id'], 'username' => $user['username'], 'email' => $user['email'], 
                    'member_since' => date('M d, Y', strtotime($user['created_at'])), 
                    'referrer_id' => $referrerIdValue, 
                    'stats' => [
                        'tokens_acquired' => number_format($totalAcquired, 2, '.', ','), 
                        'total_purchases' => $totalPurchases, 
                        'referral_earnings' => number_format($totalRefEarned, 2, '.', ',')
                    ]
                ], 
                'recent_transactions' => $recentTransactions
             ];
         } catch (PDOException $e) { error_log("User details failed: " . $e->getMessage()); return ['success' => false, 'message' => 'DB error fetching details.']; }
    }

    // --- MODIFIED: withdrawTokens ---
    /** Handles token withdrawal request, checking against withdrawable balance. */
    public function withdrawTokens($data) {
        if (!isset($_SESSION['user_id'])) { 
            return ['success' => false, 'message' => 'Authentication required.']; 
        }
        $userId = $_SESSION['user_id'];
        $amount = filter_var($data['amount'], FILTER_VALIDATE_FLOAT);
        $walletAddress = filter_var($data['wallet_address'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        // Basic validation
        if (!$amount || $amount <= 0 || empty($walletAddress) || strlen($walletAddress) < 20) { 
            return ['success' => false, 'message' => 'Invalid amount or wallet address provided.']; 
        }

        try {
            $this->pdo->beginTransaction();

            // Lock the user row and get current balances
            $stmtCheck = $this->pdo->prepare("SELECT tokens, bonus_tokens FROM users WHERE id = ? FOR UPDATE");
            $stmtCheck->execute([$userId]);
            $userBalances = $stmtCheck->fetch(PDO::FETCH_ASSOC);

            if (!$userBalances) {
                $this->pdo->rollBack();
                return ['success' => false, 'message' => 'User not found.'];
            }

            $purchasedTokens = (float)$userBalances['tokens'];
            $bonusTokens = (float)$userBalances['bonus_tokens'];
            $withdrawableBalance = $purchasedTokens + $bonusTokens;

            // Check if withdrawable balance is sufficient
            if ($withdrawableBalance < $amount) {
                $this->pdo->rollBack();
                return ['success' => false, 'message' => 'Insufficient withdrawable funds. Referral tokens cannot be withdrawn. Available: ' . number_format($withdrawableBalance, 2)];
            }

            // Determine how much to deduct from each balance (bonus first)
            $deductFromBonus = min($amount, $bonusTokens);
            $deductFromPurchased = $amount - $deductFromBonus;

            // Update user balances
            $stmtUpdate = $this->pdo->prepare("UPDATE users SET bonus_tokens = bonus_tokens - ?, tokens = tokens - ? WHERE id = ?");
            $stmtUpdate->execute([$deductFromBonus, $deductFromPurchased, $userId]);

            // Log the withdrawal transaction
            $withdrawalAmountDb = -abs($amount); // Store withdrawals as negative
            $details = "Withdrawal Request. To: " . $walletAddress;
            $stmtLog = $this->pdo->prepare("INSERT INTO transactions (user_id, type, amount, status, details) VALUES (?, 'WITHDRAWAL', ?, 'Processing', ?)");
            $stmtLog->execute([$userId, $withdrawalAmountDb, $details]);

            $this->pdo->commit();
            return ['success' => true, 'message' => 'Withdrawal requested successfully! Processing typically takes 24-48 hours.'];

        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Withdrawal failed for user $userId: " . $e->getMessage());
            return ['success' => false, 'message' => 'A database error occurred during the withdrawal process. Please try again later.'];
        }
    }

} // End Class