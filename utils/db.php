<?php
final class PDOS
{
    public static function Instance() // singletons are bad but no one would test it
    { // https://stackoverflow.com/a/203359/6732111
        static $inst = null;
        if ($inst === null) {
            $inst = PDOS::_construct();
        }
        return $inst;
    }
    private static function _construct()
    {
        $ini = load_config_file(ROOT_PATH . "/config.ini");
        if (!$ini) $ini = load_config_file(ROOT_PATH . "/config.sample.ini");
        if (!$ini) {
            report_error("db", "No valid config found.");
            return false;
        }
        $dbname = $ini["dbname"];
        $host = $ini["host"];
        $login = $ini["login"];
        $haslo = $ini["password"];

        try {
            $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $login, $haslo, array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_PERSISTENT => false
            ));
            $arr = array("CREATE TABLE IF NOT EXISTS `sezony` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `sezon` int(11) NOT NULL,
            PRIMARY KEY(`id`)
        )", "CREATE TABLE IF NOT EXISTS `zdjecia` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `sezon` int(11) NOT NULL,
            `sciezka` text COLLATE utf8mb4_general_ci NOT NULL,
            `data` date NOT NULL,
            PRIMARY KEY(`id`)
        )", "CREATE TABLE IF NOT EXISTS `informacje` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `tytul` text COLLATE utf8mb4_general_ci NOT NULL,
            `tresc` text COLLATE utf8mb4_general_ci NOT NULL,
            `data` date NOT NULL,
            PRIMARY KEY(`id`)
        )");
            foreach ($arr as $sql) {
                $stmt = $pdo->prepare($sql);
                $stmt->execute();
            }
            return $pdo;
        } catch (PDOException $e) {
            report_error("db", $e->getMessage());
        }
        return false;
    }
}

function obecny_sezon()
{
    $arr = PDOS::Instance()->query("SELECT sezon FROM sezony ORDER BY sezon DESC LIMIT 1")->fetchAll(PDO::FETCH_COLUMN);
    return $arr ? $arr[0] : NULL;
}

// Sprawdzanie czy tabela istnieje
function sprawdzanie_tabela($tabela)
{
    try {
        PDOS::Instance()->query("SELECT * FROM $tabela");
    } catch (PDOException $_) {
        return false;
    }
    return true;
}

define("ADMIN_LOGIN_URL", PREFIX . "/admin/login");
function is_logged($required = true)
{
    $logged = isset($_SESSION['zalogowany']);
    if($logged and MAINTENANCE) {
        unset($_SESSION['zalogowany']);
    }
    if ($required and !$logged) {
        header("Location: " . ADMIN_LOGIN_URL);
        exit();
    }
    return $logged;
}
