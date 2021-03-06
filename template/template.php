<!DOCTYPE html>
<html lang="pl-PL">

<head>
    <?php include(ROOT_PATH . '/template/meta.php'); ?>
    <title> <?= get_page_title() ?> </title>
    <!------------------ STYLE CSS DOTYCZĄCE TYLKO TEJ PODSTRONY STRONY ------------------>
    <?php if (!empty($REGISTERED_STYLES)) foreach ($REGISTERED_STYLES as $style) : ?>
        <link rel="stylesheet" type="text/css" href="<?= PREFIX ?>/css/<?= $style ?>.css">
    <?php endforeach; ?>
</head>

<body>

    <div id="container">
        <?php include(ROOT_PATH . '/template/menu.php'); ?>
        <?php page_render($obj); ?>
        <?php include(ROOT_PATH . '/template/footer.php'); ?>
    </div>
</body>

</html>
