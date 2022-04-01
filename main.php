<?php
session_start();

function SERVER_ERROR($code = 500)
{
    http_response_code($code);
    die();
}
if (!isset($_SERVER["REDIRECT_URL"])) // I think it is always present in Apache2 setups
    SERVER_ERROR(501);


define("ROOT_PATH", __DIR__);
require_once(ROOT_PATH . "/utils.php");
define("PREFIX", (ROOT_PATH === $_SERVER["DOCUMENT_ROOT"] ? "" : "/") . relative_path(ROOT_PATH, $_SERVER["DOCUMENT_ROOT"]));
$route = $_SERVER["REDIRECT_URL"];
if (substr($route, 0, strlen(PREFIX) + 1) === PREFIX . "/") $route = substr($route, strlen(PREFIX) + 1); // utf-8 friendly
else $route = "./$route";
define("REDIRECT_ROUTE", $route);
$HIT_ROUTE = $route === false ? array() : explode("/", $route); // I know it is easier to pop than shift, but...
$HIT_METHOD = $_SERVER["REQUEST_METHOD"];
function HIT_UNPACK()
{
    global $HIT_ROUTE;
    return array_shift($HIT_ROUTE); // ...readability is key
}
unset($route);
function NOT_FOUND()
{
    http_response_code(404);
    require(ROOT_PATH . "/sites/404.php");
    die();
}
if ($_SERVER['REDIRECT_HTTPCODE'] === "404") // see (♡) in .htaccess
    NOT_FOUND();


$controller = HIT_UNPACK();
if ($controller === null) $controller = "index";
$controller = ROOT_PATH . "/sites/$controller.php";
if (in_array($controller, glob(ROOT_PATH . "/sites/*.php"))) require($controller);
else NOT_FOUND();
