<?php

function reportError($known, $desc)
{
    $error = json_encode([$known, $desc, str_replace("\n", "<br/>\n", (new Exception)->getTraceAsString())]);
    $_SESSION["error_queue"] = (isset($_SESSION["error_queue"]) ? $_SESSION["error_queue"] . "," : "") . $error;
    $nr = count(json_decode("[" . $_SESSION["error_queue"] . "]")) - 1;
    echo "<!-- report $known #$nr -->";
    return true;
}

function reportAssert($bool, $known, $desc)
{
    if ($bool) return false;
    reportError($known, $desc);
    return true;
}

function popReports()
{
    $queue = isset($_SESSION["error_queue"]) ? $_SESSION["error_queue"] : "";
    $_SESSION["error_queue"] = "";
    unset($_SESSION["error_queue"]);
    return json_decode("[$queue]");
}

register_shutdown_function("fatal_handler");
function fatal_handler()
{
    $last = error_get_last();
    if ($last !== null) reportError("shutdown-handler", var_export($last, true));
}
set_error_handler(function ($errno, $errstr, $errfile = null, $errline = null, $errcontext = null) {
    reportError("error-handler", var_export([$errno, $errstr, $errfile, $errline], true));
});
