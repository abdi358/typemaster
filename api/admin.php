<?php
require_once dirname(__DIR__) . '/db/connect.php';

// Start session
startSecureSession();

// Admin Middleware Check
function requireAdmin()
{
    // 1. Check login
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Unauthorized']);
        exit;
    }

    // 2. Check role from DB to be sure (session might be stale)
    $user = fetchOne("SELECT role, is_banned FROM users WHERE id = ?", [$_SESSION['user_id']]);

    if (!$user || $user['role'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'Forbidden: Admin access required']);
        exit;
    }

    if ($user['is_banned']) {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'Account is banned']);
        exit;
    }

    return $_SESSION['user_id'];
}

// Log admin action
function logAdminAction($adminId, $action, $targetType, $targetId = null, $details = null)
{
    insertData('admin_logs', [
        'admin_id' => $adminId,
        'action' => $action,
        'target_type' => $targetType,
        'target_id' => $targetId,
        'details' => $details,
        'ip_address' => $_SERVER['REMOTE_ADDR']
    ]);
}

// Main Logic
header('Content-Type: application/json');

try {
    $adminId = requireAdmin();
    $action = $_GET['action'] ?? '';

    switch ($action) {
        // ==========================================
        // DASHBOARD STATS
        // ==========================================
        case 'stats':
            $totalUsers = fetchOne("SELECT COUNT(*) as count FROM users")['count'];
            $totalTests = fetchOne("SELECT COUNT(*) as count FROM test_results")['count'];
            $activeToday = fetchOne("SELECT COUNT(DISTINCT user_id) as count FROM test_results WHERE completed_at >= CURDATE()")['count'];
            $newUsersToday = fetchOne("SELECT COUNT(*) as count FROM users WHERE DATE(created_at) = CURDATE()")['count'];

            // Get recent registrations chart data (last 7 days)
            $registrations = fetchAll("
                SELECT DATE(created_at) as date, COUNT(*) as count 
                FROM users 
                WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) 
                GROUP BY DATE(created_at) 
                ORDER BY date ASC
            ");

            echo json_encode([
                'success' => true,
                'stats' => [
                    'totalUsers' => $totalUsers,
                    'totalTests' => $totalTests,
                    'activeToday' => $activeToday,
                    'newUsersToday' => $newUsersToday,
                    'registrations' => $registrations
                ]
            ]);
            break;

        // ==========================================
        // USER MANAGEMENT
        // ==========================================
        case 'get_users':
            $limit = 50;
            $search = $_GET['search'] ?? '';
            $params = [];
            $sql = "SELECT id, username, email, role, is_banned, created_at, total_tests FROM users";

            if ($search) {
                $sql .= " WHERE username LIKE ? OR email LIKE ?";
                $params[] = "%$search%";
                $params[] = "%$search%";
            }

            $sql .= " ORDER BY created_at DESC LIMIT $limit";
            $users = fetchAll($sql, $params);

            echo json_encode(['success' => true, 'users' => $users]);
            break;

        case 'ban_user':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST')
                throw new Exception('Method not allowed');
            $data = json_decode(file_get_contents('php://input'), true);
            $userId = $data['user_id'];

            updateData('users', ['is_banned' => 1], 'id = ?', [$userId]);
            logAdminAction($adminId, 'ban_user', 'user', $userId, 'Banned by admin');

            echo json_encode(['success' => true]);
            break;

        case 'unban_user':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST')
                throw new Exception('Method not allowed');
            $data = json_decode(file_get_contents('php://input'), true);
            $userId = $data['user_id'];

            updateData('users', ['is_banned' => 0], 'id = ?', [$userId]);
            logAdminAction($adminId, 'unban_user', 'user', $userId, 'Unbanned by admin');

            echo json_encode(['success' => true]);
            break;

        // ==========================================
        // CONTENT MANAGEMENT
        // ==========================================
        case 'get_texts':
            $texts = fetchAll("SELECT * FROM text_sets ORDER BY id DESC");
            echo json_encode(['success' => true, 'texts' => $texts]);
            break;

        case 'add_text':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST')
                throw new Exception('Method not allowed');
            $data = json_decode(file_get_contents('php://input'), true);

            $textId = insertData('text_sets', [
                'content' => $data['content'],
                'difficulty' => $data['difficulty'],
                'category' => $data['category'] ?? 'general',
                'word_count' => str_word_count($data['content']),
                'language' => 'en'
            ]);

            logAdminAction($adminId, 'add_text', 'text', $textId);
            echo json_encode(['success' => true, 'id' => $textId]);
            break;

        case 'delete_text':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST')
                throw new Exception('Method not allowed');
            $data = json_decode(file_get_contents('php://input'), true);
            $textId = $data['id'];

            $pdo = getDBConnection();
            $stmt = $pdo->prepare("DELETE FROM text_sets WHERE id = ?");
            $stmt->execute([$textId]);

            logAdminAction($adminId, 'delete_text', 'text', $textId);
            echo json_encode(['success' => true]);
            break;

        // ==========================================
        // SYSTEM SETTINGS
        // ==========================================
        case 'get_settings':
            $settings = fetchAll("SELECT setting_key, setting_value FROM system_settings");
            $kv = [];
            foreach ($settings as $s) {
                $kv[$s['setting_key']] = $s['setting_value'];
            }
            echo json_encode(['success' => true, 'settings' => $kv]);
            break;

        case 'update_setting':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST')
                throw new Exception('Method not allowed');
            $data = json_decode(file_get_contents('php://input'), true);
            $key = $data['key'];
            $value = $data['value'];

            updateData('system_settings', ['setting_value' => $value], 'setting_key = ?', [$key]);
            logAdminAction($adminId, 'update_setting', 'setting', $key, "Changed to: $value");

            echo json_encode(['success' => true]);
            break;

        default:
            throw new Exception('Invalid action');
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
