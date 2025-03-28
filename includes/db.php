<?php
$host = 'localhost'; // Hostname of your DB
$dbname = 'your_db_name'; // DB Name
$username = 'username'; // Username
$password = 'password'; // Password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->exec("SET NAMES utf8");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>
