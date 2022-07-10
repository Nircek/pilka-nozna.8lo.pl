<?php

mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

session_start();

function SERVER_ERROR($code = 500, $msg = null)
{
    http_response_code($code);
    if (!is_null($msg)) {
        print($msg);
    }
    die();
}
if (!isset($_SERVER["REDIRECT_URL"])) { // I think it is always present in Apache2 setups
    SERVER_ERROR(501, "REDIRECT_URL not in _SERVER");
}


define("ROOT_PATH", __DIR__);
require_once(ROOT_PATH . '/config.php');
require_once(ROOT_PATH . "/utils/utils.php");
require_once(ROOT_PATH . "/utils/error.php");
require_once(ROOT_PATH . "/utils/prefix.php");
require_once(ROOT_PATH . "/utils/db.php");
require_once(ROOT_PATH . "/utils/register.php");

HIT_RESOLVE();
if (http_response_code() === 404) {
    require_once(ROOT_PATH . "/sites/404.php");
}
$obj = null;
if (function_exists('page_init')) {
    $obj = page_init();
}
if (function_exists('page_render')) {
    if (isset($page_norender) and $page_norender) {
        page_render($obj);
    } else {
        require(ROOT_PATH . '/template/template.php');
    }
}
