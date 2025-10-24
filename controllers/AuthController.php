<?php
namespace Controllers;

require_once(__DIR__ . '/../config/config.php');
require_once(__DIR__ . '/../core/Database.php');
// Require PHPMailer autoload (adjust path if your vendor dir is elsewhere)
require_once(__DIR__ . '/../vendor/autoload.php');

// Import PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

use Core\Database;
use PDO;
use PDOException;

class AuthController {
    private $pdo;

    public function __construct($start_session = true) {
        $this->pdo = Database::getInstance()->getConnection();
        if ($start_session && session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    // --- Email Sending Helper ---
    private function sendEmail($toEmail, $toName, $subject, $htmlBody, $plainBody = '') {
        $mail = new PHPMailer(true);
        try {
            // Server settings
            // $mail->SMTPDebug = SMTP::DEBUG_SERVER; // Enable verbose debug output for testing
            $mail->isSMTP();
            $mail->Host       = SMTP_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = SMTP_USERNAME;
            $mail->Password   = SMTP_PASSWORD;
            $mail->SMTPSecure = SMTP_SECURE; // e.g., PHPMailer::ENCRYPTION_STARTTLS
            $mail->Port       = SMTP_PORT;

            // Recipients
            $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
            $mail->addAddress($toEmail, $toName);

            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $htmlBody;
            $mail->AltBody = $plainBody ?: strip_tags($htmlBody);

            $mail->send();
            error_log("Email sent successfully to: " . $toEmail . " Subject: " . $subject);
            return true;
        } catch (Exception $e) {
            error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
            return false;
        }
    }

    // --- Generate OTP / Token Helper ---
    private function generateVerificationCode($length = 6) {
        // Simple numeric OTP
        return substr(str_shuffle(str_repeat('0123456789', $length)), 0, $length);
    }
     private function generateSecureToken($length = 32) {
         // Secure random token for password reset
         return bin2hex(random_bytes($length));
     }

     // --- Store Verification Code/Token ---
     private function storeVerification($email, $userId, $codeOrToken, $type, $minutesValid = 15) {
         $this->clearExpiredVerifications($email, $type); // Clear old ones first
         $expires = date('Y-m-d H:i:s', strtotime("+$minutesValid minutes"));
         try {
             $stmt = $this->pdo->prepare(
                 "INSERT INTO email_verifications (user_id, email, token, type, expires_at) VALUES (?, ?, ?, ?, ?)"
             );
             // Consider hashing $codeOrToken here for security if needed
             return $stmt->execute([$userId, $email, $codeOrToken, $type, $expires]);
         } catch (PDOException $e) {
             error_log("Failed to store verification code for $email: " . $e->getMessage());
             return false;
         }
     }

     // --- Verify Code/Token (and delete on success) ---
     private function verifyCode($emailOrToken, $code, $type) {
         // Allow verifying by token directly for password reset
         $field = ($type === 'password_reset') ? 'token' : 'email';
         $value = ($type === 'password_reset') ? $emailOrToken : $emailOrToken; // Use token if type is reset
         $codeToCheck = ($type === 'password_reset') ? $emailOrToken : $code; // Use token itself if type is reset

          try {
             $stmt = $this->pdo->prepare(
                 "SELECT id, user_id, email FROM email_verifications
                  WHERE $field = ? AND token = ? AND type = ? AND expires_at > NOW()"
             );
             $stmt->execute([$value, $codeToCheck, $type]);
             $verification = $stmt->fetch();

             if ($verification) {
                 $this->deleteVerification($verification['id']); // Delete upon successful verification
                 return $verification;
             }
             return false;
         } catch (PDOException $e) {
              error_log("Failed to verify code/token for $value ($type): " . $e->getMessage());
              return false;
         }
     }

      // --- Helpers to clean up used/expired codes ---
      private function deleteVerification($verificationId) {
          try {
              $stmt = $this->pdo->prepare("DELETE FROM email_verifications WHERE id = ?");
              $stmt->execute([$verificationId]);
          } catch (PDOException $e) {
               error_log("Failed to delete verification ID $verificationId: " . $e->getMessage());
          }
      }
      private function clearExpiredVerifications($email = null, $type = null) {
          try {
              $sql = "DELETE FROM email_verifications WHERE expires_at <= NOW()";
              $params = [];
              if ($email) { $sql .= " AND email = ?"; $params[] = $email; }
              if ($type) { $sql .= " AND type = ?"; $params[] = $type; }
              $stmt = $this->pdo->prepare($sql);
              $stmt->execute($params);
          } catch (PDOException $e) {
              error_log("Failed to clear expired verifications: " . $e->getMessage());
          }
      }

    // --- Registration Process ---

    /** Step 1: Request Registration OTP */
    public function requestRegisterOtp($data) {
        $email = filter_var($data['email'], FILTER_VALIDATE_EMAIL); $username = filter_var($data['username'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if (!$email || !$username) { return ['success' => false, 'message' => 'Username and Email required.']; }
        $stmt = $this->pdo->prepare("SELECT id FROM users WHERE (email = ? OR username = ?) AND is_email_verified = TRUE"); $stmt->execute([$email, $username]);
        if ($stmt->fetch()) { return ['success' => false, 'message' => 'Username or Email already verified.']; }
        $otp = $this->generateVerificationCode();
        if (!$this->storeVerification($email, null, $otp, 'register_otp', 10)) { return ['success' => false, 'message' => 'Could not store code. Try again.']; }
        $subject = "Your OTP for " . APP_NAME; $body = "<p>Hello " . htmlspecialchars($username) . ",</p><p>OTP: <strong>" . $otp . "</strong> (10 min validity)</p>";
        if ($this->sendEmail($email, $username, $subject, $body)) { return ['success' => true, 'message' => 'OTP sent. Check inbox/spam.']; }
        else { return ['success' => false, 'message' => 'Failed to send OTP email. Check server config.']; }
    }

    /** Step 2: Complete Registration with OTP */
    public function register($data) {
        $email = filter_var($data['email'], FILTER_VALIDATE_EMAIL); $username = filter_var($data['username'], FILTER_SANITIZE_FULL_SPECIAL_CHARS); $password = $data['password'] ?? ''; $otp = filter_var($data['otp'], FILTER_SANITIZE_NUMBER_INT); $refIdInput = $data['referrer_id'] ?? ''; $refId = null;
        if (!empty($refIdInput)) { $fId = filter_var($refIdInput, FILTER_VALIDATE_INT); if ($fId !== false && $fId > 0) { $refId = $fId; } }
        if (!$email || !$username || empty($password) || strlen($password) < 6 || empty($otp) || strlen($otp) !== 6) { return ['success' => false, 'message' => 'Invalid input. Check fields, password (min 6), OTP (6 digits).']; }
        $verification = $this->verifyCode($email, $otp, 'register_otp'); if (!$verification) { return ['success' => false, 'message' => 'Invalid or expired OTP.']; }
        $stmtCheck = $this->pdo->prepare("SELECT id FROM users WHERE email = ? OR username = ?"); $stmtCheck->execute([$email, $username]); if ($stmtCheck->fetch()) { return ['success' => false, 'message' => 'Username or Email already registered.']; }
        $passwordHash = password_hash($password, PASSWORD_DEFAULT); $initialTokens = KYC_BONUS;
        try {
            $this->pdo->beginTransaction();
            $stmt = $this->pdo->prepare("INSERT INTO users (username, email, password_hash, tokens, kyc_claimed, referrer_id, is_email_verified) VALUES (?, ?, ?, ?, TRUE, ?, TRUE)");
            $stmt->execute([$username, $email, $passwordHash, $initialTokens, $refId]); $newUserId = $this->pdo->lastInsertId(); if (!$newUserId) { $this->pdo->rollBack(); return ['success' => false, 'message' => 'Failed user creation.']; }
            if ($refId !== null && $refId != $newUserId) { $sR = $this->pdo->prepare("UPDATE users SET tokens = tokens + ? WHERE id = ?"); $sR->execute([REFERRAL_BONUS, $refId]); }
            $stmtLogSignup = $this->pdo->prepare("INSERT INTO transactions (user_id, type, amount, status, details) VALUES (?, 'SIGNUP', ?, 'Complete', ?)"); $stmtLogSignup->execute([$newUserId, $initialTokens, 'KYC & Email Verified Bonus']);
            if ($refId !== null && $refId != $newUserId) { $sLR = $this->pdo->prepare("INSERT INTO transactions (user_id, type, amount, status, details) VALUES (?, 'REFERRAL', ?, 'Complete', ?)"); $sLR->execute([$refId, REFERRAL_BONUS, 'Referral Reward for ID ' . $newUserId]); }
            $this->pdo->commit();
            $subjectWelcome = "Welcome to " . APP_NAME . "!"; $bodyWelcome = "<p>Hello " . htmlspecialchars($username) . ",</p><p>Welcome! Account active.</p><p>Bonus: " . number_format($initialTokens) . " GALAXY.</p><p>Login: <a href='" . SITE_URL . "'>" . SITE_URL . "</a></p>";
            $this->sendEmail($email, $username, $subjectWelcome, $bodyWelcome);
            $_SESSION['user_id'] = $newUserId; $_SESSION['username'] = $username;
            return ['success' => true, 'message' => 'Registration complete! Welcome email sent. Bonus added.'];
        } catch (PDOException $e) { if ($this->pdo->inTransaction()) { $this->pdo->rollBack(); } error_log("Registration failed: " . $e->getMessage()); return ['success' => false, 'message' => 'DB error during registration.']; }
    }

    // --- User Login ---
    public function login($data) {
        $loginId = $data['login_id'] ?? ''; $password = $data['password'] ?? '';
        if (empty($loginId) || empty($password)) { return ['success' => false, 'message' => 'Credentials required.']; }
        try {
            $stmt = $this->pdo->prepare("SELECT id, username, password_hash, is_email_verified FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$loginId, $loginId]); $user = $stmt->fetch();
            if ($user && password_verify($password, $user['password_hash'])) {
                 if (!$user['is_email_verified']) { return ['success' => false, 'message' => 'Email not verified. Check inbox or register again.']; }
                session_regenerate_id(true); $_SESSION['user_id'] = $user['id']; $_SESSION['username'] = $user['username'];
                return ['success' => true, 'message' => 'Login successful! Redirecting...'];
            } else { return ['success' => false, 'message' => 'Invalid username/email or password.']; }
        } catch (PDOException $e) { error_log("Login failed: " . $e->getMessage()); return ['success' => false, 'message' => 'DB error during login.']; }
    }

    // --- User Logout ---
    public function logout() {
        session_unset(); session_destroy();
        return ['success' => true, 'message' => 'Logged out successfully.'];
    }

    // --- Update Password (Profile) ---
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

    // --- Forgot Password Process ---

    /** Step 1: Request Password Reset Link */
    public function requestPasswordReset($data) {
        $email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
        if (!$email) { return ['success' => false, 'message' => 'Invalid email.']; }
        try {
            $stmt = $this->pdo->prepare("SELECT id, username FROM users WHERE email = ? AND is_email_verified = TRUE"); $stmt->execute([$email]); $user = $stmt->fetch();
            if ($user) {
                $token = $this->generateSecureToken();
                if ($this->storeVerification($email, $user['id'], $token, 'password_reset', 60)) {
                    $resetLink = SITE_URL . 'reset_password.php?token=' . urlencode($token); $subject = "Password Reset - " . APP_NAME;
                    $body = "<p>Hello " . htmlspecialchars($user['username']) . ",</p><p>Reset link (valid 60 min):</p><p><a href='" . $resetLink . "'>" . $resetLink . "</a></p><p>Ignore if not requested.</p>";
                    $this->sendEmail($email, $user['username'], $subject, $body);
                } else { error_log("Failed store reset token for $email"); }
            } return ['success' => true, 'message' => 'If account exists, reset link sent.']; // Security
        } catch (PDOException $e) { error_log("Reset request DB error: " . $e->getMessage()); return ['success' => true, 'message' => 'If account exists, reset link sent.']; }
    }

    /** Step 2: Verify Reset Token (Used by reset_password.php) */
     public function verifyResetToken($token) {
        if (empty($token)) return null;
        try { $stmt = $this->pdo->prepare("SELECT id, email, user_id FROM email_verifications WHERE token = ? AND type = 'password_reset' AND expires_at > NOW()"); $stmt->execute([$token]); return $stmt->fetch(); }
        catch (PDOException $e) { error_log("Verify reset token failed: " . $e->getMessage()); return null; }
     }

    /** Step 3: Reset Password using Token (Called by reset_password.php form) */
    public function resetPasswordWithToken($data) {
        $token = $data['token'] ?? ''; $newPassword = $data['new_password'] ?? ''; $confirmPassword = $data['confirm_password'] ?? '';
        if (empty($token) || empty($newPassword) || $newPassword !== $confirmPassword || strlen($newPassword) < 6) { return ['success' => false, 'message' => 'Invalid input. Check token, passwords (min 6).']; }
        $verification = $this->verifyCode($token, $token, 'password_reset'); // Verifies token and DELETES if valid
        if (!$verification || !$verification['user_id']) { return ['success' => false, 'message' => 'Invalid or expired reset token.']; }
        $userId = $verification['user_id'];
        try { $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT); $stmtUpdate = $this->pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?"); $updated = $stmtUpdate->execute([$newPasswordHash, $userId]);
            if ($updated) { return ['success' => true, 'message' => 'Password reset successful!']; }
            else { return ['success' => false, 'message' => 'Failed to update password.']; }
        } catch (PDOException $e) { error_log("Reset password DB error: " . $e->getMessage()); return ['success' => false, 'message' => 'DB error resetting password.']; }
    }

    // --- Payment Flow (NowPayments - Redirect Method + Email) ---

    /** [REDIRECT METHOD] Creates NowPayments invoice WITHOUT currency spec */
    public function createNowPaymentsInvoice($data) {
         if (!isset($_SESSION['user_id'])) { return ['success' => false, 'message' => 'Auth required.']; }
         $userId = $_SESSION['user_id']; $usdAmount = filter_var($data['usd_amount'], FILTER_VALIDATE_FLOAT); $bonusPercent = filter_var($data['bonus_percent'], FILTER_VALIDATE_FLOAT);
         if (!$usdAmount || $usdAmount <= 0) { return ['success' => false, 'message' => 'Invalid amount.']; }
         $baseTokens = $usdAmount * TOKEN_RATE; $totalTokens = $baseTokens + ($baseTokens * $bonusPercent);
         $orderId = $userId . '-' . time(); $description = "Purchase of " . number_format($totalTokens) . " GALAXY tokens";
         $transaction_id = null; $payment_record_id = null;
         try {
             $this->pdo->beginTransaction();
             // 1. Insert into transactions
             $stmtTx = $this->pdo->prepare("INSERT INTO transactions (user_id, type, amount, status, details) VALUES (?, 'PURCHASE', ?, 'Pending', ?)"); $stmtTx->execute([$userId, $totalTokens, $orderId]); $transaction_id = $this->pdo->lastInsertId();
             if (!$transaction_id) { $this->pdo->rollBack(); error_log("DB Error: Failed TX insert"); return ['success' => false, 'message' => 'DB error (TX).']; }
             // 2. Insert into payments (without pay_currency initially)
             $planKey = 'CUSTOM_'.$usdAmount;
             $stmtPay = $this->pdo->prepare("INSERT INTO payments (user_id, order_id, plan_key, usd_amount, tokens, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, 'Pending', NOW(), NOW())"); $stmtPay->execute([$userId, $orderId, $planKey, $usdAmount, $totalTokens]); $payment_record_id = $this->pdo->lastInsertId();
             if (!$payment_record_id) { $this->pdo->rollBack(); error_log("DB Error: Failed PAY insert"); return ['success' => false, 'message' => 'DB error (PAY).']; }
             // 3. Prepare payload WITHOUT pay_currency
             $payload = ['price_amount' => $usdAmount, 'price_currency' => 'usd', 'order_id' => $orderId, 'order_description' => $description, 'ipn_callback_url' => IPN_URL, 'success_url' => SITE_URL . '?p=wallet&payment=success&orderId=' . $orderId, 'cancel_url' => SITE_URL . '?p=dashboard&payment=cancelled'];
             // 4. Call NowPayments API
             $ch = curl_init(); /* ... cURL setup ... */ curl_setopt($ch, CURLOPT_URL, NOWPAYMENTS_API_URL . '/payment'); curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); curl_setopt($ch, CURLOPT_POST, 1); curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload)); curl_setopt($ch, CURLOPT_HTTPHEADER, ['x-api-key: ' . NOWPAYMENTS_API_KEY, 'Content-Type: application/json']);
             $response = curl_exec($ch); $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); $curl_error = curl_error($ch); curl_close($ch);
             // 5. Handle Response (Expect URL)
             if ($http_code == 201 || $http_code == 200) {
                 $responseData = json_decode($response, true);
                 $payment_url = $responseData['payment_url'] ?? $responseData['invoice_url'] ?? null;
                 if ($payment_url) {
                     $gatewayPaymentId = $responseData['payment_id'] ?? null;
                     if ($gatewayPaymentId && $payment_record_id) { $stmtUpdatePayId = $this->pdo->prepare("UPDATE payments SET gateway_payment_id = ?, updated_at = NOW() WHERE id = ?"); $stmtUpdatePayId->execute([$gatewayPaymentId, $payment_record_id]); }
                     $this->pdo->commit(); return ['success' => true, 'payment_url' => $payment_url]; // Return redirect URL
                 } else { $this->pdo->rollBack(); error_log("NP Response Missing URL: " . $response); return ['success' => false, 'message' => 'Gateway Error: Invalid structure: ' . $response]; }
             }
             // --- Error Handling ---
             $this->pdo->rollBack(); $errorData = json_decode($response, true); $errorMessage = $errorData['message'] ?? $response ?? "Unknown API error"; if (!empty($curl_error)) { $errorMessage = "cURL Error: " . $curl_error; } error_log("NP API Error: " . $errorMessage); return ['success' => false, 'message' => 'Gateway Error: ' . $errorMessage];
         } catch (PDOException $e) { if ($this->pdo->inTransaction()) { $this->pdo->rollBack(); } error_log("Create Invoice DB Error: " . $e->getMessage()); return ['success' => false, 'message' => 'Database error.']; }
    }

    // getPaymentStatus function REMOVED as polling is not used in redirect method

    /** [MODIFIED FOR EMAIL] Handles NowPayments IPN */
    public function handleNowPaymentsIPN() {
         error_log("====== IPN Handler Started ======");
         $headers = getallheaders(); $np_signature = $headers['X-Nowpayments-Sig'] ?? $headers['x-nowpayments-sig'] ?? '';
         if (empty($np_signature)) { error_log("IPN Error: No signature."); header('HTTP/1.1 400 Bad Request'); exit('IPN Error: No signature.'); } error_log("IPN Sig Received: " . $np_signature);
         $raw_body = file_get_contents('php://input'); if ($raw_body === false) { error_log("IPN Error: No body."); header('HTTP/1.1 400 Bad Request'); exit('IPN Error: No body.'); } error_log("IPN Raw Body: " . $raw_body);
         try {
             $hmac = hash_hmac('sha512', $raw_body, NOWPAYMENTS_IPN_SECRET); error_log("IPN Calculated HMAC: " . $hmac);
             $signature_valid = hash_equals($hmac, $np_signature); error_log("IPN Sig Valid: " . ($signature_valid ? 'Yes' : 'No'));
             if (!$signature_valid) { error_log("IPN Error: Invalid signature."); header('HTTP/1.1 401 Unauthorized'); exit('IPN Error: Invalid signature.'); }
             $data = json_decode($raw_body, true); if ($data === null) { error_log("IPN Error: Invalid JSON."); header('HTTP/1.1 400 Bad Request'); exit('IPN Error: Invalid JSON.'); } error_log("IPN Data: " . print_r($data, true));
             $payment_status = $data['payment_status'] ?? 'unknown'; $orderId = $data['order_id'] ?? null; $gatewayPaymentId = $data['payment_id'] ?? null; $actualPayCurrency = $data['pay_currency'] ?? null; $actualPayAmount = $data['pay_amount'] ?? null;
             error_log("IPN Status: " . $payment_status . " for Order ID: " . $orderId . ", Gateway ID: " . $gatewayPaymentId);
             if (!$orderId) { error_log("IPN Error: Missing order_id."); echo "IPN OK: Missing order_id."; exit(); }
             $finalDbStatus = 'Pending';
             if ($payment_status === 'finished') { $finalDbStatus = 'Complete'; }
             else if (in_array($payment_status, ['confirmed', 'sending', 'partially_paid'])) { $finalDbStatus = 'Processing'; }
             else if (in_array($payment_status, ['failed', 'refunded', 'expired'])) { $finalDbStatus = 'Failed'; }
             $this->pdo->beginTransaction();
             $sqlPay = "UPDATE payments SET status = ?, gateway_payment_id = ?, updated_at = NOW()"; $paramsPay = [$finalDbStatus, $gatewayPaymentId]; if ($actualPayCurrency) { $sqlPay .= ", pay_currency = IFNULL(pay_currency, ?)"; $paramsPay[] = $actualPayCurrency; } $sqlPay .= " WHERE order_id = ? AND status IN ('Pending', 'Processing')"; $paramsPay[] = $orderId;
             $stmtUpdatePay = $this->pdo->prepare($sqlPay); $updatedPay = $stmtUpdatePay->execute($paramsPay); $payRowsAffected = $stmtUpdatePay->rowCount(); error_log("IPN Updated payments: Rows = " . $payRowsAffected);
             $stmtUpdateTx = $this->pdo->prepare("UPDATE transactions SET status = ? WHERE details = ? AND status IN ('Pending', 'Processing')"); $updatedTx = $stmtUpdateTx->execute([$finalDbStatus, $orderId]); $txRowsAffected = $stmtUpdateTx->rowCount(); error_log("IPN Updated transactions: Rows = " . $txRowsAffected);
             $emailSent = false;
             if ($finalDbStatus === 'Complete' && ($payRowsAffected > 0 || $txRowsAffected > 0)) {
                  $stmtGetData = $this->pdo->prepare("SELECT p.user_id, p.tokens, p.usd_amount, u.email, u.username FROM payments p JOIN users u ON p.user_id = u.id WHERE p.order_id = ?"); $stmtGetData->execute([$orderId]); $paymentData = $stmtGetData->fetch();
                  if ($paymentData && $paymentData['tokens'] > 0) {
                       $userId = $paymentData['user_id']; $tokenAmount = $paymentData['tokens']; $usdAmount = $paymentData['usd_amount']; $email = $paymentData['email']; $username = $paymentData['username'];
                       $stmtUser = $this->pdo->prepare("UPDATE users SET tokens = tokens + ? WHERE id = ?"); $updatedUser = $stmtUser->execute([$tokenAmount, $userId]); error_log("IPN Credited Tokens (User: $userId): " . ($updatedUser ? 'Success' : 'FAILURE'));
                       if ($updatedUser) {
                            $subjectPurchase = "Purchase Confirmation - " . APP_NAME; $bodyPurchase = "<p>Hello " . htmlspecialchars($username) . ",</p><p>Your purchase of <strong>" . number_format($tokenAmount) . " GALAXY</strong> ($" . number_format($usdAmount, 2) . ") is complete!</p><p>Tokens added.</p><p>Order ID: " . htmlspecialchars($orderId) . "</p><p>Paid: ~" . ($actualPayAmount ?? 'N/A') . " " . ($actualPayCurrency ? strtoupper($actualPayCurrency) : '') . "</p>"; $emailSent = $this->sendEmail($email, $username, $subjectPurchase, $bodyPurchase);
                       }
                  } else { error_log("IPN Error: Could not find payment/user data for Order ID: " . $orderId); }
             }
             if (($payRowsAffected > 0 || $txRowsAffected > 0)) { $this->pdo->commit(); error_log("IPN DB Committed for Order ID: " . $orderId); echo "IPN OK: Processed."; }
             else { $this->pdo->rollBack(); error_log("IPN DB Rollback: No rows affected for Order ID: " . $orderId); echo "IPN OK: No update."; }
             exit();
         } catch (\Exception $e) { error_log("IPN CRITICAL Error: " . $e->getMessage()); if ($this->pdo->inTransaction()) { $this->pdo->rollBack(); error_log("IPN Rolled back DB due to exception."); } header('HTTP/1.1 500 Internal Server Error'); exit('IPN Error: Server exception.');}
    }


    // --- Other User Functions ---
    public function getBalance() {
        if (!isset($_SESSION['user_id'])) { return ['success' => false, 'message' => 'Not auth.']; } $userId = $_SESSION['user_id'];
        try { $stmt = $this->pdo->prepare("SELECT tokens FROM users WHERE id = ?"); $stmt->execute([$userId]); $data = $stmt->fetch();
            if ($data) { return ['success' => true, 'balance' => number_format((float)$data['tokens'], 2, '.', ',')]; } else { return ['success' => false, 'message' => 'User not found.']; }
        } catch (PDOException $e) { error_log("Get Balance failed: " . $e->getMessage()); return ['success' => false, 'message' => 'DB error.']; }
    }
    public function getTransactionHistory() {
        if (!isset($_SESSION['user_id'])) { return ['success' => false, 'message' => 'Not auth.']; } $userId = $_SESSION['user_id'];
        try { $stmt = $this->pdo->prepare("SELECT id, type, amount, status, created_at, details FROM transactions WHERE user_id = ? ORDER BY created_at DESC"); $stmt->execute([$userId]); $history = $stmt->fetchAll();
            return ['success' => true, 'history' => $history];
        } catch (PDOException $e) { error_log("Tx history failed: " . $e->getMessage()); return ['success' => false, 'message' => 'DB error.']; }
    }
    public function getReferralHistory() {
        if (!isset($_SESSION['user_id'])) { return ['success' => false, 'message' => 'Not auth.']; } $userId = $_SESSION['user_id'];
        try { $stmtStats = $this->pdo->prepare("SELECT COUNT(id) as total_referrals FROM users WHERE referrer_id = ?"); $stmtStats->execute([$userId]); $stats = $stmtStats->fetch();
            $totalReferrals = $stats['total_referrals'] ?? 0; $totalEarnings = $totalReferrals * REFERRAL_BONUS;
            $stmtHistory = $this->pdo->prepare("SELECT username, created_at FROM users WHERE referrer_id = ? ORDER BY created_at DESC"); $stmtHistory->execute([$userId]); $history = $stmtHistory->fetchAll();
            return ['success' => true, 'stats' => ['total_referrals' => (int)$totalReferrals, 'total_earnings' => number_format($totalEarnings, 2, '.', ',')], 'history' => $history];
        } catch (PDOException $e) { error_log("Ref history failed: " . $e->getMessage()); return ['success' => false, 'message' => 'DB error.']; }
    }
    public function getUserDetails() {
         if (!isset($_SESSION['user_id'])) { return ['success' => false, 'message' => 'Not auth.']; } $userId = $_SESSION['user_id'];
         try { $stmtUser = $this->pdo->prepare("SELECT id, username, email, created_at, referrer_id FROM users WHERE id = ?"); $stmtUser->execute([$userId]); $user = $stmtUser->fetch();
             if (!$user) { return ['success' => false, 'message' => 'User not found.']; }
             $stmtMetrics = $this->pdo->prepare("SELECT COALESCE(SUM(CASE WHEN type IN ('PURCHASE', 'SIGNUP', 'REFERRAL', 'ADJUST_IN') AND status='Complete' THEN amount ELSE 0 END), 0) AS total_acquired, COALESCE(SUM(CASE WHEN type = 'REFERRAL' AND status='Complete' THEN amount ELSE 0 END), 0) AS total_ref_earned, COALESCE(COUNT(CASE WHEN type = 'PURCHASE' AND status = 'Complete' THEN 1 ELSE NULL END), 0) AS total_purchases FROM transactions WHERE user_id = ?"); $stmtMetrics->execute([$userId]); $metrics = $stmtMetrics->fetch();
             $stmtRecentTx = $this->pdo->prepare("SELECT type, amount, status, created_at FROM transactions WHERE user_id = ? ORDER BY created_at DESC LIMIT 5"); $stmtRecentTx->execute([$userId]); $recentTransactions = $stmtRecentTx->fetchAll();
             $totalAcquired = (float)($metrics['total_acquired'] ?? 0); $totalRefEarned = (float)($metrics['total_ref_earned'] ?? 0); $totalPurchases = (int)($metrics['total_purchases'] ?? 0);
             return ['success' => true, 'details' => ['id' => $user['id'], 'username' => $user['username'], 'email' => $user['email'], 'member_since' => date('M d, Y', strtotime($user['created_at'])), 'referrer_id' => $user['referrer_id'] ? 'User ID: ' . $user['referrer_id'] : 'None', 'stats' => ['tokens_acquired' => number_format($totalAcquired, 2, '.', ','), 'total_purchases' => $totalPurchases, 'referral_earnings' => number_format($totalRefEarned, 2, '.', ',')]], 'recent_transactions' => $recentTransactions];
         } catch (PDOException $e) { error_log("User details failed: " . $e->getMessage()); return ['success' => false, 'message' => 'DB error.']; }
    }
    public function withdrawTokens($data) {
        if (!isset($_SESSION['user_id'])) { return ['success' => false, 'message' => 'Auth required.']; }
        $userId = $_SESSION['user_id']; $amount = filter_var($data['amount'], FILTER_VALIDATE_FLOAT); $walletAddress = filter_var($data['wallet_address'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if (!$amount || $amount <= 0 || empty($walletAddress) || strlen($walletAddress) < 20) { return ['success' => false, 'message' => 'Invalid amount or address.']; }
        $withdrawalAmount = -abs($amount); $details = "Withdrawal Request. To: " . $walletAddress;
        try {
            $this->pdo->beginTransaction(); $stmtCheck = $this->pdo->prepare("SELECT tokens FROM users WHERE id = ? FOR UPDATE"); $stmtCheck->execute([$userId]); $user = $stmtCheck->fetch();
            if (!$user || $user['tokens'] < $amount) { $this->pdo->rollBack(); return ['success' => false, 'message' => 'Insufficient funds.']; }
            $stmtUpdate = $this->pdo->prepare("UPDATE users SET tokens = tokens - ? WHERE id = ?"); $stmtUpdate->execute([$amount, $userId]);
            $stmtLog = $this->pdo->prepare("INSERT INTO transactions (user_id, type, amount, status, details) VALUES (?, 'WITHDRAWAL', ?, 'Processing', ?)"); $stmtLog->execute([$userId, $withdrawalAmount, $details]);
            $this->pdo->commit(); return ['success' => true, 'message' => 'Withdrawal requested! Processing takes 24-48h.'];
        } catch (PDOException $e) { $this->pdo->rollBack(); error_log("Withdrawal failed: " . $e->getMessage()); return ['success' => false, 'message' => 'DB error during withdrawal.']; }
    }

} // End Class