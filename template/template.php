<?php
require_once(__DIR__ . '/../utils.php');

function get_page_title($file, $add = NULL)
{

    $file = relative_path($file);
    global $PRETTY_PAGE_TITLES;
    $title = "";
    $sep1 = TITLE_LIGHT_SEPARATOR;
    $sep2 = TITLE_HEAVY_SEPARATOR;
    $pretty_title = $PRETTY_PAGE_TITLES[$file];
    if ($add and $pretty_title) $title = "$add $sep1 $pretty_title";
    else $title = $add ? $add : $pretty_title;
    if ($title) $title .= " $sep2 ";
    $title .= GLOBAL_TITLE;
    return $title;
}

function generate_header($css, $title = NULL)
{
    $css = arrayify($css);
?>
    <!DOCTYPE html>
    <html lang="pl-PL">

    <head>
        <?php include(ROOT_PATH . '/template/meta.php'); ?>
        <title> <?= get_page_title($_SERVER['SCRIPT_FILENAME'], $title) ?> </title>
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
