<?php
namespace Controllers;

require_once(__DIR__ . '/../config/config.php');
require_once(__DIR__ . '/../core/Database.php');

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

    // --- Registration Process ---
    public function register($data) {
        $email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
        $username = filter_var($data['username'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $password = $data['password'] ?? '';
        $refIdInput = $data['referrer_id'] ?? '';
        $refId = null;

        // --- (Input validation remains the same) ---
        if (!empty($refIdInput)) {
            $filteredId = filter_var($refIdInput, FILTER_VALIDATE_INT);
            if ($filteredId !== false && $filteredId > 0) {
                $refId = $filteredId;
            }
        }
        if (!$email || !$username || empty($password) || strlen($password) < 6) {
            return ['success' => false, 'message' => 'Invalid input. Check fields and password length (min 6).'];
        }
        $stmtCheck = $this->pdo->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
        $stmtCheck->execute([$email, $username]);
        if ($stmtCheck->fetch()) {
            return ['success' => false, 'message' => 'Username or Email already taken.'];
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        // --- MODIFICATION: Initial bonus goes to bonus_tokens ---
        $initialBonus = KYC_BONUS; 

        try {
            $this->pdo->beginTransaction();
            
            // --- MODIFICATION: Insert into tokens=0, bonus_tokens = $initialBonus ---
            $stmtInsert = $this->pdo->prepare(
                "INSERT INTO users (username, email, password_hash, tokens, bonus_tokens, referral_tokens, kyc_claimed, referrer_id) 
                 VALUES (?, ?, ?, 0.00, ?, 0.00, TRUE, ?)"
            );
            $stmtInsert->execute([$username, $email, $passwordHash, $initialBonus, $refId]);
            $newUserId = $this->pdo->lastInsertId();

            // --- MODIFICATION: Add referral bonus to referrer's referral_tokens ---
            if ($refId !== null && $refId != $newUserId) { 
                $stmtRef = $this->pdo->prepare("UPDATE users SET referral_tokens = referral_tokens + ? WHERE id = ?");
                $stmtRef->execute([REFERRAL_BONUS, $refId]);
            }
            
            // --- Log transactions (no change needed here, just ensure types are correct) ---
            $stmtLogSignup = $this->pdo->prepare("INSERT INTO transactions (user_id, type, amount, status, details) VALUES (?, 'SIGNUP', ?, 'Complete', ?)");
            $stmtLogSignup->execute([$newUserId, $initialBonus, 'Signup Bonus Credited']);

            if ($refId !== null && $refId != $newUserId) { 
                 $stmtLogRef = $this->pdo->prepare("INSERT INTO transactions (user_id, type, amount, status, details) VALUES (?, 'REFERRAL', ?, 'Complete', ?)");
                 $stmtLogRef->execute([$refId, REFERRAL_BONUS, 'Referral Reward for user ' . $newUserId]);
            }

            $this->pdo->commit();
            
            $_SESSION['user_id'] = $newUserId;
            $_SESSION['username'] = $username;
            return ['success' => true, 'message' => 'Registration successful! You received ' . number_format($initialBonus) . ' Bonus GALAXY tokens.'];

        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Registration failed: " . $e->getMessage());
            return ['success' => false, 'message' => 'Registration failed due to a database error.'];
        }
    }

    // --- User Login (No changes needed) ---
    public function login($data) {
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
    public function handleNowPaymentsIPN() {
         error_log("====== IPN Handler Started ======");
         $headers = getallheaders(); $np_signature = $headers['X-Nowpayments-Sig'] ?? $headers['x-nowpayments-sig'] ?? '';
         // --- (Signature validation remains the same) ---
         if (empty($np_signature)) { /* ... */ exit('IPN Error: No signature.'); }
         $raw_body = file_get_contents('php://input'); 
         if ($raw_body === false) { /* ... */ exit('IPN Error: No body.'); }
         try {
             $hmac = hash_hmac('sha512', $raw_body, NOWPAYMENTS_IPN_SECRET);
             if (!hash_equals($hmac, $np_signature)) { /* ... */ exit('IPN Error: Invalid signature.'); }
             
             $data = json_decode($raw_body, true); 
             if ($data === null) { /* ... */ exit('IPN Error: Invalid JSON.'); }
             
             $payment_status = $data['payment_status'] ?? 'unknown'; 
             $orderId = $data['order_id'] ?? null; 
             $gatewayPaymentId = $data['payment_id'] ?? null; 
             $actualPayCurrency = $data['pay_currency'] ?? null;
             error_log("IPN Status: " . $payment_status . " for Order ID: " . $orderId);
             if (!$orderId) { /* ... */ exit(); }
             
             $finalDbStatus = 'Processing'; 
             if ($payment_status === 'finished') { $finalDbStatus = 'Complete'; }
             else if (in_array($payment_status, ['failed', 'refunded', 'expired'])) { $finalDbStatus = 'Failed'; }

             if ($finalDbStatus === 'Processing' && $payment_status !== 'finished' && !in_array($payment_status, ['failed', 'refunded', 'expired'])) {
                 error_log("IPN Info: Intermediate status '$payment_status' received for $orderId. Keeping DB status as Processing.");
                 echo "IPN OK: Intermediate status.";
                 exit();
             }

             $this->pdo->beginTransaction();
             
             $sqlPay = "UPDATE payments SET status = ?, gateway_payment_id = IFNULL(?, gateway_payment_id), updated_at = NOW()"; 
             $paramsPay = [$finalDbStatus, $gatewayPaymentId]; 
             if ($actualPayCurrency) { $sqlPay .= ", pay_currency = IFNULL(pay_currency, ?)"; $paramsPay[] = $actualPayCurrency; } 
             $sqlPay .= " WHERE order_id = ? AND status IN ('Processing', 'Pending')"; 
             $paramsPay[] = $orderId;
             $stmtUpdatePay = $this->pdo->prepare($sqlPay); 
             $updatedPay = $stmtUpdatePay->execute($paramsPay); 
             $payRowsAffected = $stmtUpdatePay->rowCount();
             
             $stmtUpdateTx = $this->pdo->prepare("UPDATE transactions SET status = ? WHERE details = ? AND status IN ('Processing', 'Pending')"); 
             $updatedTx = $stmtUpdateTx->execute([$finalDbStatus, $orderId]); 
             $txRowsAffected = $stmtUpdateTx->rowCount();
             
             // --- MODIFICATION: Credit purchased tokens to the 'tokens' column ---
             if ($finalDbStatus === 'Complete' && ($payRowsAffected > 0 || $txRowsAffected > 0)) {
                  $stmtGetData = $this->pdo->prepare("SELECT user_id, tokens FROM payments WHERE order_id = ?"); 
                  $stmtGetData->execute([$orderId]); 
                  $paymentData = $stmtGetData->fetch();
                  if ($paymentData && $paymentData['tokens'] > 0) {
                       $userId = $paymentData['user_id']; 
                       $purchasedTokenAmount = $paymentData['tokens']; // This is the total amount including bonus from purchase
                       
                       // Add the purchased amount (including any package bonus) to the main 'tokens' column
                       $stmtUser = $this->pdo->prepare("UPDATE users SET tokens = tokens + ? WHERE id = ?"); 
                       $updatedUser = $stmtUser->execute([$purchasedTokenAmount, $userId]);
                       error_log("IPN Credited Purchased Tokens (User: $userId, Order: $orderId): " . ($updatedUser ? 'Success' : 'FAILURE'));
                  } else { error_log("IPN Error: Could not find payment data or token amount for Order ID: " . $orderId . " during Complete step."); }
             }
             
             if (($payRowsAffected > 0 || $txRowsAffected > 0)) { 
                 $this->pdo->commit(); 
                 error_log("IPN Processed: Order $orderId updated to $finalDbStatus. Rows affected (Pay/Tx): $payRowsAffected/$txRowsAffected");
                 echo "IPN OK: Processed."; 
             } else { 
                 $this->pdo->rollBack(); 
                 error_log("IPN Info: No DB update for Order $orderId with IPN status '$payment_status'. DB Status likely already final or order mismatch.");
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

