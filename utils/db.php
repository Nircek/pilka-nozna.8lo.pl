<?php

class CmdPDO
{
    # SRC: https://stackoverflow.com/a/3012389/6732111
    protected $_instance;

    public function cmd($cmd, $arg = array())
    {
        $i = 0;
        foreach ($this->cmds[$cmd] as $sql) {
            $stmt = $this->_instance->prepare($sql);
            $stmt->execute($arg);
            ++$i;
        }
        if (count($this->cmds[$cmd]) > 1) {
            return $i;
        }
        return $stmt;
    }

    public function __construct(PDO $instance, $cmds)
    {
        $this->_instance = $instance;
        $this->cmds = $cmds;
    }

    public function __call($method, $args)
    {
        return call_user_func_array(array($this->_instance, $method), $args);
    }

    public function __get($key)
    {
        return $this->_instance->$key;
    }

    public function __set($key, $val)
    {
        return $this->_instance->$key = $val;
    }
}


final class PDOS
{
    public static function Instance() // singletons are bad but no one would test it
    { // https://stackoverflow.com/a/203359/6732111
        static $inst = null;
        if ($inst === null) {
            try {
                $r = PDOS::_construct();
                $inst = new CmdPDO($r[0], $r[1]);
                if ($inst->cmd("@init()") === 0) {
                    report_error("db", "@init() = 0");
                }
            } catch (PDOException $e) {
                report_error("db", $e->getMessage());
                $inst = false;
            }
        }
        return $inst;
    }
    private static function _construct()
    {
        $ini = load_config_file(ROOT_PATH . "/config.ini");
        if (!$ini) {
            $ini = load_config_file(ROOT_PATH . "/config.sample.ini");
        }
        if (!$ini) {
            report_error("db", "No valid config found.");
            return false;
        }
        $dbname = $ini["dbname"];
        $host = $ini["host"];
        $login = $ini["login"];
        $haslo = $ini["password"];

        $sql = file_get_contents(ROOT_PATH . "/utils/commands.sql");
        if ($sql === false) {
            report_error("db", "No valid commands found.");
            return false;
        }
        preg_match_all('/^-- ([^#\n]*)(?: #.*)?\n([^;]*;)/m', $sql, $raw_cmds, PREG_SET_ORDER);
        $cmds = array();
        foreach ($raw_cmds as $match) {
            if (!array_key_exists($match[1], $cmds)) {
                $cmds[$match[1]] = array($match[2]);
            } else {
                if ($match[1][0] !== "@") {
                    report_error("db", "multiline in `{$match[1]}`");
                }
                array_push($cmds[$match[1]], $match[2]);
            }
        }
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $login, $haslo, array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_PERSISTENT => false
        ));
        return array($pdo, $cmds);
    }
}

function obecny_sezon()
{
    $arr = PDOS::Instance()->cmd("get_latest_season()")->fetchAll(PDO::FETCH_COLUMN);
    return $arr ? $arr[0] : null;
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
