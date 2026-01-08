<?php
/**
 * TypeMaster - Leaderboard API
 * Returns leaderboard data for different time periods
 */

require_once __DIR__ . '/../db/connect.php';

// Get parameters
$type = sanitize($_GET['type'] ?? 'global'); // global, daily, weekly, monthly
$testMode = sanitize($_GET['mode'] ?? 'all');
$testValue = sanitize($_GET['value'] ?? '', 'int');
$limit = (int) ($_GET['limit'] ?? 50);

// Validate
$validTypes = ['global', 'daily', 'weekly', 'monthly'];
if (!in_array($type, $validTypes)) {
    $type = 'global';
}

$limit = max(10, min(100, $limit));

try {
    // Build date filter
    $dateFilter = '';
    $params = [];

    switch ($type) {
        case 'daily':
            $dateFilter = "AND DATE(completed_at) = CURDATE()";
            break;
        case 'weekly':
            $dateFilter = "AND completed_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
            break;
        case 'monthly':
            $dateFilter = "AND completed_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
            break;
    }

    // Build mode filter
    $modeFilter = '';
    if ($testMode !== 'all') {
        $modeFilter = "AND test_mode = ?";
        $params[] = $testMode;

        if ($testValue) {
            $modeFilter .= " AND test_value = ?";
            $params[] = $testValue;
        }
    }

    // Get leaderboard
    $sql = "SELECT 
                tr.id,
                tr.wpm,
                tr.accuracy,
                tr.test_duration,
                tr.test_mode,
                tr.test_value,
                tr.difficulty,
                tr.completed_at,
                u.id as user_id,
                u.username,
                u.avatar_url
            FROM test_results tr
            LEFT JOIN users u ON tr.user_id = u.id
            WHERE tr.wpm > 0 {$dateFilter} {$modeFilter}
            ORDER BY tr.wpm DESC, tr.accuracy DESC
            LIMIT {$limit}";

    $results = fetchAll($sql, $params);

    // Add rank
    $leaderboard = [];
    $rank = 1;
    foreach ($results as $row) {
        $leaderboard[] = [
            'rank' => $rank++,
            'id' => $row['id'],
            'wpm' => round($row['wpm'], 1),
            'accuracy' => round($row['accuracy'], 1),
            'duration' => $row['test_duration'],
            'mode' => $row['test_mode'],
            'value' => $row['test_value'],
            'difficulty' => $row['difficulty'],
            'completedAt' => $row['completed_at'],
            'user' => $row['user_id'] ? [
                'id' => $row['user_id'],
                'username' => $row['username'],
                'avatar' => $row['avatar_url']
            ] : [
                'id' => null,
                'username' => 'Guest',
                'avatar' => null
            ]
        ];
    }

    // Get current user's rank if logged in
    startSecureSession();
    $currentUserRank = null;
    $currentUserBest = null;

    if (isLoggedIn()) {
        $userId = getCurrentUserId();

        // Get user's best score for this mode
        $userBestSql = "SELECT 
                            tr.id, tr.wpm, tr.accuracy, tr.completed_at
                        FROM test_results tr
                        WHERE tr.user_id = ? {$modeFilter}
                        ORDER BY tr.wpm DESC
                        LIMIT 1";

        $userBest = fetchOne($userBestSql, array_merge([$userId], $params));

        if ($userBest) {
            $currentUserBest = [
                'wpm' => round($userBest['wpm'], 1),
                'accuracy' => round($userBest['accuracy'], 1),
                'completedAt' => $userBest['completed_at']
            ];

            // Calculate rank
            $rankSql = "SELECT COUNT(*) + 1 as rank 
                        FROM test_results 
                        WHERE wpm > ? {$dateFilter} {$modeFilter}";

            $rankResult = fetchOne($rankSql, array_merge([$userBest['wpm']], $params));
            $currentUserRank = $rankResult['rank'];
        }
    }

    // Get total entries count
    $countSql = "SELECT COUNT(*) as total FROM test_results WHERE wpm > 0 {$dateFilter} {$modeFilter}";
    $totalCount = fetchOne($countSql, $params);

    jsonResponse([
        'success' => true,
        'type' => $type,
        'mode' => $testMode,
        'value' => $testValue,
        'leaderboard' => $leaderboard,
        'totalEntries' => $totalCount['total'],
        'currentUser' => [
            'rank' => $currentUserRank,
            'best' => $currentUserBest
        ]
    ]);

} catch (Exception $e) {
    error_log("Leaderboard error: " . $e->getMessage());
    jsonResponse(['success' => false, 'error' => 'Failed to load leaderboard'], 500);
}
