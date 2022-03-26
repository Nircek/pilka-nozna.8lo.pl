<?php
session_start();
include('./funkcje/funkcje_admin.php');
is_logged();
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
        .termin {
            margin-top: -10px;
            border-width: 1.5px;
            font-size: 18px;
        }

    </style>
</head>

<body>
    <div id="container">
        <?php include('./szablon/menu.php'); ?>
        <div id="content-border">
            <div id="content">
                <h1> WPISYWANIE HARMONOGRAMU </h1>
                <?php
                if (isset($_SESSION['e_harmonogram_baza'])) {
                    echo '<div id="error">' . $_SESSION['e_harmonogram_baza'] . '</div><br/>';
                    unset($_SESSION['e_harmonogram_baza']);
                }

                include('./skrypty/db-connect.php');
                include('./funkcje/funkcje.php');

                $sezon = obecny_sezon($pdo);
                $sezon_terminarz = $sezon . "_terminarz";
                $sezon_final = $sezon . "_final";

                // =================== FAZA FINAŁOWA ===================
                if (sprawdzanie_tabela($pdo, $sezon_final) == true) {
                    echo "<h2> FAZA FINAŁOWA </h2>";

                    try {
                        $sql = "SELECT * FROM $sezon_final ORDER BY id DESC";
                        $result = $pdo->query($sql);
                    } catch (PDOException $e) {
                        $_SESSION['e_harmonogram_baza'] = "Błąd bazy danych: $e";
                        header('Location: admin.php');
                        exit();
                    }
                    while ($row = $result->fetch()) {
                        $runda_finalowa[] = array('id' => $row['id'],
                                                'druzyna_1' => $row['druzyna_1'],
                                                'druzyna_2' => $row['druzyna_2'],
                                                'termin' => $row['termin'],
                                                'etap' => $row['poziom']);
                    }

                    // =================== FORMULARZ ===================

                    echo "<form method='post' action='skrypty/terminarz.php'>";
                    $id_final = 4;
                    foreach ($runda_finalowa as $final) {
                        if ($final['etap'] == 1) {
                            $final['etap'] = "FINAŁ";
                        } elseif ($final['etap'] == 2) {
                            $final['etap'] = "PÓŁFINAŁ";
                        } elseif ($final['etap'] == 3) {
                            $final['etap'] = "3 MIEJSCE";
                        }

                        echo "<input class='termin' type='date' name='f_$id_final' value='" . $final['termin'] . "'>  " . $final['druzyna_1'] . " vs " . $final['druzyna_2'] . "  (" . $final['etap'] . ") <br/>";
                        $id_final--;
                    }
                    echo "<input type='hidden' value='$id_final' name='final_ilosc'>";
                    echo "<input type='hidden' value='$sezon' name='sezon'>";
                    echo "<input type='submit' value='AKTUALIZUJ!'>";
                    echo "</form>";
                }

                // =================== FAZA GRUPOWA ===================
                echo "<h2> FAZA GRUPOWA </h2>";

                try {
                    $sql = "SELECT * FROM $sezon_terminarz ORDER BY id ASC";
                    $result = $pdo->query($sql);
                } catch (PDOException $e) {
                    $_SESSION['e_harmonogram_baza'] = "Błąd bazy danych: $e";
                    header('Location: admin.php');
                    exit();
                }
                while ($row = $result->fetch()) {
                    $grupa[] = array('id' => $row['id'],
                                    'druzyna_1' => $row['1_text'],
                                    'druzyna_2' => $row['2_text'],
                                    'termin' => $row['termin']);
                }

                // =================== FORMULARZ ===================
                echo "<form method='post' action='skrypty/terminarz.php'>";
                $id_grupa = 1;
                foreach ($grupa as $grupa) {
                    echo "<input class='termin' type='date' name='$id_grupa' value='" . $grupa['termin'] . "'>  " . $grupa['druzyna_1'] . " vs " . $grupa['druzyna_2'] . "<br/>";
                    if ($id_grupa % 2 == 0) {
                    }
                    $id_grupa++;
                }
                echo "<input type='hidden' value='$id_grupa' name='grupa_ilosc'>";
                echo "<input type='hidden' value='$sezon' name='sezon'>";
                echo "<input type='submit' value='AKTUALIZUJ!'>";
                echo "</form>";
                ?>
            </div>
            <?php include('./szablon/footer.php'); ?>
        </div>
    </div>
</body>

</html>
