<?php

require __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$start_year = 24; 
$end_year = 25;    
$issue_date = '27 MAY 2025';
$deadline = '9 MAY 2025';

$host = 'localhost';
$db = 'slprecord_' . $start_year . $end_year;  
$dbuser = $_ENV['DB_USER'];
$pass = $_ENV['DB_PASS'];

$dsn = "mysql:host=$host;dbname=$db";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $dbuser, $pass, $options);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}