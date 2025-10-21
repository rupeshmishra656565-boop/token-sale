<?php
namespace Controllers;

require_once(__DIR__ . '/../config/config.php');
require_once(__DIR__ . '/../core/Database.php'); 

use Core\Database;
use PDO;
use PDOException;

class AuthController {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Handles user registration, secure password hashing, and token bonuses.
     */
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

            // Log REFERRAL bonus for the new user if applicable
            if ($refId !== null && $refId != $newUserId) { 
                 $stmtLogRef = $this->pdo->prepare("INSERT INTO transactions (user_id, type, amount, status, details) VALUES (?, 'REFERRAL', ?, 'Complete', ?)");
                 $stmtLogRef->execute([$refId, REFERRAL_BONUS, 'Referral Reward for ID ' . $newUserId]);
            }

            $this->pdo->commit();

            $_SESSION['user_id'] = $newUserId;
            $_SESSION['username'] = $username;
            return ['success' => true, 'message' => 'Registration successful! You received ' . number_format(KYC_BONUS) . ' GALAXY tokens.'];

        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Registration failed: " . $e->getMessage());
            // Check for the missing table error to provide the SQL schema hint if needed
            if (strpos($e->getMessage(), 'transactions') !== false) {
                 return ['success' => false, 'message' => 'Registration failed: DB Error (Schema Missing).'];
            }
            return ['success' => false, 'message' => 'Registration failed due to a database error.'];
        }
    }

    /**
     * Handles user login using password verification.
     */
    public function login($data) {
        $loginId = $data['login_id'] ?? '';
        $password = $data['password'] ?? '';

        if (empty($loginId) || empty($password)) {
            return ['success' => false, 'message' => 'Please provide credentials.'];
        }

        $stmt = $this->pdo->prepare("SELECT id, username, password_hash, is_admin FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$loginId, $loginId]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['is_admin'] = (bool)$user['is_admin']; // Set admin status in session
            return ['success' => true, 'message' => 'Login successful! Redirecting...'];
        } else {
            return ['success' => false, 'message' => 'Invalid username/email or password.'];
        }
    }
    
    /**
     * Checks if the current user is an administrator.
     */
    private function checkIsAdmin() {
        return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
    }

    /**
     * Admin Function: Gets aggregated system metrics.
     */
    public function getAdminOverview() {
        if (!$this->checkIsAdmin()) {
            return ['success' => false, 'message' => 'Access denied. Administrator privileges required.'];
        }

        try {
            // 1. Get total user count
            $totalUsers = $this->pdo->query("SELECT COUNT(id) FROM users")->fetchColumn();
            
            // 2. Aggregate Financials (from TRANSACTIONS table)
            // This isolates the SUM functions to the correct table.
            $txMetrics = $this->pdo->query("
                SELECT
                    COALESCE(SUM(CASE WHEN type = 'PURCHASE' THEN amount ELSE 0 END), 0) AS total_purchased,
                    COALESCE(SUM(CASE WHEN type = 'WITHDRAWAL' AND status = 'Processing' THEN amount ELSE 0 END), 0) AS total_pending_withdrawal
                FROM transactions
            ")->fetch();

            // 3. Total Tokens in Circulation (from USERS table)
            $tokenMetrics = $this->pdo->query("
                SELECT COALESCE(SUM(tokens), 0) AS total_tokens_in_circulation
                FROM users
            ")->fetch();
            
            // Get recent activity (last 5 transactions from all users) - This query is fine.
            $recentActivity = $this->pdo->query("
                SELECT t.id, u.username, t.type, t.amount, t.status, t.created_at
                FROM transactions t
                JOIN users u ON t.user_id = u.id
                ORDER BY t.created_at DESC
                LIMIT 5
            ")->fetchAll();

            // Calculate total USD revenue (estimated: $0.001 per token purchased)
            $totalRevenue = (float)$txMetrics['total_purchased'] * 0.001;

            return [
                'success' => true,
                'data' => [
                    'total_users' => (int)$totalUsers,
                    'total_tokens_circulated' => number_format((float)$tokenMetrics['total_tokens_in_circulation'], 2),
                    'total_revenue_usd' => number_format($totalRevenue, 2),
                    'pending_withdrawal' => number_format(abs((float)$txMetrics['total_pending_withdrawal']), 2),
                    'recent_activity' => $recentActivity
                ]
            ];

        } catch (PDOException $e) {
            error_log("Admin overview failed: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error while fetching overview.'];
        }
    }
    
    /**
     * Admin Function: Processes a pending withdrawal.
     */
    public function processWithdrawal($data) {
        if (!$this->checkIsAdmin()) {
            return ['success' => false, 'message' => 'Access denied.'];
        }
        
        $txId = filter_var($data['tx_id'], FILTER_VALIDATE_INT);
        $status = filter_var($data['status'], FILTER_SANITIZE_STRING);

        if (!$txId || !in_array($status, ['Complete', 'Failed'])) {
            return ['success' => false, 'message' => 'Invalid transaction ID or status.'];
        }

        try {
            $this->pdo->beginTransaction();
            
            $stmt = $this->pdo->prepare("SELECT status, amount, user_id FROM transactions WHERE id = ?");
            $stmt->execute([$txId]);
            $transaction = $stmt->fetch();

            if (!$transaction || $transaction['status'] !== 'Processing') {
                $this->pdo->rollBack();
                return ['success' => false, 'message' => 'Transaction not found or already processed.'];
            }

            $amount = (float)$transaction['amount'];
            $userId = $transaction['user_id'];

            // If processing failed, refund the tokens
            if ($status === 'Failed') {
                $stmtRefund = $this->pdo->prepare("UPDATE users SET tokens = tokens + ? WHERE id = ?");
                $stmtRefund->execute([abs($amount), $userId]);
                $message = "Withdrawal failed and " . number_format(abs($amount), 2) . " GALAXY refunded to user.";
            } else {
                $message = "Withdrawal marked as complete and processed off-chain.";
            }

            // Update transaction status
            $stmtUpdate = $this->pdo->prepare("UPDATE transactions SET status = ? WHERE id = ?");
            $stmtUpdate->execute([$status, $txId]);
            
            $this->pdo->commit();
            
            return ['success' => true, 'message' => $message];

        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Process withdrawal failed: " . $e->getMessage());
            return ['success' => false, 'message' => 'Server error during processing.'];
        }
    }
    
    /**
     * Admin Function: Fetches all pending withdrawals for the admin panel.
     */
    public function getPendingWithdrawals() {
         if (!$this->checkIsAdmin()) {
            return ['success' => false, 'message' => 'Access denied.'];
        }
        
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    t.id, 
                    u.username, 
                    t.amount, 
                    t.details, 
                    t.created_at
                FROM transactions t
                JOIN users u ON t.user_id = u.id
                WHERE t.type = 'WITHDRAWAL' AND t.status = 'Processing'
                ORDER BY t.created_at ASC
            ");
            $stmt->execute();
            $withdrawals = $stmt->fetchAll();
            
            // Format amounts for display
            foreach ($withdrawals as &$w) {
                $w['amount'] = number_format(abs((float)$w['amount']), 2);
                $w['details'] = substr($w['details'], strpos($w['details'], 'To: ') + 4);
                $w['created_at'] = date('M d, H:i', strtotime($w['created_at']));
            }

            return ['success' => true, 'withdrawals' => $withdrawals];

        } catch (PDOException $e) {
            error_log("Pending withdrawals failed: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error.'];
        }
    }

    /**
     * Admin Function: Fetches all users for management.
     */
    public function getAllUsers() {
        if (!$this->checkIsAdmin()) {
            return ['success' => false, 'message' => 'Access denied.'];
        }
        
        try {
            $stmt = $this->pdo->query("
                SELECT 
                    u.id, 
                    u.username, 
                    u.email, 
                    u.tokens, 
                    u.is_admin,
                    u.created_at,
                    (SELECT COUNT(id) FROM users WHERE referrer_id = u.id) AS referrals
                FROM users u
                ORDER BY u.created_at DESC
            ");
            $users = $stmt->fetchAll();
            
            // Format tokens for display
            foreach ($users as &$user) {
                $user['tokens'] = number_format((float)$user['tokens'], 2);
                $user['is_admin'] = (bool)$user['is_admin'];
                $user['created_at'] = date('M d, Y', strtotime($user['created_at']));
            }

            return ['success' => true, 'users' => $users];

        } catch (PDOException $e) {
            error_log("User list failed: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error.'];
        }
    }
    
    /**
     * Admin Function: Updates a user's token balance (manual adjustment).
     */
    public function adjustUserBalance($data) {
        if (!$this->checkIsAdmin()) {
            return ['success' => false, 'message' => 'Access denied.'];
        }
        
        $userId = filter_var($data['user_id'], FILTER_VALIDATE_INT);
        $amount = filter_var($data['amount'], FILTER_VALIDATE_FLOAT); 
        $details = filter_var($data['details'], FILTER_SANITIZE_STRING); 

        if (!$userId || $amount === false || empty($details)) {
            return ['success' => false, 'message' => 'Invalid user ID, amount, or details.'];
        }

        try {
            $this->pdo->beginTransaction();

            $stmtUpdate = $this->pdo->prepare("UPDATE users SET tokens = tokens + ? WHERE id = ?");
            $stmtUpdate->execute([$amount, $userId]);
            
            // Log as a manual adjustment
            $type = $amount >= 0 ? 'ADJUST_IN' : 'ADJUST_OUT';
            $stmtLog = $this->pdo->prepare("INSERT INTO transactions (user_id, type, amount, status, details) VALUES (?, ?, ?, 'Complete', ?)");
            $stmtLog->execute([$userId, $type, $amount, "Admin Adjustment: " . $details]);

            $this->pdo->commit();
            return ['success' => true, 'message' => "Balance for User $userId adjusted by " . number_format($amount, 2) . " GALAXY."];

        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Balance adjustment failed: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error during balance adjustment.'];
        }
    }

    /**
     * Handles updating the user's password.
     */
    public function updatePassword($data) {
        if (!isset($_SESSION['user_id'])) {
            return ['success' => false, 'message' => 'Authentication required.'];
        }
        
        $userId = $_SESSION['user_id'];
        $currentPassword = $data['current_password'] ?? '';
        $newPassword = $data['new_password'] ?? '';
        $confirmPassword = $data['confirm_password'] ?? '';

        if (empty($currentPassword) || empty($newPassword) || $newPassword !== $confirmPassword || strlen($newPassword) < 6) {
            return ['success' => false, 'message' => 'Invalid input. Check password match and length (min 6).'];
        }

        try {
            // 1. Verify current password
            $stmt = $this->pdo->prepare("SELECT password_hash FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch();

            if (!$user || !password_verify($currentPassword, $user['password_hash'])) {
                return ['success' => false, 'message' => 'Current password is incorrect.'];
            }

            // 2. Hash and update new password
            $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmtUpdate = $this->pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
            $stmtUpdate->execute([$newPasswordHash, $userId]);

            return ['success' => true, 'message' => 'Password updated successfully!'];

        } catch (PDOException $e) {
            error_log("Password update failed: " . $e->getMessage());
            return ['success' => false, 'message' => 'Server error during password update.'];
        }
    }
    
    /**
     * Fetches the user's current token balance.
     */
    public function getBalance() {
        if (!isset($_SESSION['user_id'])) {
            return ['success' => false, 'message' => 'Not authenticated.'];
        }

        $userId = $_SESSION['user_id'];
        $stmt = $this->pdo->prepare("SELECT tokens FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $data = $stmt->fetch();

        if ($data) {
            return ['success' => true, 'balance' => number_format((float)$data['tokens'], 2, '.', ',')];
        } else {
            return ['success' => false, 'message' => 'User data not found.'];
        }
    }
    
    /**
     * Fetches all user transactions for the Wallet history.
     */
    public function getTransactionHistory() {
        if (!isset($_SESSION['user_id'])) {
            return ['success' => false, 'message' => 'Not authenticated.'];
        }
        $userId = $_SESSION['user_id'];
        
        try {
            $stmt = $this->pdo->prepare("SELECT type, amount, status, created_at, details FROM transactions WHERE user_id = ? ORDER BY created_at DESC");
            $stmt->execute([$userId]);
            $history = $stmt->fetchAll();
            
            // Format amounts for client-side display
            foreach ($history as &$tx) {
                 $tx['amount'] = number_format((float)abs($tx['amount']), 2, '.', ',');
            }
            
            return ['success' => true, 'history' => $history];

        } catch (PDOException $e) {
            error_log("Transaction history fetch failed: " . $e->getMessage());
            return ['success' => false, 'message' => 'DB Error: Transactions table missing. Run the required SQL schema.'];
        }
    }
    
    /**
     * Fetches the user's referral statistics and history.
     */
    public function getReferralHistory() {
        if (!isset($_SESSION['user_id'])) {
            return ['success' => false, 'message' => 'Not authenticated.'];
        }

        $userId = $_SESSION['user_id'];
        
        // 1. Get Referral Statistics
        $stmtStats = $this->pdo->prepare("SELECT COUNT(id) as total_referrals FROM users WHERE referrer_id = ?");
        $stmtStats->execute([$userId]);
        $stats = $stmtStats->fetch();
        $totalReferrals = $stats['total_referrals'] ?? 0;
        $totalEarnings = $totalReferrals * REFERRAL_BONUS;

        // 2. Get Detailed Referral History
        $stmtHistory = $this->pdo->prepare("SELECT username, created_at FROM users WHERE referrer_id = ? ORDER BY created_at DESC");
        $stmtHistory->execute([$userId]);
        $history = $stmtHistory->fetchAll();

        return [
            'success' => true,
            'stats' => [
                'total_referrals' => (int)$totalReferrals,
                'total_earnings' => number_format($totalEarnings, 2, '.', ',')
            ],
            'history' => $history
        ];
    }
    
    /**
     * Fetches non-sensitive user details and lifetime stats for the profile page.
     */
    public function getUserDetails() {
        if (!isset($_SESSION['user_id'])) {
            return ['success' => false, 'message' => 'Not authenticated.'];
        }
        $userId = $_SESSION['user_id'];
        
        try {
            // 1. Fetch user details
            $stmt = $this->pdo->prepare("
                SELECT 
                    u.id, 
                    u.username, 
                    u.email, 
                    u.created_at, 
                    u.referrer_id
                FROM users u
                WHERE u.id = ?
            ");
            $stmt->execute([$userId]);
            $user = $stmt->fetch();

            if (!$user) {
                return ['success' => false, 'message' => 'User profile data not found.'];
            }
            
            // 2. Aggregate Lifetime Metrics (using COALESCE to guarantee non-NULL results)
            $stmtMetrics = $this->pdo->prepare("
                SELECT 
                    COALESCE(SUM(CASE WHEN type IN ('PURCHASE', 'SIGNUP', 'REFERRAL') THEN amount ELSE 0 END), 0) AS total_acquired_tokens,
                    COALESCE(SUM(CASE WHEN type = 'REFERRAL' THEN amount ELSE 0 END), 0) AS total_ref_earned,
                    COALESCE(COUNT(CASE WHEN type = 'PURCHASE' THEN 1 ELSE NULL END), 0) AS total_purchase_count
                FROM transactions 
                WHERE user_id = ?
            ");
            $stmtMetrics->execute([$userId]);
            $metrics = $stmtMetrics->fetch();
            
            // 3. Fetch Recent Transactions
            $stmtRecentTx = $this->pdo->prepare("
                SELECT type, amount, status, created_at
                FROM transactions 
                WHERE user_id = ? 
                ORDER BY created_at DESC 
                LIMIT 5
            ");
            $stmtRecentTx->execute([$userId]);
            $recentTransactions = $stmtRecentTx->fetchAll();

            // Calculate final 'Tokens Acquired (Net)'
            $totalAcquired = (float)($metrics['total_acquired_tokens']);
            $totalRefEarned = (float)($metrics['total_ref_earned']);
            $totalPurchases = (int)($metrics['total_purchase_count']);
            
            return [
                'success' => true,
                'details' => [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'member_since' => $user['created_at'],
                    'referrer_id' => $user['referrer_id'],
                    'stats' => [
                        'tokens_acquired' => number_format($totalAcquired, 2, '.', ','),
                        'total_purchases' => $totalPurchases,
                        'referral_earnings' => number_format($totalRefEarned, 2, '.', ',')
                    ]
                ],
                'recent_transactions' => $recentTransactions
            ];
            
        } catch (PDOException $e) {
            error_log("Profile detail fetch failed: " . $e->getMessage());
            return ['success' => false, 'message' => 'Server error while fetching profile details.'];
        }
    }


    /**
     * Destroys the current session.
     */
    public function logout() {
        session_destroy();
        return ['success' => true, 'message' => 'Logged out successfully.'];
    }
}
