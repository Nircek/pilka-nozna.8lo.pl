<?php

$REGISTERED_STYLES = array();

function register_style($name)
{
    global $REGISTERED_STYLES;
    if (!in_array($name, $REGISTERED_STYLES)) {
        $REGISTERED_STYLES[] = $name;
    }
}

$REGISTERED_TITLE = "";
$REGISTERED_ADDITIONAL_TITLE = "";

function register_title($title)
{
    global $REGISTERED_TITLE;
    $REGISTERED_TITLE = $title;
}
function register_additional_title($title)
{
    global $REGISTERED_ADDITIONAL_TITLE, $config_ini;
    $REGISTERED_ADDITIONAL_TITLE = $title . empty($REGISTERED_ADDITIONAL_TITLE) ?
        '' : $config_ini['TITLE_LIGHT_SEPARATOR'] . $REGISTERED_ADDITIONAL_TITLE;
}

function get_page_title()
{
    global $REGISTERED_TITLE, $REGISTERED_ADDITIONAL_TITLE, $config_ini;
    $title = "";
    if (!empty($REGISTERED_ADDITIONAL_TITLE) and !empty($REGISTERED_TITLE)) {
        $title = $REGISTERED_ADDITIONAL_TITLE . $config_ini['TITLE_LIGHT_SEPARATOR'] . $REGISTERED_TITLE;
    } else {
        $title = empty($REGISTERED_TITLE) ? $REGISTERED_ADDITIONAL_TITLE : $REGISTERED_TITLE;
    }
    if ($title) {
        $title .= $config_ini['TITLE_HEAVY_SEPARATOR'] . $config_ini['GLOBAL_TITLE'];
    }
    return $title;
}
