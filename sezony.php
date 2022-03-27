<?php
session_start();
include("./skrypty/db-connect.php");
include("./funkcje/funkcje.php");
if (isset($_GET['s'])) {
    $sezon_pelny = $_GET['s'];
    // Sezon zapisywany w bazie to tylko jego liczba początkowa
    // 2016/2017 to tylko 2016, dlatego $_GET musi rozdzielić te dwie liczby
    $sezon = explode("/", $sezon_pelny)[0];
    $sezon_tabela = "${sezon}_tabela";
    $sezon_terminarz = "${sezon}_terminarz";
    if (!sprawdzanie_tabela($pdo, $sezon_tabela) || !sprawdzanie_tabela($pdo, $sezon_terminarz)) {
        header('Location: sezony.php');
        echo "Podany sezon nie istnieje...";
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="pl-PL">

<head>

    <?php include('./szablon/meta.php'); ?>

    <!------------------ STYLE CSS DOTYCZĄCE TYLKO TEJ PODSTRONY STRONY ------------------>
    <link rel="stylesheet" type="text/css" href="style/sezony.css">
</head>

<body>
    <div id="container">
        <?php include('./szablon/menu.php'); ?>
        <div id="content-border">
            <?php
            // =================== MENU SEZONÓW ===================
            if (!isset($_GET['s'])) :
            ?>
                <div id='content' style='background-color: rgba(20, 21, 24, 0.9); width: 960px; padding: 20px;'>
                    <h1> WYBIERZ SEZON </h1>
                    <?php
                    try {
                        $sql = "SELECT sezon FROM sezony ORDER BY id DESC";
                        $result = $pdo->query($sql);
                    } catch (PDOException $e) {
                        echo "<div id='error'> $e </div>";
                    }
                    $sezon = array();
                    while ($row = $result->fetch()) {
                        $sezon[] = array('sezon' => $row['sezon']);
                    }

                    foreach ($sezon as $sezon) :
                    ?>
                        <div class='sezon'>
                            <a href='?s=<?= $sezon['sezon'] ?>/<?= $sezon['sezon'] + 1 ?>'> .
                                <?= $sezon['sezon'] ?>/<?= $sezon['sezon'] + 1 ?>
                            </a>
                        </div>
                    <?php endforeach; ?>

                    <div class='sezon'>
                        <a href='http://www.pilka-nozna.8lo.pl/archiwum/' target='_blank'>
                            ARCHIWUM
                        </a>
                    </div>
                    <div style='clear: both;'></div>
                </div>
            <?php else : ?>
                <div id="content">
                    <div id="head">
                        <div id="powrot">
                            <a href="sezony.php"> &#8592 POWRÓT </a>
                        </div>

                        <span>
                            <h1> SEZON <?= $sezon_pelny ?> </h1>
                        </span>
                    </div>
                    <div id="runda-finalowa">
                        <?php
                        $sezon_final = "${sezon}_final";
                        if (sprawdzanie_tabela($pdo, $sezon_final)) {
                            $check_final = true;
                            try {
                                $sql = "SELECT * FROM $sezon_final ORDER BY id DESC";
                                $result = $pdo->query($sql);
                            } catch (PDOException $e) {
                                echo "<div id='error'> $e </div>";
                            }

                            while ($row = $result->fetch()) {
                                $runda_finalowa[] = array(
                                    'd1' => $row['druzyna_1'],
                                    'd2' => $row['druzyna_2'],
                                    'wynik_1' => $row['wynik_1'],
                                    'wynik_2' => $row['wynik_2'],
                                    'termin' => $row['termin'],
                                    'etap' => $row['poziom']
                                );
                            }

                            $i = 0;
                            foreach ($runda_finalowa as $runda_finalowa) {
                                $final[$i][0] = $runda_finalowa['d1'];
                                $final[$i][1] = $runda_finalowa['d2'];
                                $final[$i][2] = $runda_finalowa['wynik_1'];
                                $final[$i][3] = $runda_finalowa['wynik_2'];
                                $final[$i][4] = $runda_finalowa['termin'];
                                $final[$i][5] = $runda_finalowa['etap'];

                                // Konfigurowanie wyjątkowych sytuacji np puste stringi itp...
                                if ($final[$i][5] == 3) {
                                    $final[$i][5] = "3 MIEJSCE";
                                } elseif ($final[$i][5] == 2) {
                                    $final[$i][5] = "PÓŁFINAŁ";
                                } elseif ($final[$i][5] == 1) {
                                    $final[$i][5] = "FINAŁ";
                                }

                                if (empty($final[$i][0])) {
                                    $final[$i][0] = "?";
                                }
                                if (empty($final[$i][1])) {
                                    $final[$i][1] = "?";
                                }

                                if ($final[$i][4] == "0000-00-00") {
                                    $final[$i][4] = "nie ustalono";
                                }

                                if ($final[$i][5] == "FINAŁ" or $final[$i][5] == "3 MIEJSCE") :
                        ?>
                                    <h2> <?= $final[$i][5] ?> </h2>
                                    <table id='tabela' cellspacing='0'>
                                        <tr>
                                            <th colspan='3'> <?= $final[$i][4] ?> </th>
                                        </tr>
                                        <td style='width: 33%;'> <?= $final[$i][0] ?> </td>
                                        <td style='width: 33%;'> <?= $final[$i][2] ?> : <?= $final[$i][3] ?> </td>
                                        <td style='width: 33%;'> <?= $final[$i][1] ?> </td>
                                        <tr>
                                    </table>
                        <?php
                                endif;
                                $i++;
                            }
                        }
                        ?>
                    </div>

                    <!------------------ GRUPA PIERWSZA ------------------>
                    <div id="grupa-pierwsza">
                        <?php
                        if (isset($check_final)) :
                        ?>
                            <h2> <?= $final[3][5] ?> </h2>
                            <table id='tabela' cellspacing='0'>
                                <tr>
                                    <th colspan='3'> <?= $final[3][4] ?> </th>
                                </tr>
                                <td style='width: 33%;'> <?= $final[3][0] ?> </td>
                                <td style='width: 33%;'> <?= $final[3][2] ?> : <?= $final[3][3] ?> </td>
                                <td style='width: 33%;'> <?= $final[3][1] ?> </td>
                                <tr>
                            </table>
                        <?php endif; ?>
                        <h2> GRUPA PIERWSZA </h2>
                        <!------------------ TEBELA ------------------>
                        <h3> TABELA </h3>
                        <?php show_tabela(1, $sezon_tabela, 1); ?>
                        <!------------------ HARMONOGRAM ------------------>
                        <h3> HARMONOGRAM </h3>
                        <?php harmonogram_grupy($sezon_terminarz, 1); ?>
                    </div>

                    <!------------------ GRUPA DRUGA ------------------>
                    <div id="grupa-druga">
                        <?php if (isset($check_final)) : ?>
                            <h2> <?= $final[2][5] ?> </h2>
                            <table id='tabela' cellspacing='0'>
                                <tr>
                                    <th colspan='3'> <?= $final[2][4] ?> </th>
                                </tr>
                                <td style='width: 33%;'> <?= $final[2][0] ?> </td>
                                <td style='width: 33%;'> <?= $final[2][2] ?> : <?= $final[2][3] ?> </td>
                                <td style='width: 33%;'> <?= $final[2][1] ?> </td>
                                <tr>
                            </table>
                        <?php endif; ?>
                        <h2> GRUPA DRUGA </h2>
                        <!------------------ TEBELA ------------------>
                        <h3> TABELA </h3>
                        <?php show_tabela(1, $sezon_tabela, 2); ?>

                        <!------------------ HARMONOGRAM ------------------>
                        <h3> HARMONOGRAM </h3>
                        <?php harmonogram_grupy($sezon_terminarz, 2); ?>
                    </div>
                </div>
            <?php endif; ?>
            <?php include('./szablon/footer.php'); ?>
        </div>
</body>

</html>
