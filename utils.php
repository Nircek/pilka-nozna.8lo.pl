<?php
define('ROOT_PATH', __DIR__);
define("PREFIX", (ROOT_PATH === $_SERVER['DOCUMENT_ROOT'] ? '' : '/') . relative_path(ROOT_PATH, $_SERVER['DOCUMENT_ROOT']));
require_once('config.php');

function trace_assert($bool, $desc = NULL)
{
    if ($bool) return;
    echo str_replace("\n", "<br/>\n", (new Exception)->getTraceAsString());
    assert($bool, $desc);
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
    trace_assert($path and $root, "corrupted paths");
    $helper = function ($prev, $next) {
        if ($prev === $next) return ["", ""];
        return [$prev ? "/.." : "", $next ? "/$next" : ""];
    };
    $arr = array_map($helper, explode("/", $root), explode("/", $path));
    $relative = implode("", tuple_export(0, $arr)) . implode("", tuple_export(1, $arr));
    if ($relative[0] === "/") $relative = substr($relative, 1);
    return $relative;
}

require_once('template/template.php');
