<?php
// Test Bootstrap
// Sets up the testing environment

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../includes/grade_calculator.php'; // For unit tests
require_once __DIR__ . '/../config/database.php';

// --- Seeding Functions ---

function seedUsers(PDO $db) {
    $users = [
        ['id' => 1, 'username' => 'superadmin', 'password' => password_hash('password', PASSWORD_DEFAULT), 'email' => 'admin@test.com', 'role' => 'admin'],
        ['id' => 2, 'username' => 'testteacher', 'password' => password_hash('password', PASSWORD_DEFAULT), 'email' => 'teacher@test.com', 'role' => 'teacher'],
        ['id' => 3, 'username' => 'teststudent', 'password' => password_hash('password', PASSWORD_DEFAULT), 'email' => 'student@test.com', 'role' => 'student'],
    ];

    $stmt = $db->prepare("INSERT INTO users (id, username, password, email, role) VALUES (?, ?, ?, ?, ?)");
    foreach ($users as $user) {
        $stmt->execute([$user['id'], $user['username'], $user['password'], $user['email'], $user['role']]);
    }
}

function seedMarksData(PDO $db) {
    // Add a student
    $db->exec("INSERT INTO students (id, user_id, student_id, first_name, last_name, date_of_birth, gender, enrollment_date, session_id, semester, department, batch_year) VALUES (1, 3, 'S123', 'Test', 'Student', '2000-01-01', 'male', '2023-01-01', 1, 1, 'Computer Science', 2023)");

    // Add subjects
    $db->exec("INSERT INTO subjects (id, subject_code, subject_name, credit_hours, semester) VALUES (1, 'CS101', 'Intro to CS', 3, 1)");
    $db->exec("INSERT INTO subjects (id, subject_code, subject_name, credit_hours, semester) VALUES (2, 'MA101', 'Calculus I', 4, 1)");

    // Add marks
    $db->exec("INSERT INTO marks (student_id, subject_id, session_id, semester, total_marks, grade_point, letter_grade) VALUES (1, 1, 1, 1, 85, 3.75, 'A')");
    $db->exec("INSERT INTO marks (student_id, subject_id, session_id, semester, total_marks, grade_point, letter_grade) VALUES (1, 2, 1, 1, 92, 4.00, 'A+')");
}

function seedExamMarksData(PDO $db) {
    // Add a teacher
    $db->exec("INSERT INTO teachers (id, user_id, teacher_id, first_name, last_name, date_of_birth, gender, joining_date, department) VALUES (1, 2, 'T123', 'Test', 'Teacher', '1980-01-01', 'female', '2020-01-01', 'Computer Science')");

    // Add exam marks
    $db->exec("INSERT INTO exam_marks (student_id, subject_id, semester, exam_type, marks_obtained, max_marks, entered_by) VALUES (1, 1, 1, 'internal_1', 40, 50, 1)");
}


// --- Test Database Setup ---

function setupTestDatabase() {
    $db = new PDO('sqlite::memory:');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $schema = file_get_contents(__DIR__ . '/../../database/schema.sql');
    $schema = preg_replace('/ENGINE=InnoDB.*?;/', ';', $schema);
    $schema = preg_replace('/AUTO_INCREMENT/', 'PRIMARY KEY AUTOINCREMENT', $schema);
    $schema = preg_replace('/ON UPDATE CURRENT_TIMESTAMP/', '', $schema);
    $schema = preg_replace('/TIMESTAMP DEFAULT CURRENT_TIMESTAMP/', 'DATETIME DEFAULT CURRENT_TIMESTAMP', $schema);
    $schema = preg_replace('/CHECK \(semester BETWEEN 1 AND 6\)/', '', $schema);
    $schema = preg_replace('/ENUM\(.*?\)/', 'TEXT', $schema);
    $db->exec($schema);

    // Create a dummy exam_marks table for the test
    $db->exec("CREATE TABLE IF NOT EXISTS exam_marks (id INTEGER PRIMARY KEY AUTOINCREMENT, student_id INTEGER, subject_id INTEGER, semester INTEGER, exam_type TEXT, marks_obtained INTEGER, max_marks INTEGER, entered_by INTEGER, exam_date DATETIME, updated_at DATETIME)");

    seedUsers($db);
    seedMarksData($db);
    seedExamMarksData($db);

    return $db;
}

$GLOBALS['test_db'] = setupTestDatabase();
