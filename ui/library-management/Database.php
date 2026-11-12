<?php

$host = "localhost";
$dbname = "library_db";
$user = "root";
$password = "root";

try {

    $conn = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $user,
        $password
    );

    $conn->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

} catch (PDOException $e) {

    die("Database connection failed: " . $e->getMessage());

}
?>