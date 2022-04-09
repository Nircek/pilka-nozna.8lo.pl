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
            reportError("db", "No valid config found.");
            return false;
        }
        $dbname = $ini["dbname"];
        $host = $ini["host"];
        $login = $ini["login"];
        $haslo = $ini["password"];

        try {
            $pdo = new PDO("mysql:host=$host; dbname=$dbname", $login, $haslo);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->exec('SET NAMES "utf8"');
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
            return $pdo;
        } catch (PDOException $e) {
            reportError("db", $e->getMessage());
        }
        return false;
    }
}
