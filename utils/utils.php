<?php

function cast_int($x)
{
    if (!is_numeric($x) or $x === '') {
        return null;
    }
    return (int)$x;
}

function coalesce()
{
    foreach (func_get_args() as $e) {
        if (!is_null($e)) {
            return $e;
        }
    }
    return null;
}

function tuple_export($i, $arr)
{
    return array_map(function ($x) use ($i) { // https://stackoverflow.com/a/11420541/6732111
        return $x[$i];
    }, $arr);
}

function relative_path($path, $root = null)
{
    if ($root === null) {
        $root = ROOT_PATH;
    }
    $path = realpath($path);
    $root = realpath($root);
    if (!$path or !$root) {
        return false;
    }
    $helper = function ($prev, $next) {
        if ($prev === $next) {
            return ["", ""];
        }
        return [$prev ? "/.." : "", $next ? "/$next" : ""];
    };
    $arr = array_map($helper, explode("/", $root), explode("/", $path));
    $relative = implode("", tuple_export(0, $arr)) . implode("", tuple_export(1, $arr));
    if (strlen($relative) > 0 and $relative[0] === "/") {
        $relative = substr($relative, 1);
    }
    return $relative;
}
