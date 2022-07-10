<?php

define("GLOBAL_TITLE", "PIK Piłka Nożna");
define("TITLE_LIGHT_SEPARATOR", " • ");
define("TITLE_HEAVY_SEPARATOR", " » ");

define("ERROR_REPORTING", !file_exists(ROOT_PATH . "/config.ini") or false);
define("MAINTENANCE", false);
define(
    "MOTD",
    null
);
