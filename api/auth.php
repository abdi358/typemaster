<?php
/**
 * TypeMaster Authentication API
 * Handles login, register, logout, and session checking
 */

require_once __DIR__ . '/../db/connect.php';

// Start session
startSecureSession();

// Get request method and action
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

// Handle requests
switch ($method) {
    case 'POST':
        $input = json_decode(file_get_contents('php://input'), true);

        switch ($action) {
            case 'register':
                handleRegister($input);
                break;
            case 'login':
                handleLogin($input);
                break;
            case 'logout':
                handleLogout();
                break;
            case 'update-preferences':
                handleUpdatePreferences($input);
                break;
            default:
                jsonResponse(['success' => false, 'error' => 'Invalid action'], 400);
        }
        break;

    case 'GET':
        switch ($action) {
            case 'check':
                handleSessionCheck();
                break;
            case 'profile':
                handleGetProfile();
                break;
            case 'stats':
                handleGetStats();
                break;
            default:
                jsonResponse(['success' => false, 'error' => 'Invalid action'], 400);
        }
        break;

    default:
        jsonResponse(['success' => false, 'error' => 'Method not allowed'], 405);
}

/**
 * Handle user registration
 */
function handleRegister($input)
{
    // Validate input
    $username = sanitize($input['username'] ?? '');
    $email = sanitize($input['email'] ?? '', 'email');
    $password = $input['password'] ?? '';

    // Validation
    $errors = [];

    if (strlen($username) < 3 || strlen($username) > 50) {
        $errors[] = 'Username must be between 3 and 50 characters';
    }

    if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $errors[] = 'Username can only contain letters, numbers, and underscores';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email address';
    }

    if (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters';
    }

    if (!preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $errors[] = 'Password must contain uppercase, lowercase, and number';
    }

    if (!empty($errors)) {
        jsonResponse(['success' => false, 'errors' => $errors], 400);
    }

    // Check if username or email exists
    $existing = fetchOne(
        "SELECT id FROM users WHERE username = ? OR email = ?",
        [$username, $email]
    );

    if ($existing) {
        jsonResponse(['success' => false, 'error' => 'Username or email already exists'], 409);
    }

    // Create user
    try {
        $passwordHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

        $userId = insertData('users', [
            'username' => $username,
            'email' => $email,
            'password_hash' => $passwordHash,
            'preferences' => json_encode(['theme' => 'dark', 'sound' => true, 'strictMode' => false])
        ]);

        // Log user in
        $_SESSION['user_id'] = $userId;
        $_SESSION['username'] = $username;

        // Award first achievement
        checkAndAwardAchievements($userId, 'tests', 0);

        jsonResponse([
            'success' => true,
            'message' => 'Account created successfully',
            'user' => [
                'id' => $userId,
                'username' => $username,
                'email' => $email
            ]
        ]);

    } catch (Exception $e) {
        error_log("Registration error: " . $e->getMessage());
        jsonResponse(['success' => false, 'error' => 'Registration failed. Please try again.'], 500);
    }
}

/**
 * Handle user login
 */
function handleLogin($input)
{
    $email = sanitize($input['email'] ?? '', 'email');
    $password = $input['password'] ?? '';

    if (empty($email) || empty($password)) {
        jsonResponse(['success' => false, 'error' => 'Email and password are required'], 400);
    }

    // Find user
    $user = fetchOne(
        "SELECT id, username, email, role, password_hash, preferences FROM users WHERE email = ?",
        [$email]
    );

    if (!$user || !password_verify($password, $user['password_hash'])) {
        jsonResponse(['success' => false, 'error' => 'Invalid email or password'], 401);
    }

    // Update last login
    updateData('users', ['last_login' => date('Y-m-d H:i:s')], 'id = ?', [$user['id']]);

    // Set session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];

    jsonResponse([
        'success' => true,
        'message' => 'Login successful',
        'user' => [
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'role' => $user['role'],
            'preferences' => json_decode($user['preferences'], true)
        ]
    ]);
}

/**
 * Handle logout
 */
function handleLogout()
{
    $_SESSION = [];

    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    session_destroy();

    jsonResponse(['success' => true, 'message' => 'Logged out successfully']);
}

/**
 * Check session status
 */
function handleSessionCheck()
{
    if (isLoggedIn()) {
        $user = getCurrentUser();
        jsonResponse([
            'success' => true,
            'authenticated' => true,
            'user' => [
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'role' => $user['role'],
                'preferences' => json_decode($user['preferences'], true)
            ]
        ]);
    } else {
        jsonResponse([
            'success' => true,
            'authenticated' => false
        ]);
    }
}

/**
 * Get user profile with stats
 */
function handleGetProfile()
{
    if (!isLoggedIn()) {
        jsonResponse(['success' => false, 'error' => 'Not authenticated'], 401);
    }

    $user = fetchOne(
        "SELECT id, username, email, avatar_url, created_at, total_tests, total_time_typing,
                best_wpm, avg_wpm, avg_accuracy, current_streak, max_streak, achievements, preferences
         FROM users WHERE id = ?",
        [getCurrentUserId()]
    );

    // Get recent tests
    $recentTests = fetchAll(
        "SELECT wpm, accuracy, test_duration, test_mode, test_value, difficulty, completed_at 
         FROM test_results WHERE user_id = ? ORDER BY completed_at DESC LIMIT 10",
        [getCurrentUserId()]
    );

    // Get user achievements
    $userAchievements = fetchAll(
        "SELECT a.name, a.description, a.icon, a.rarity, ua.unlocked_at
         FROM user_achievements ua
         JOIN achievements a ON ua.achievement_id = a.id
         WHERE ua.user_id = ?
         ORDER BY ua.unlocked_at DESC",
        [getCurrentUserId()]
    );

    jsonResponse([
        'success' => true,
        'profile' => [
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'avatar_url' => $user['avatar_url'],
            'created_at' => $user['created_at'],
            'stats' => [
                'total_tests' => $user['total_tests'],
                'total_time' => $user['total_time_typing'],
                'best_wpm' => $user['best_wpm'],
                'avg_wpm' => $user['avg_wpm'],
                'avg_accuracy' => $user['avg_accuracy'],
                'current_streak' => $user['current_streak'],
                'max_streak' => $user['max_streak']
            ],
            'achievements' => $userAchievements,
            'recent_tests' => $recentTests,
            'preferences' => json_decode($user['preferences'], true)
        ]
    ]);
}

/**
 * Get user statistics
 */
function handleGetStats()
{
    if (!isLoggedIn()) {
        jsonResponse(['success' => false, 'error' => 'Not authenticated'], 401);
    }

    $userId = getCurrentUserId();

    // Get overall stats
    $stats = fetchOne(
        "SELECT total_tests, best_wpm, avg_wpm, avg_accuracy, current_streak, max_streak, total_time_typing
         FROM users WHERE id = ?",
        [$userId]
    );

    // Get WPM progression (last 30 tests)
    $wpmProgression = fetchAll(
        "SELECT wpm, accuracy, completed_at FROM test_results 
         WHERE user_id = ? ORDER BY completed_at DESC LIMIT 30",
        [$userId]
    );

    // Get error heatmap data
    $errorData = fetchAll(
        "SELECT error_map FROM test_results WHERE user_id = ? AND error_map IS NOT NULL
         ORDER BY completed_at DESC LIMIT 50",
        [$userId]
    );

    // Aggregate error data
    $aggregatedErrors = [];
    foreach ($errorData as $row) {
        $errors = json_decode($row['error_map'], true) ?? [];
        foreach ($errors as $char => $count) {
            $aggregatedErrors[$char] = ($aggregatedErrors[$char] ?? 0) + $count;
        }
    }
    arsort($aggregatedErrors);
    $aggregatedErrors = array_slice($aggregatedErrors, 0, 20, true);

    // Get performance by mode
    $performanceByMode = fetchAll(
        "SELECT test_mode, test_value, AVG(wpm) as avg_wpm, AVG(accuracy) as avg_accuracy, COUNT(*) as count
         FROM test_results WHERE user_id = ?
         GROUP BY test_mode, test_value
         ORDER BY count DESC",
        [$userId]
    );

    jsonResponse([
        'success' => true,
        'stats' => $stats,
        'wpm_progression' => array_reverse($wpmProgression),
        'error_heatmap' => $aggregatedErrors,
        'performance_by_mode' => $performanceByMode
    ]);
}

/**
 * Update user preferences
 */
function handleUpdatePreferences($input)
{
    if (!isLoggedIn()) {
        jsonResponse(['success' => false, 'error' => 'Not authenticated'], 401);
    }

    $preferences = $input['preferences'] ?? [];

    // Validate preferences
    $validPreferences = [
        'theme' => in_array($preferences['theme'] ?? 'dark', ['dark', 'light']) ? $preferences['theme'] : 'dark',
        'sound' => isset($preferences['sound']) ? (bool) $preferences['sound'] : true,
        'strictMode' => isset($preferences['strictMode']) ? (bool) $preferences['strictMode'] : false,
        'showLiveWPM' => isset($preferences['showLiveWPM']) ? (bool) $preferences['showLiveWPM'] : true,
        'showProgressBar' => isset($preferences['showProgressBar']) ? (bool) $preferences['showProgressBar'] : true
    ];

    updateData(
        'users',
        ['preferences' => json_encode($validPreferences)],
        'id = ?',
        [getCurrentUserId()]
    );

    jsonResponse([
        'success' => true,
        'message' => 'Preferences updated',
        'preferences' => $validPreferences
    ]);
}

/**
 * Check and award achievements
 */
function checkAndAwardAchievements($userId, $type, $value)
{
    $achievements = fetchAll(
        "SELECT a.* FROM achievements a
         LEFT JOIN user_achievements ua ON a.id = ua.achievement_id AND ua.user_id = ?
         WHERE ua.id IS NULL AND a.requirement_type = ?",
        [$userId, $type]
    );

    $awarded = [];

    foreach ($achievements as $achievement) {
        $earned = false;

        switch ($achievement['requirement_operator']) {
            case '>=':
                $earned = $value >= $achievement['requirement_value'];
                break;
            case '=':
                $earned = $value == $achievement['requirement_value'];
                break;
            case '<=':
                $earned = $value <= $achievement['requirement_value'];
                break;
        }

        if ($earned) {
            insertData('user_achievements', [
                'user_id' => $userId,
                'achievement_id' => $achievement['id']
            ]);
            $awarded[] = $achievement;
        }
    }

    return $awarded;
}
