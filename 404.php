<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pl-PL">
<head>
    <?php include('./szablon/meta.php'); ?>
    <title> PIK Piłka Nożna </title>
    <!------------------ STYLE CSS DOTYCZĄCE TYLKO TEJ PODSTRONY STRONY ------------------>
    <style>
    #content {
        padding: 0px;
    }
    </style>
</head>
<body>
    <div id="container">
        <?php include('./szablon/menu.php'); ?>
        <div id="content-border">
            <div id="content">
                <img src="img/error-404.jpg" width='1000' style='margin-left: -20px;'>
            </div>
        </div>
        <?php include('./szablon/footer.php'); ?>
    </div>
</body>
</html>
