<?php

function border_passed($datetimestr, $def = false)
{
    return is_null($datetimestr) ? $def : strtotime($datetimestr) < time();
}
function load_config_file($file)
{
    if (!file_exists($file)) {
        return false;
    }
    return parse_ini_file($file);
}
function array_emplace($key, & $arr)
{
    $arr[$key] = array_key_exists($key, $arr) ? $arr[$key] : null;
}

if (!($config_ini = load_config_file(ROOT_PATH . "/config.ini"))) {
    if (!($config_ini = load_config_file(ROOT_PATH . "/config.sample.ini"))) {
        SERVER_ERROR(500, "No valid config found.");
    }
}

session_start();
# inspired by https://stackoverflow.com/a/1270960/6732111
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $config_ini['SESSION_TIMEOUT'])) {
    session_unset();
    session_destroy();
}
$_SESSION['LAST_ACTIVITY'] = time();
if (!isset($_SESSION['CREATED'])) {
    $_SESSION['CREATED'] = time();
} elseif (time() - $_SESSION['CREATED'] > 1800) {
    session_regenerate_id(true);
    $_SESSION['CREATED'] = time();
}

define("ERROR_REPORTING", !file_exists(ROOT_PATH . "/config.ini") or false);

array_emplace('BORDER_TIME', $config_ini);
array_emplace('BORDER_AUTHOR', $config_ini);
array_emplace('BORDER_NOTICE_TIME', $config_ini);
array_emplace('FORCED_MOTD', $config_ini);
$config_ini['FORCED_MAINTENANCE'] = array_key_exists('FORCED_MAINTENANCE', $config_ini);

date_default_timezone_set($config_ini['TIMEZONE']);
$time_border_active = border_passed($config_ini['BORDER_TIME']);
define(
    "MAINTENANCE",
    $time_border_active or
    $config_ini['FORCED_MAINTENANCE']
);
$current_datetime = date("c");
define(
    "MOTD",
    is_null($config_ini['FORCED_MOTD']) ? (
        is_null($config_ini['BORDER_TIME']) ?
            (border_passed($config_ini['BORDER_NOTICE_TIME'], true) ? null : "Pomyślnie wprowadzono nowe zmiany. Pozdrawiam.".(is_null($config_ini['BORDER_AUTHOR']) ? "" : " ~{$config_ini['BORDER_AUTHOR']}"))
            : (
                $time_border_active ?
                "Wprowadzanie nowych zmian. Logowanie jest zablokowane.<br>Prosimy o cierpliwość." :
                "Czas serwera załadowania strony: $current_datetime.<br>Logowanie będzie zablokowane po {$config_ini['BORDER_TIME']}."
                ."<br>Użytkownicy zostaną wylogowani.<br>Jest to potrzebne do wprowadzenia nowych zmian."
            ).(is_null($config_ini['BORDER_AUTHOR']) ? "" : "<br>~{$config_ini['BORDER_AUTHOR']}")
    ) : $config_ini['FORCED_MOTD']
);
