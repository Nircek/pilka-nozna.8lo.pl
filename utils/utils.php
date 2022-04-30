<?php
require_once(ROOT_PATH . '/config.php');

function cast_int($x)
{
    if (!is_numeric($x)) return NULL;
    return $x + 0;
}

function coalesce() {
    foreach(func_get_args() as $e)
        if(!is_null($e))
            return $e;
    return null;

}

function load_config_file($file)
{
    if (!file_exists($file)) return false;
    return parse_ini_file($file);
}

function arrayify($x)
{
    return is_array($x) ? $x : explode(',', $x);
}

function tuple_export($i, $arr)
{
    return array_map(function ($x) use ($i) { // https://stackoverflow.com/a/11420541/6732111
        return $x[$i];
    }, $arr);
}

function relative_path($path, $root = NULL)
{
    if ($root === NULL) $root = ROOT_PATH;
    $path = realpath($path);
    $root = realpath($root);
    if (!$path or $root)
        return false;
    $helper = function ($prev, $next) {
        if ($prev === $next) return ["", ""];
        return [$prev ? "/.." : "", $next ? "/$next" : ""];
    };
    $arr = array_map($helper, explode("/", $root), explode("/", $path));
    $relative = implode("", tuple_export(0, $arr)) . implode("", tuple_export(1, $arr));
    if (strlen($relative) > 0 and $relative[0] === "/") $relative = substr($relative, 1);
    return $relative;
}
