<?php

function get_page_title($file, $add = NULL)
{
    global $PRETTY_PAGE_TITLES;
    $pretty_title = null;
    $file = relative_path($file, ROOT_PATH . "/sites/");
    $pretty_title = in_array($file, $PRETTY_PAGE_TITLES) ? $PRETTY_PAGE_TITLES[$file] : null;
    $title = "";
    $sep1 = TITLE_LIGHT_SEPARATOR;
    $sep2 = TITLE_HEAVY_SEPARATOR;
    if ($add and $pretty_title) $title = "$add $sep1 $pretty_title";
    else $title = $add ? $add : $pretty_title;
    if ($title) $title .= " $sep2 ";
    $title .= GLOBAL_TITLE;
    return $title;
}

function generate_header($css, $title = NULL)
{
    global $controller;
    $css = arrayify($css);
?>
    <!DOCTYPE html>
    <html lang="pl-PL">

    <head>
        <?php include(ROOT_PATH . '/template/meta.php'); ?>
        <title> <?= get_page_title($controller, $title) ?> </title>
        <!------------------ STYLE CSS DOTYCZĄCE TYLKO TEJ PODSTRONY STRONY ------------------>
        <?php foreach ($css as $style) : ?>
            <link rel="stylesheet" type="text/css" href="<?= PREFIX ?>/css/<?= $style ?>.css">
        <?php endforeach; ?>
    </head>

    <body>

        <div id="container">
            <?php include(ROOT_PATH . '/template/menu.php'); ?>
            <div id="content-border">
            <?php }
        function generate_footer()
        {
            ?>
            </div>
            <?php include(ROOT_PATH . '/template/footer.php'); ?>
        </div>
    </body>

    </html>
<?php
        }
