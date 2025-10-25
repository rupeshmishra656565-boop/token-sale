<?php
namespace Controllers;

require_once(__DIR__ . '/../config/config.php');
require_once(__DIR__ . '/../core/Database.php');
// PHPMailer 'require' and 'use' statements removed

use Core\Database;
use PDO;
use PDOException;

class AuthController {
    private $pdo;

    // Reverted constructor
    public function __construct($start_session = true) {
        $this->pdo = Database::getInstance()->getConnection();
        if ($start_session && session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    // --- [REVERTED] Original Registration Process ---
    public function register($data) {
        $email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
        $username = filter_var($data['username'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $password = $data['password'] ?? '';
        $refIdInput = $data['referrer_id'] ?? '';
        $refId = null;

        if (!empty($refIdInput)) {
            $filteredId = filter_var($refIdInput, FILTER_VALIDATE_INT);
            if ($filteredId !== false && $filteredId > 0) {
                $refId = $filteredId;
            }
        }

        if (!$email || !$username || empty($password) || strlen($password) < 6) {
            return ['success' => false, 'message' => 'Invalid input. Check fields and password length (min 6).'];
        }

        $stmt = $this->pdo->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
        $stmt->execute([$email, $username]);
        if ($stmt->fetch()) {
            return ['success' => false, 'message' => 'Username or Email already taken.'];
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $initialTokens = KYC_BONUS;

        try {
            $this->pdo->beginTransaction();
            
            // Note: is_email_verified column is not used
            $stmt = $this->pdo->prepare("INSERT INTO users (username, email, password_hash, tokens, kyc_claimed, referrer_id) VALUES (?, ?, ?, ?, TRUE, ?)");
            $stmt->execute([$username, $email, $passwordHash, $initialTokens, $refId]);
            $newUserId = $this->pdo->lastInsertId();

            if ($refId !== null && $refId != $newUserId) { 
                $stmtRef = $this->pdo->prepare("UPDATE users SET tokens = tokens + ? WHERE id = ?");
                $stmtRef->execute([REFERRAL_BONUS, $refId]);
            }
            
            // Log initial SIGNUP bonus
            $stmtLogSignup = $this->pdo->prepare("INSERT INTO transactions (user_id, type, amount, status, details) VALUES (?, 'SIGNUP', ?, 'Complete', ?)");
            $stmtLogSignup->execute([$newUserId, KYC_BONUS, 'KYC Verified Bonus']);

            // Log REFERRAL bonus for the referrer
            if ($refId !== null && $refId != $newUserId) { 
                 $stmtLogRef = $this->pdo->prepare("INSERT INTO transactions (user_id, type, amount, status, details) VALUES (?, 'REFERRAL', ?, 'Complete', ?)");
                 $stmtLogRef->execute([$refId, REFERRAL_BONUS, 'Referral Reward for ID ' . $newUserId]);
            }

            $this->pdo->commit();
            
            // No welcome email sent
            
            $_SESSION['user_id'] = $newUserId;
            $_SESSION['username'] = $username;
            return ['success' => true, 'message' => 'Registration successful! You received ' . number_format(KYC_BONUS) . ' GALAXY tokens.'];

        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Registration failed: " . $e->getMessage());
            return ['success' => false, 'message' => 'Registration failed due to a database error.'];
        }
    }

    // --- [REVERTED] User Login ---
    public function login($data) {
        $loginId = $data['login_id'] ?? ''; $password = $data['password'] ?? '';
        if (empty($loginId) || empty($password)) { return ['success' => false, 'message' => 'Credentials required.']; }
        try {
            // Removed is_email_verified check
            $stmt = $this->pdo->prepare("SELECT id, username, password_hash FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$loginId, $loginId]); $user = $stmt->fetch();
            if ($user && password_verify($password, $user['password_hash'])) {
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

    // --- Password Reset Functions REMOVED ---

    // --- Payment Flow (Display Details Method) ---

    /** [DISPLAY DETAILS METHOD] Creates NowPayments request, logs, returns details */
    public function createNowPaymentsInvoice($data) {
        if (!isset($_SESSION['user_id'])) { return ['success' => false, 'message' => 'Auth required.']; }
        $userId = $_SESSION['user_id']; $usdAmount = filter_var($data['usd_amount'], FILTER_VALIDATE_FLOAT); $bonusPercent = filter_var($data['bonus_percent'], FILTER_VALIDATE_FLOAT); $payCurrency = filter_var($data['pay_currency'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $planKey = 'CUSTOM'; if ($usdAmount == 15 && $bonusPercent == 0) $planKey = 'STARTER'; else if ($usdAmount == 100 && $bonusPercent == 0.25) $planKey = 'PRO'; else if ($usdAmount == 50 && $bonusPercent == 0.10) $planKey = 'ENTERPRISE';
        if (!$usdAmount || $usdAmount <= 0) { return ['success' => false, 'message' => 'Invalid amount.']; } if (empty($payCurrency)) { return ['success' => false, 'message' => 'No currency selected.']; }
        $allowed_currencies = ['usdttrc20', 'usdtbsc', 'trx']; if (!in_array($payCurrency, $allowed_currencies)) { return ['success' => false, 'message' => 'Invalid currency.']; }
        $baseTokens = $usdAmount * TOKEN_RATE; $totalTokens = $baseTokens + ($baseTokens * $bonusPercent); $orderId = $userId . '-' . time(); $description = "Purchase of " . number_format($totalTokens) . " GALAXY tokens";
        $transaction_id = null; $payment_record_id = null;
        try {
            $this->pdo->beginTransaction();
            $stmtTx = $this->pdo->prepare("INSERT INTO transactions (user_id, type, amount, status, details) VALUES (?, 'PURCHASE', ?, 'Pending', ?)"); $stmtTx->execute([$userId, $totalTokens, $orderId]); $transaction_id = $this->pdo->lastInsertId(); if (!$transaction_id) { $this->pdo->rollBack(); return ['success' => false, 'message' => 'DB error (TX).']; }
            $stmtPay = $this->pdo->prepare("INSERT INTO payments (user_id, order_id, plan_key, usd_amount, tokens, pay_currency, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, 'Pending', NOW(), NOW())"); $stmtPay->execute([$userId, $orderId, $planKey, $usdAmount, $totalTokens, $payCurrency]); $payment_record_id = $this->pdo->lastInsertId(); if (!$payment_record_id) { $this->pdo->rollBack(); return ['success' => false, 'message' => 'DB error (PAY).']; }
            $payload = ['price_amount' => $usdAmount, 'price_currency' => 'usd', 'pay_currency' => $payCurrency, 'order_id' => $orderId, 'order_description' => $description, 'ipn_callback_url' => IPN_URL, 'success_url' => SITE_URL . '?p=wallet&payment=success&orderId=' . $orderId, 'cancel_url' => SITE_URL . '?p=dashboard&payment=cancelled'];
            $ch = curl_init(); /* ... cURL setup ... */ curl_setopt($ch, CURLOPT_URL, NOWPAYMENTS_API_URL . '/payment'); curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); curl_setopt($ch, CURLOPT_POST, 1); curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload)); curl_setopt($ch, CURLOPT_HTTPHEADER, ['x-api-key: ' . NOWPAYMENTS_API_KEY, 'Content-Type: application/json']);
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
            $this->pdo->rollBack(); $errorData = json_decode($response, true); $errorMessage = $errorData['message'] ?? $response ?? "API error"; if (!empty($curl_error)) { $errorMessage = "cURL Error: " . $curl_error; } error_log("NP API Error: " . $errorMessage); return ['success' => false, 'message' => 'Gateway Error: ' . $errorMessage];
        } catch (PDOException $e) { if ($this->pdo->inTransaction()) { $this->pdo->rollBack(); } error_log("Create Invoice DB Error: " . $e->getMessage()); return ['success' => false, 'message' => 'Database error.']; }
    }

    /** [ADDED] Gets payment status from OUR transactions table for polling */
    public function getPaymentStatus($data) {
        if (!isset($_SESSION['user_id'])) { return ['success' => false, 'message' => 'Auth required.']; } $userId = $_SESSION['user_id']; $transactionId = filter_var($data['transaction_id'] ?? null, FILTER_VALIDATE_INT);
        if (!$transactionId) { return ['success' => false, 'message' => 'Invalid Tx ID.']; }
        try { $stmt = $this->pdo->prepare("SELECT status FROM transactions WHERE id = ? AND user_id = ?"); $stmt->execute([$transactionId, $userId]); $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) { return ['success' => true, 'status' => $result['status']]; } else { return ['success' => false, 'message' => 'Tx not found.']; }
        } catch (PDOException $e) { error_log("Get Status DB Error: " . $e->getMessage()); return ['success' => false, 'message' => 'DB error.']; }
    }

    /** [REVERTED] Handles NowPayments IPN (No Email) */
    public function handleNowPaymentsIPN() {
         error_log("====== IPN Handler Started ======");
         $headers = getallheaders(); $np_signature = $headers['X-Nowpayments-Sig'] ?? $headers['x-nowpayments-sig'] ?? '';
         if (empty($np_signature)) { error_log("IPN Error: No signature."); header('HTTP/1.1 400 Bad Request'); exit('IPN Error: No signature.'); }
         $raw_body = file_get_contents('php://input'); if ($raw_body === false) { error_log("IPN Error: No body."); header('HTTP/1.1 400 Bad Request'); exit('IPN Error: No body.'); }
         try {
             $hmac = hash_hmac('sha512', $raw_body, NOWPAYMENTS_IPN_SECRET);
             $signature_valid = hash_equals($hmac, $np_signature);
             if (!$signature_valid) { error_log("IPN Error: Invalid signature."); header('HTTP/1.1 401 Unauthorized'); exit('IPN Error: Invalid signature.'); }
             $data = json_decode($raw_body, true); if ($data === null) { error_log("IPN Error: Invalid JSON."); header('HTTP/1.1 400 Bad Request'); exit('IPN Error: Invalid JSON.'); }
             $payment_status = $data['payment_status'] ?? 'unknown'; $orderId = $data['order_id'] ?? null; $gatewayPaymentId = $data['payment_id'] ?? null; $actualPayCurrency = $data['pay_currency'] ?? null;
             error_log("IPN Status: " . $payment_status . " for Order ID: " . $orderId);
             if (!$orderId) { error_log("IPN Error: Missing order_id."); echo "IPN OK: Missing order_id."; exit(); }
             $finalDbStatus = 'Pending';
             if ($payment_status === 'finished') { $finalDbStatus = 'Complete'; }
             else if (in_array($payment_status, ['confirmed', 'sending', 'partially_paid'])) { $finalDbStatus = 'Processing'; }
             else if (in_array($payment_status, ['failed', 'refunded', 'expired'])) { $finalDbStatus = 'Failed'; }
             $this->pdo->beginTransaction();
             $sqlPay = "UPDATE payments SET status = ?, gateway_payment_id = ?, updated_at = NOW()"; $paramsPay = [$finalDbStatus, $gatewayPaymentId]; if ($actualPayCurrency) { $sqlPay .= ", pay_currency = IFNULL(pay_currency, ?)"; $paramsPay[] = $actualPayCurrency; } $sqlPay .= " WHERE order_id = ? AND status IN ('Pending', 'Processing')"; $paramsPay[] = $orderId;
             $stmtUpdatePay = $this->pdo->prepare($sqlPay); $updatedPay = $stmtUpdatePay->execute($paramsPay); $payRowsAffected = $stmtUpdatePay->rowCount();
             $stmtUpdateTx = $this->pdo->prepare("UPDATE transactions SET status = ? WHERE details = ? AND status IN ('Pending', 'Processing')"); $updatedTx = $stmtUpdateTx->execute([$finalDbStatus, $orderId]); $txRowsAffected = $stmtUpdateTx->rowCount();
             
             if ($finalDbStatus === 'Complete' && ($payRowsAffected > 0 || $txRowsAffected > 0)) {
                  $stmtGetData = $this->pdo->prepare("SELECT user_id, tokens FROM payments WHERE order_id = ?"); $stmtGetData->execute([$orderId]); $paymentData = $stmtGetData->fetch();
                  if ($paymentData && $paymentData['tokens'] > 0) {
                       $userId = $paymentData['user_id']; $tokenAmount = $paymentData['tokens'];
                       $stmtUser = $this->pdo->prepare("UPDATE users SET tokens = tokens + ? WHERE id = ?"); $updatedUser = $stmtUser->execute([$tokenAmount, $userId]);
                       error_log("IPN Credited Tokens (User: $userId): " . ($updatedUser ? 'Success' : 'FAILURE'));
                       // No email sent
                  } else { error_log("IPN Error: Could not find payment data for Order ID: " . $orderId); }
             }
             if (($payRowsAffected > 0 || $txRowsAffected > 0)) { $this->pdo->commit(); echo "IPN OK: Processed."; }
             else { $this->pdo->rollBack(); echo "IPN OK: No update."; }
             exit();
         } catch (\Exception $e) { error_log("IPN CRITICAL Error: " . $e->getMessage()); if ($this->pdo->inTransaction()) { $this->pdo->rollBack(); } header('HTTP/1.1 500 Internal Server Error'); exit('IPN Error: Server exception.');}
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
             $stmtMetrics = $this->pdo->prepare("SELECT COALESCE(SUM(CASE WHEN type IN ('PURCHASE', 'SIGNUP', 'REFERRAL') AND status='Complete' THEN amount ELSE 0 END), 0) AS total_acquired, COALESCE(SUM(CASE WHEN type = 'REFERRAL' AND status='Complete' THEN amount ELSE 0 END), 0) AS total_ref_earned, COALESCE(COUNT(CASE WHEN type = 'PURCHASE' AND status = 'Complete' THEN 1 ELSE NULL END), 0) AS total_purchases FROM transactions WHERE user_id = ?"); $stmtMetrics->execute([$userId]); $metrics = $stmtMetrics->fetch();
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