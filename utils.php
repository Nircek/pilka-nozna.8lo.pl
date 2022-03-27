<?php
define("ROOT_PATH", __DIR__);
require_once("config.php");

function trace_assert($bool, $desc = NULL)
{
    if ($bool) return;
    echo str_replace("\n", "<br/>\n", (new Exception)->getTraceAsString());
    assert($bool, $desc);
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

function get_page_title($file, $add = NULL)
{

    $file = relative_path($file);
    global $PRETTY_PAGE_TITLES;
    $title = "";
    $sep1 = TITLE_LIGHT_SEPARATOR;
    $sep2 = TITLE_HEAVY_SEPARATOR;
    $pretty_title = $PRETTY_PAGE_TITLES[$file];
    if ($add and $pretty_title) $title = "$add $sep1 $pretty_title";
    else $title = $add ? $add : $pretty_title;
    if ($title) $title .= " $sep2 ";
    $title .= GLOBAL_TITLE;
    return $title;
}
