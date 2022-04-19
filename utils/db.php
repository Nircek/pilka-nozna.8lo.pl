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
            $arr = array(
                "CREATE TABLE IF NOT EXISTS `ng_article` (
                    `article_id` int(10) UNSIGNED NOT NULL,
                    `season_id` int(10) UNSIGNED DEFAULT NULL,
                    `title` tinytext COLLATE utf8mb4_polish_ci NOT NULL,
                    `author_id` int(10) UNSIGNED NOT NULL,
                    `publish_on_news_page` tinyint(1) NOT NULL,
                    `is_subpage` tinyint(1) NOT NULL,
                    `content` mediumtext COLLATE utf8mb4_polish_ci NOT NULL,
                    `created_at` date NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;",
                "CREATE TABLE IF NOT EXISTS `ng_game` (
                    `game_id` int(10) UNSIGNED NOT NULL,
                    `season_id` int(10) UNSIGNED NOT NULL,
                    `type` enum('first','second','half1','half2','final','third') COLLATE utf8mb4_polish_ci NOT NULL,
                    `date` date NOT NULL,
                    `A_team_id` int(10) NOT NULL,
                    `A_score` int(11) DEFAULT NULL,
                    `B_score` int(11) DEFAULT NULL,
                    `B_team_id` int(10) NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;",
                "CREATE TABLE IF NOT EXISTS `ng_photo` (
                    `photo_id` int(11) UNSIGNED NOT NULL,
                    `season_id` int(10) UNSIGNED NOT NULL,
                    `game_id` int(10) UNSIGNED DEFAULT NULL,
                    `date` date NOT NULL,
                    `type` enum('filename','url') COLLATE utf8mb4_polish_ci NOT NULL,
                    `content` text COLLATE utf8mb4_polish_ci NOT NULL,
                    `photographer_id` int(10) UNSIGNED NOT NULL,
                    `credit_photographer` tinyint(1) NOT NULL DEFAULT 0,
                    `comment` text COLLATE utf8mb4_polish_ci DEFAULT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;",
                "CREATE TABLE IF NOT EXISTS `ng_season` (
                    `season_id` int(10) UNSIGNED NOT NULL,
                    `created_at` date NOT NULL,
                    `name` tinytext COLLATE utf8mb4_polish_ci NOT NULL,
                    `html_name` tinytext COLLATE utf8mb4_polish_ci NOT NULL,
                    `description` text COLLATE utf8mb4_polish_ci,
                    `grouping_type` enum('no_grouping','two_rounds','two_groups') COLLATE utf8mb4_polish_ci NOT NULL,
                    `comment` text COLLATE utf8mb4_polish_ci
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;",
                "CREATE TABLE IF NOT EXISTS `ng_team` (
                    `season_id` int(10) UNSIGNED NOT NULL,
                    `team_id` int(10) NOT NULL,
                    `name` varchar(42) COLLATE utf8mb4_polish_ci NOT NULL,
                    `photo_id` int(10) UNSIGNED NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;",
                "CREATE TABLE IF NOT EXISTS `ng_user` (
                    `active` tinyint(1) NOT NULL,
                    `user_id` int(10) UNSIGNED NOT NULL,
                    `login` tinytext COLLATE utf8mb4_polish_ci NOT NULL,
                    `password` varchar(255) COLLATE utf8mb4_polish_ci NOT NULL COMMENT 'plain if `change_password_at_next_login` else hash',
                    `change_password_at_next_login` tinyint(1) NOT NULL,
                    `pretty_name` varchar(255) COLLATE utf8mb4_polish_ci NOT NULL,
                    `last_logged_at` date NOT NULL,
                    `registered_at` date NOT NULL,
                    `comment` text COLLATE utf8mb4_polish_ci,
                    `admin` tinyint(1) NOT NULL,
                    `photographer` tinyint(1) NOT NULL,
                    `referee` tinyint(1) NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;"
            );
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
    $arr = PDOS::Instance()->query( // get_latest_season()
        "SELECT `season_id` FROM `ng_season` ORDER BY `created_at` DESC LIMIT 1"
    )->fetchAll(PDO::FETCH_COLUMN);
    return $arr ? $arr[0] : NULL;
}

define("ADMIN_LOGIN_URL", PREFIX . "/admin/login");
function is_logged($required = true)
{
    $logged = isset($_SESSION['zalogowany']);
    if ($logged and MAINTENANCE) {
        unset($_SESSION['zalogowany']);
    }
    if ($required and !$logged) {
        header("Location: " . ADMIN_LOGIN_URL);
        exit();
    }
    return $logged;
}
