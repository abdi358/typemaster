<?php
/**
 * Database Setup Script
 * Use this to initialize the database on Railway
 * DELETE THIS FILE AFTER USE!
 */

require_once 'db/connect.php';

// Check for secret key to prevent unauthorized access (simple security)
$key = $_GET['key'] ?? '';
if ($key !== 'typemaster2024') {
    die('Unauthorized access. Please provide the correct key.');
}

try {
    $pdo = getDBConnection();
    echo "<h1>Database Setup</h1>";
    echo "<p>Connected to database successfully.</p>";

    // Read schema file
    $schemaSql = file_get_contents(__DIR__ . '/db/schema.sql');
    if (!$schemaSql) {
        throw new Exception("Could not read schema.sql");
    }

    // Split SQL into statements (simple split by ;)
    // Note: detailed parsing might be needed for complex logic, but this works for basic schemas
    $pdo->exec($schemaSql);
    echo "<p>✅ Schema initialized successfully.</p>";

    // Read seed data
    $seedSql = file_get_contents(__DIR__ . '/db/seed_data.sql');
    if ($seedSql) {
        $pdo->exec($seedSql);
        echo "<p>✅ Seed data imported successfully.</p>";
    } else {
        echo "<p>⚠️ No seed data found or file empty.</p>";
    }

    echo "<h3>Setup Complete!</h3>";
    echo "<p>You can now <a href='index.php'>Go to Home Page</a></p>";
    echo "<p><strong>IMPORTANT:</strong> Delete this file (setup.php) from your repository/server now.</p>";

} catch (PDOException $e) {
    echo "<h2>Error</h2>";
    echo "<p style='color:red'>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
} catch (Exception $e) {
    echo "<h2>General Error</h2>";
    echo "<p style='color:red'>" . htmlspecialchars($e->getMessage()) . "</p>";
}
