<?php
// Test Bootstrap
// Sets up the testing environment

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../includes/grade_calculator.php'; // For unit tests
require_once __DIR__ . '/../config/database.php';

// --- Test Database Setup ---

function setupTestDatabase() {
    // Use an in-memory SQLite database for testing
    // This provides a clean slate for each test run
    $db = new PDO('sqlite::memory:');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Load and execute the base schema
    $schema = file_get_contents(__DIR__ . '/../../database/schema.sql');
    // SQLite doesn't support all MySQL features, so we need to do some replacements
    $schema = preg_replace('/ENGINE=InnoDB.*?;/', ';', $schema);
    $schema = preg_replace('/AUTO_INCREMENT/', 'PRIMARY KEY AUTOINCREMENT', $schema);
    $schema = preg_replace('/ON UPDATE CURRENT_TIMESTAMP/', '', $schema);
    $schema = preg_replace('/TIMESTAMP DEFAULT CURRENT_TIMESTAMP/', 'DATETIME DEFAULT CURRENT_TIMESTAMP', $schema);
    $schema = preg_replace('/CHECK \(semester BETWEEN 1 AND 6\)/', '', $schema);
    $schema = preg_replace('/ENUM\(.*?\)/', 'TEXT', $schema);

    // Execute the modified schema
    $db->exec($schema);

    // --- Seed the database with test users ---
    $users = [
        [
            'username' => 'superadmin',
            'password' => password_hash('password', PASSWORD_DEFAULT),
            'email' => 'admin@test.com',
            'role' => 'admin',
        ],
        [
            'username' => 'testteacher',
            'password' => password_hash('password', PASSWORD_DEFAULT),
            'email' => 'teacher@test.com',
            'role' => 'teacher',
        ],
        [
            'username' => 'teststudent',
            'password' => password_hash('password', PASSWORD_DEFAULT),
            'email' => 'student@test.com',
            'role' => 'student',
        ],
    ];

    $stmt = $db->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, ?)");
    foreach ($users as $user) {
        $stmt->execute([$user['username'], $user['password'], $user['email'], $user['role']]);
    }

    return $db;
}

// Store the test database in a global variable for access in tests
$GLOBALS['test_db'] = setupTestDatabase();
