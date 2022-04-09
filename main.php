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
require_once(ROOT_PATH . "/utils/utils.php");
require_once(ROOT_PATH . "/utils/error.php");
require_once(ROOT_PATH . "/utils/prefix.php");
require_once(ROOT_PATH . "/utils/db.php");

try {
    HIT_RESOLVE();
} catch (Exception $e) {
    reportError("exception", $e->getMessage());
}
