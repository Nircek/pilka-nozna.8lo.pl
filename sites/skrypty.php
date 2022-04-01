<?php

$controller = HIT_UNPACK();
if ($controller === null) $controller = "index";
$controller = ROOT_PATH . "/sites/skrypty/$controller.php";
if (in_array($controller, glob(ROOT_PATH . "/sites/skrypty/*.php"))) require($controller);
else NOT_FOUND();
