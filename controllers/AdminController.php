<?php
namespace Controllers;

// Adjust path as needed based on your final structure
require_once(__DIR__ . '/../config/config.php');
require_once(__DIR__ . '/../core/Database.php');

use Core\Database;
use PDO;
use PDOException;

class AdminController {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
        // Admin area should always start session if not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Checks if an admin user is logged in.
     */
    public function isAdminLoggedIn() {
        // Use a specific session variable for admin login status
        return isset($_SESSION['admin_user_id']) && isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
    }

    /**
     * Handles admin login.
     */
    public function login($data) {
        $loginId = $data['login_id'] ?? '';
        $password = $data['password'] ?? '';

        if (empty($loginId) || empty($password)) {
            return ['success' => false, 'message' => 'Please provide credentials.'];
        }

        try {
            // IMPORTANT: Only select users WHERE is_admin = TRUE
            $stmt = $this->pdo->prepare("SELECT id, username, password_hash, is_admin FROM users WHERE (username = ? OR email = ?) AND is_admin = TRUE");
            $stmt->execute([$loginId, $loginId]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                // Regenerate session ID for security
                session_regenerate_id(true);

                // Set admin-specific session variables
                $_SESSION['admin_user_id'] = $user['id'];
                $_SESSION['admin_username'] = $user['username'];
                $_SESSION['is_admin'] = true; // Explicitly set

                return ['success' => true, 'message' => 'Login successful! Redirecting...'];
            } else {
                return ['success' => false, 'message' => 'Invalid credentials or not an admin user.'];
            }
        } catch (PDOException $e) {
            error_log("Admin Login failed: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error during login.'];
        }
    }

     /**
     * Destroys the admin session.
     */
    public function logout() {
        // Unset only admin-specific session variables
        unset($_SESSION['admin_user_id']);
        unset($_SESSION['admin_username']);
        unset($_SESSION['is_admin']);
        // Optionally destroy session if ONLY admin uses it, otherwise keep user session if separate
        // session_destroy();
        return ['success' => true, 'message' => 'Logged out successfully.'];
    }


    // --- MOVED ADMIN METHODS ---

    /** Admin Function: Gets aggregated system metrics. */
    public function getAdminOverview() {
        if (!$this->isAdminLoggedIn()) return ['success' => false, 'message' => 'Access denied.'];
        // ... (Keep the exact code from AuthController's getAdminOverview) ...
        try {
            $totalUsers = $this->pdo->query("SELECT COUNT(id) FROM users")->fetchColumn();
            $txMetrics = $this->pdo->query("SELECT COALESCE(SUM(CASE WHEN type = 'PURCHASE' AND status='Complete' THEN amount ELSE 0 END), 0) AS total_purchased, COALESCE(SUM(CASE WHEN type = 'WITHDRAWAL' AND status = 'Processing' THEN amount ELSE 0 END), 0) AS total_pending_withdrawal FROM transactions")->fetch();
            $tokenMetrics = $this->pdo->query("SELECT COALESCE(SUM(tokens), 0) AS total_tokens_in_circulation FROM users")->fetch();
            $recentActivity = $this->pdo->query("SELECT t.id, u.username, t.type, t.amount, t.status, t.created_at FROM transactions t JOIN users u ON t.user_id = u.id ORDER BY t.created_at DESC LIMIT 5")->fetchAll();
            $totalRevenue = (float)$txMetrics['total_purchased'] * (1 / TOKEN_RATE);
            return ['success' => true, 'data' => ['total_users' => (int)$totalUsers, 'total_tokens_circulated' => number_format((float)$tokenMetrics['total_tokens_in_circulation'], 2), 'total_revenue_usd' => number_format($totalRevenue, 2), 'pending_withdrawal' => number_format(abs((float)$txMetrics['total_pending_withdrawal']), 2), 'recent_activity' => $recentActivity]];
        } catch (PDOException $e) { /* ... error handling ... */ error_log("Admin overview failed: " . $e->getMessage()); return ['success' => false, 'message' => 'Database error fetching overview.']; }
    }

    /** Admin Function: Processes a pending withdrawal. */
    public function processWithdrawal($data) {
        if (!$this->isAdminLoggedIn()) return ['success' => false, 'message' => 'Access denied.'];
        // ... (Keep the exact code from AuthController's processWithdrawal) ...
        $txId = filter_var($data['tx_id'], FILTER_VALIDATE_INT);
        $status = filter_var($data['status'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if (!$txId || !in_array($status, ['Complete', 'Failed'])) return ['success' => false, 'message' => 'Invalid transaction ID or status.'];
        try {
            $this->pdo->beginTransaction();
            $stmt = $this->pdo->prepare("SELECT status, amount, user_id FROM transactions WHERE id = ?"); $stmt->execute([$txId]); $transaction = $stmt->fetch();
            if (!$transaction || $transaction['status'] !== 'Processing') { $this->pdo->rollBack(); return ['success' => false, 'message' => 'Transaction not found or already processed.']; }
            $amount = (float)$transaction['amount']; $userId = $transaction['user_id']; $message = "";
            if ($status === 'Failed') { $stmtRefund = $this->pdo->prepare("UPDATE users SET tokens = tokens + ? WHERE id = ?"); $stmtRefund->execute([abs($amount), $userId]); $message = "Withdrawal failed and " . number_format(abs($amount), 2) . " GALAXY refunded."; }
            else { $message = "Withdrawal marked as complete."; }
            $stmtUpdate = $this->pdo->prepare("UPDATE transactions SET status = ? WHERE id = ?"); $stmtUpdate->execute([$status, $txId]);
            $this->pdo->commit(); return ['success' => true, 'message' => $message];
        } catch (PDOException $e) { $this->pdo->rollBack(); error_log("Process withdrawal failed: " . $e->getMessage()); return ['success' => false, 'message' => 'Server error during processing.']; }

    }

    /** Admin Function: Fetches all pending withdrawals. */
    public function getPendingWithdrawals() {
         if (!$this->isAdminLoggedIn()) return ['success' => false, 'message' => 'Access denied.'];
         // ... (Keep the exact code from AuthController's getPendingWithdrawals) ...
         try {
             $stmt = $this->pdo->prepare("SELECT t.id, u.username, t.amount, t.details, t.created_at FROM transactions t JOIN users u ON t.user_id = u.id WHERE t.type = 'WITHDRAWAL' AND t.status = 'Processing' ORDER BY t.created_at ASC"); $stmt->execute(); $withdrawals = $stmt->fetchAll();
             foreach ($withdrawals as &$w) { $w['amount'] = number_format(abs((float)$w['amount']), 2); $w['details'] = substr($w['details'], strpos($w['details'], 'To: ') + 4); $w['created_at'] = date('M d, H:i', strtotime($w['created_at'])); }
             return ['success' => true, 'withdrawals' => $withdrawals];
         } catch (PDOException $e) { error_log("Pending withdrawals failed: " . $e->getMessage()); return ['success' => false, 'message' => 'Database error.']; }
    }

    /** Admin Function: Fetches all users for management. */
    public function getAllUsers() {
        if (!$this->isAdminLoggedIn()) return ['success' => false, 'message' => 'Access denied.'];
        // ... (Keep the exact code from AuthController's getAllUsers) ...
        try {
            $stmt = $this->pdo->query("SELECT u.id, u.username, u.email, u.tokens, u.is_admin, u.created_at, (SELECT COUNT(id) FROM users WHERE referrer_id = u.id) AS referrals FROM users u ORDER BY u.created_at DESC"); $users = $stmt->fetchAll();
            foreach ($users as &$user) { $user['tokens'] = number_format((float)$user['tokens'], 2); $user['is_admin'] = (bool)$user['is_admin']; $user['created_at'] = date('M d, Y', strtotime($user['created_at'])); }
            return ['success' => true, 'users' => $users];
        } catch (PDOException $e) { error_log("User list failed: " . $e->getMessage()); return ['success' => false, 'message' => 'Database error.']; }
    }

    /** Admin Function: Updates a user's token balance. */
    public function adjustUserBalance($data) {
        if (!$this->isAdminLoggedIn()) return ['success' => false, 'message' => 'Access denied.'];
        // ... (Keep the exact code from AuthController's adjustUserBalance) ...
        $userId = filter_var($data['user_id'], FILTER_VALIDATE_INT); $amount = filter_var($data['amount'], FILTER_VALIDATE_FLOAT); $details = filter_var($data['details'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if (!$userId || $amount === false || empty($details)) return ['success' => false, 'message' => 'Invalid user ID, amount, or details.'];
        try {
            $this->pdo->beginTransaction();
            $stmtUpdate = $this->pdo->prepare("UPDATE users SET tokens = tokens + ? WHERE id = ?"); $stmtUpdate->execute([$amount, $userId]);
            $type = $amount >= 0 ? 'ADJUST_IN' : 'ADJUST_OUT';
            // IMPORTANT: Update transactions enum if ADJUST_IN/OUT needed, or use a generic type like 'ADJUSTMENT' if enum allows
            // Assuming 'PURCHASE' and 'WITHDRAWAL' cover adjustments for now based on previous enum
            $logType = $amount >= 0 ? 'PURCHASE' : 'WITHDRAWAL'; // Use existing types or adjust DB enum
            $logAmount = $amount; // Store actual adjustment amount

            $stmtLog = $this->pdo->prepare("INSERT INTO transactions (user_id, type, amount, status, details) VALUES (?, ?, ?, 'Complete', ?)");
            $stmtLog->execute([$userId, $logType, $logAmount, "Admin Adjustment: " . $details]);
            $this->pdo->commit();
            return ['success' => true, 'message' => "Balance for User $userId adjusted by " . number_format($amount, 2) . " GALAXY."];
        } catch (PDOException $e) { $this->pdo->rollBack(); error_log("Balance adjustment failed: " . $e->getMessage()); return ['success' => false, 'message' => 'Database error during balance adjustment.']; }
    }

} // End Class