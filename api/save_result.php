<?php
/**
 * TypeMaster - Save Result API
 * Saves test results and updates user statistics
 */

require_once __DIR__ . '/../db/connect.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'error' => 'Method not allowed'], 405);
}

// Get input
$input = json_decode(file_get_contents('php://input'), true);

// Validate required fields
$requiredFields = [
    'wpm',
    'rawWpm',
    'cpm',
    'accuracy',
    'totalCharacters',
    'correctCharacters',
    'incorrectCharacters',
    'totalErrors',
    'testDuration',
    'testMode',
    'testValue',
    'difficulty'
];

foreach ($requiredFields as $field) {
    if (!isset($input[$field])) {
        jsonResponse(['success' => false, 'error' => "Missing required field: {$field}"], 400);
    }
}

// Sanitize inputs
$wpm = sanitize($input['wpm'], 'float');
$rawWpm = sanitize($input['rawWpm'], 'float');
$cpm = sanitize($input['cpm'], 'int');
$accuracy = sanitize($input['accuracy'], 'float');
$totalCharacters = sanitize($input['totalCharacters'], 'int');
$correctCharacters = sanitize($input['correctCharacters'], 'int');
$incorrectCharacters = sanitize($input['incorrectCharacters'], 'int');
$totalErrors = sanitize($input['totalErrors'], 'int');
$testDuration = sanitize($input['testDuration'], 'int');
$testMode = sanitize($input['testMode']);
$testValue = sanitize($input['testValue'], 'int');
$difficulty = sanitize($input['difficulty']);
$textType = sanitize($input['textType'] ?? 'words');
$errorMap = isset($input['errorMap']) ? json_encode($input['errorMap']) : '{}';
$metrics = $input['metrics'] ?? [];

// Validate values
if ($wpm < 0 || $wpm > 300) {
    jsonResponse(['success' => false, 'error' => 'Invalid WPM value'], 400);
}

if ($accuracy < 0 || $accuracy > 100) {
    jsonResponse(['success' => false, 'error' => 'Invalid accuracy value'], 400);
}

startSecureSession();
$userId = getCurrentUserId();

try {
    $pdo = getDBConnection();
    $pdo->beginTransaction();

    // Insert test result
    $resultId = insertData('test_results', [
        'user_id' => $userId,
        'wpm' => $wpm,
        'raw_wpm' => $rawWpm,
        'cpm' => $cpm,
        'accuracy' => $accuracy,
        'total_characters' => $totalCharacters,
        'correct_characters' => $correctCharacters,
        'incorrect_characters' => $incorrectCharacters,
        'total_errors' => $totalErrors,
        'test_duration' => $testDuration,
        'test_mode' => $testMode,
        'test_value' => $testValue,
        'difficulty' => $difficulty,
        'text_type' => $textType,
        'error_map' => $errorMap
    ]);

    // Insert second-by-second metrics
    if (!empty($metrics) && is_array($metrics)) {
        foreach ($metrics as $metric) {
            insertData('test_metrics', [
                'result_id' => $resultId,
                'second_mark' => $metric['second'] ?? 0,
                'wpm_at_second' => $metric['wpm'] ?? 0,
                'accuracy_at_second' => $metric['accuracy'] ?? 100,
                'characters_typed' => $metric['characters'] ?? 0,
                'errors_at_second' => $metric['errors'] ?? 0
            ]);
        }
    }

    $newAchievements = [];

    // Update user statistics if logged in
    if ($userId) {
        // Get current stats
        $user = fetchOne(
            "SELECT total_tests, best_wpm, avg_wpm, avg_accuracy, total_time_typing, 
                    current_streak, max_streak, last_test_date
             FROM users WHERE id = ?",
            [$userId]
        );

        // Calculate new averages
        $newTotalTests = $user['total_tests'] + 1;
        $newAvgWpm = (($user['avg_wpm'] * $user['total_tests']) + $wpm) / $newTotalTests;
        $newAvgAccuracy = (($user['avg_accuracy'] * $user['total_tests']) + $accuracy) / $newTotalTests;
        $newBestWpm = max($user['best_wpm'], $wpm);
        $newTotalTime = $user['total_time_typing'] + $testDuration;

        // Calculate streak
        $today = date('Y-m-d');
        $lastTestDate = $user['last_test_date'];
        $currentStreak = $user['current_streak'];
        $maxStreak = $user['max_streak'];

        if ($lastTestDate === null) {
            $currentStreak = 1;
        } elseif ($lastTestDate === $today) {
            // Same day, streak continues
        } elseif ($lastTestDate === date('Y-m-d', strtotime('-1 day'))) {
            // Yesterday, streak increases
            $currentStreak++;
        } else {
            // Streak broken
            $currentStreak = 1;
        }

        $maxStreak = max($maxStreak, $currentStreak);

        // Update user
        updateData('users', [
            'total_tests' => $newTotalTests,
            'avg_wpm' => round($newAvgWpm, 2),
            'avg_accuracy' => round($newAvgAccuracy, 2),
            'best_wpm' => $newBestWpm,
            'total_time_typing' => $newTotalTime,
            'current_streak' => $currentStreak,
            'max_streak' => $maxStreak,
            'last_test_date' => $today
        ], 'id = ?', [$userId]);

        // Check achievements
        $newAchievements = array_merge(
            checkAndAwardAchievements($userId, 'wpm', $wpm),
            checkAndAwardAchievements($userId, 'accuracy', $accuracy),
            checkAndAwardAchievements($userId, 'tests', $newTotalTests),
            checkAndAwardAchievements($userId, 'streak', $currentStreak),
            checkAndAwardAchievements($userId, 'time', $newTotalTime)
        );

        // Check for special achievements (time-based)
        $hour = (int) date('H');
        if ($hour >= 0 && $hour < 5) {
            $newAchievements = array_merge(
                $newAchievements,
                checkSpecialAchievement($userId, 'Night Owl')
            );
        }
        if ($hour >= 5 && $hour < 7) {
            $newAchievements = array_merge(
                $newAchievements,
                checkSpecialAchievement($userId, 'Early Bird')
            );
        }
    }

    // Update text usage count
    executeQuery(
        "UPDATE text_sets SET times_used = times_used + 1 WHERE difficulty = ? LIMIT 1",
        [$difficulty]
    );

    $pdo->commit();

    // Get rank info
    $rank = fetchOne(
        "SELECT COUNT(*) + 1 as rank FROM test_results WHERE wpm > ? AND test_mode = ? AND test_value = ?",
        [$wpm, $testMode, $testValue]
    );

    jsonResponse([
        'success' => true,
        'message' => 'Result saved successfully',
        'resultId' => $resultId,
        'rank' => $rank['rank'] ?? null,
        'newAchievements' => array_map(function ($a) {
            return [
                'name' => $a['name'],
                'description' => $a['description'],
                'icon' => $a['icon'],
                'rarity' => $a['rarity']
            ];
        }, $newAchievements),
        'isPersonalBest' => $userId && $wpm >= $newBestWpm
    ]);

} catch (Exception $e) {
    if (isset($pdo)) {
        $pdo->rollBack();
    }
    error_log("Save result error: " . $e->getMessage());
    jsonResponse(['success' => false, 'error' => 'Failed to save result'], 500);
}

/**
 * Check and award achievements by type
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
            try {
                insertData('user_achievements', [
                    'user_id' => $userId,
                    'achievement_id' => $achievement['id']
                ]);
                $awarded[] = $achievement;
            } catch (Exception $e) {
                // Already awarded, skip
            }
        }
    }

    return $awarded;
}

/**
 * Check special achievements by name
 */
function checkSpecialAchievement($userId, $name)
{
    $achievement = fetchOne(
        "SELECT a.* FROM achievements a
         LEFT JOIN user_achievements ua ON a.id = ua.achievement_id AND ua.user_id = ?
         WHERE ua.id IS NULL AND a.name = ?",
        [$userId, $name]
    );

    if ($achievement) {
        try {
            insertData('user_achievements', [
                'user_id' => $userId,
                'achievement_id' => $achievement['id']
            ]);
            return [$achievement];
        } catch (Exception $e) {
            // Already awarded
        }
    }

    return [];
}
