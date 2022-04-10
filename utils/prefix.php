<?php

define("PREFIX", (ROOT_PATH === $_SERVER["DOCUMENT_ROOT"] ? "" : "/") . relative_path(ROOT_PATH, $_SERVER["DOCUMENT_ROOT"]));
$route = $_SERVER["REDIRECT_URL"];
if (substr($route, 0, strlen(PREFIX) + 1) === PREFIX . "/") $route = substr($route, strlen(PREFIX) + 1); // utf-8 friendly
else $route = "./$route";
define("REDIRECT_ROUTE", $route);
$HIT_ROUTE = $route === false ? array() : explode("/", $route); // I know it is easier to pop than shift, but...
$HIT_METHOD = $_SERVER["REQUEST_METHOD"];
function HIT_PACK($what)
{
    global $HIT_ROUTE;
    return array_unshift($HIT_ROUTE, $what); // ...readability is key
}
function HIT_UNPACK()
{
    global $HIT_ROUTE;
    return array_shift($HIT_ROUTE); // ...readability is key
}
unset($route);
function NOT_FOUND()
{
    http_response_code(404);
}
if (isset($_SERVER['REDIRECT_HTTPCODE']) and $_SERVER['REDIRECT_HTTPCODE'] === "404") // see (♡) in .htaccess
    NOT_FOUND();

define("HIT_SITES", ROOT_PATH . "/sites");
function HIT_RESOLVE($dir = HIT_SITES)
{
    $controller = HIT_UNPACK();
    if (empty($controller)) $controller = "index";
    $controller = "$dir/$controller.php";
    if (in_array($controller, glob("$dir/*.php"))) require($controller);
    else NOT_FOUND();
}
