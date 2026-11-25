
<?php

// Run this file only through PHP command line
if (PHP_SAPI !== 'cli') {
    exit("This script can only run from the command line.");
}

define('BASE_PATH', dirname(__DIR__));

require_once BASE_PATH . '/app/core/Database.php';

$db = Database::connect();

$users = [
    [
        'username' => 'admin',
        'email' => 'admin@hospital.com',
        'password' => 'Admin@123',
        'role' => 'admin'
    ],
    [
        'username' => 'doctor',
        'email' => 'doctor@hospital.com',
        'password' => 'Doctor@123',
        'role' => 'doctor'
    ],
    [
        'username' => 'receptionist',
        'email' => 'receptionist@hospital.com',
        'password' => 'Reception@123',
        'role' => 'receptionist'
    ],
    [
        'username' => 'nurse',
        'email' => 'nurse@hospital.com',
        'password' => 'Nurse@123',
        'role' => 'nurse'
    ]
];

$sql = "INSERT INTO users
        (username, email, password, role)
        VALUES
        (:username, :email, :password, :role)";

$stmt = $db->prepare($sql);

foreach ($users as $user) {

    $check = $db->prepare(
        "SELECT id FROM users
         WHERE username = :username"
    );

    $check->execute([
        ':username' => $user['username']
    ]);

    if ($check->fetch()) {
        echo $user['username'] . " already exists.\n";
        continue;
    }

    $hashedPassword = password_hash(
        $user['password'],
        PASSWORD_DEFAULT
    );

    $stmt->execute([
        ':username' => $user['username'],
        ':email' => $user['email'],
        ':password' => $hashedPassword,
        ':role' => $user['role']
    ]);

    echo $user['username'] . " created successfully.\n";
}

echo "\nDemo users creation completed.\n";

