<?php

mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

function SERVER_ERROR($code = 500, $msg = null)
{
    http_response_code($code);
    if (!is_null($msg)) {
        print($msg);
    }
    die();
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

if ($_SERVER['REQUEST_METHOD'] === "POST" and function_exists('page_perform')) {
    page_perform();
}
$obj = null;
if (function_exists('page_init')) {
    $obj = page_init();
}
if (function_exists('page_render')) {
    if (isset($page_norender) and $page_norender) {
        page_render($obj); // render without template
    } else {
        require(ROOT_PATH . '/template/template.php');
    }
}
