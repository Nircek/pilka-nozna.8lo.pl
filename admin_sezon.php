<?php
session_start();
include('./funkcje/funkcje_admin.php');
is_logged();

// Check if step exists
if (!isset($_SESSION['krok'])) {
    $_SESSION['krok'] = 1;
    $krok = $_SESSION['krok'];
} elseif ($_SESSION['krok'] == 1 or $_SESSION['krok'] == 2 or $_SESSION['krok'] == 3) {
    $krok = $_SESSION['krok'];
} else {
    header('Location: admin.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="pl-PL">
<head>

    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="style/szablon.css">
    <link rel="stylesheet" type="text/css" href="fontello/css/peak.css">
    <link href="https://fonts.googleapis.com/css?family=Monda:400,700&amp;subset=latin-ext" rel="stylesheet">
    <link rel="icon" type="image/png" href="img/logo.png">
    <meta name="robots" content="noindex" />

    <title> PIK Piłka Nożna </title>
    <!------------------ STYLE CSS DOTYCZĄCE TYLKO TEJ PODSTRONY STRONY ------------------>
    <link rel="stylesheet" type="text/css" href="style/admin.css">
    <style>
    #grupa-pierwsza,
    #grupa-druga {
        width: 50%;
        float: left;
        padding-bottom: 20000px;
        margin-bottom: -20000px;
    }

    table {
        margin: auto;
        border: solid 2px rgba(0, 0, 0, 0.9);
    }

    td,
    th {
        border: solid 2px rgba(0, 0, 0, 0.9);
        margin-bottom: 0;
        font-size: 15px;
        padding: 0 5px;
    }

    th {
        padding: 5px 0;
    }

    #termin {
        margin-bottom: 0px;
    }

    #akceptuje-sezon {
        margin-top: 30px;
        padding: 10px 30px;
    }
    </style>
</head>
<body>

    <div id="container">
        <?php include('./szablon/menu.php'); ?>

        <div id="content-border">
            <div id="content">
                <h1> TWORZENIE SEZONU - KROK <?php echo $krok; ?> </h1>

                <div id="panel">
                    <?php
                    // Loading same script as step of creating next season
                    include('./skrypty/sezon_krok' . $krok . '.php');
                    ?>
                </div>
            </div>
        </div>

        <?php include('./szablon/footer.php'); ?>

    </div>
</body>
</html>
