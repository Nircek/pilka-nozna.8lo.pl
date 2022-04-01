<?php

$ini = @parse_ini_file(__DIR__ . "/../config.ini"); // relative to `..` folder!
if (!$ini) $ini = @parse_ini_file(__DIR__ . "/../config.sample.ini"); // relative to `..` folder!
if (!$ini) {
    echo "Failed to load db.";
    die();
}
$dbname = $ini["dbname"];
$host = $ini["host"];
$login = $ini["login"];
$haslo = $ini["password"];

// KONFIGURACJA POŁĄCZENIA
try {
    $pdo = new PDO("mysql:host=$host; dbname=$dbname", $login, $haslo);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec('SET NAMES "utf8"');
} catch (PDOException $e) {
    $output = 'Nie można nawiązać połączenia z bazą danych ' . $e->getMessage();
}
$arr = array("CREATE TABLE IF NOT EXISTS `sezony` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `sezon` int(11) NOT NULL,
                PRIMARY KEY(`id`)
            )", "CREATE TABLE IF NOT EXISTS `zdjecia` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `sezon` int(11) NOT NULL,
                `sciezka` text COLLATE utf8_polish_ci NOT NULL,
                `data` date NOT NULL,
                PRIMARY KEY(`id`)
            )", "CREATE TABLE IF NOT EXISTS `informacje` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `tytul` text COLLATE utf8_polish_ci NOT NULL,
                `tresc` text COLLATE utf8_polish_ci NOT NULL,
                `data` date NOT NULL,
                PRIMARY KEY(`id`)
            )");
foreach ($arr as $sql) {
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
}
