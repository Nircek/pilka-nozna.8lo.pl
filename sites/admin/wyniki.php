<?php
include_once(ROOT_PATH . '/funkcje/funkcje_admin.php');
is_logged();
?>
<?php generate_header("admin,admin_wyniki"); ?>

<div id="content">
    <h1> WPISZ WYNIKI (jesli się nie odbył to zostaw puste) </h1>
    <?php
    include_once(ROOT_PATH . "/funkcje/db-connect.php");
    include_once(ROOT_PATH . "/funkcje/funkcje.php");

    // Errors
    if (isset($_SESSION['e_wyniki_baza'])) {
        echo '<div id="error">' . $_SESSION['e_wyniki_baza'] . '</div><br/>';
        unset($_SESSION['e_wyniki_baza']);
    } elseif (isset($_SESSION['wyniki_sukces'])) {
        echo '<div id="sukces">' . $_SESSION['wyniki_sukces'] . '</div><br/>';
        unset($_SESSION['wyniki_sukces']);
    } elseif (isset($_SESSION['e_wyniki_remis'])) {
        echo '<div id="error">' . $_SESSION['e_wyniki_remis'] . '</div><br/>';
        unset($_SESSION['e_wyniki_remis']);
    }

    $sezon = obecny_sezon($pdo);
    $sezon_terminarz = "${sezon}_terminarz";
    $sezon_final = "${sezon}_final";

    if (sprawdzanie_tabela($pdo, $sezon_final)) {
        try {
            $sql = "SELECT * FROM $sezon_final ORDER BY id DESC";
            $result = $pdo->query($sql);
        } catch (PDOException $e) {
            $_SESSION['e_wyniki_baza'] = "Błąd bazy danych: $e";
            header('Location: admin.php');
            exit();
        }
        while ($row = $result->fetch()) {
            $runda_finalowa[] = array(
                'id' => $row['id'],
                'druzyna_1' => $row['druzyna_1'],
                'druzyna_2' => $row['druzyna_2'],
                'wynik_1' => $row['wynik_1'],
                'wynik_2' => $row['wynik_2'],
                'termin' => $row['termin'],
                'etap' => $row['poziom']
            );
        }

    ?>
        <form method='post' action='<?= PREFIX ?>/skrypty/wyniki'>
            <?php
            $y = 4;
            foreach ($runda_finalowa as $runda_finalowa) {
                $final[$y][0] = $runda_finalowa['id'];
                $final[$y][1] = $runda_finalowa['druzyna_1'];
                $final[$y][2] = $runda_finalowa['druzyna_2'];
                $final[$y][3] = $runda_finalowa['wynik_1'];
                $final[$y][4] = $runda_finalowa['wynik_2'];
                $final[$y][5] = $runda_finalowa['etap'];

                if ($final[$y][5] == 1) {
                    $etap = "FINAŁ";
                } elseif ($final[$y][5] == 2) {
                    $etap = "PÓŁFINAŁ";
                } elseif ($final[$y][5] == 3) {
                    $etap = "3 MIEJSCE";
                }

                if (empty($final[$y][1]) or empty($final[$y][2])) {
                    if (empty($final[$y][1])) {
                        $final[$y][1] = "??? ";
                    }

                    if (empty($final[$y][2])) {
                        $final[$y][2] = " ???";
                    }
            ?>
                    <?= $etap ?> | <?= $final[$y][1] ?> vs <?= $final[$y][2] ?> <br />";
                <?php
                } else {
                ?>
                    <?= $etap ?> | <?= $final[$y][1] ?><input class='wynik' type='number' value='<?= $final[$y][3] ?>' name='f_wynik_1_$y'>
                    <input type='hidden' value='<?= $final[$y][1] ?>' name='f_d1_$y'>:
                    <input class='wynik' type='number' value='<?= $final[$y][4] ?>' name='f_wynik_2_$y'>
                    <input type='hidden' value='<?= $final[$y][2] ?>' name='f_d2_$y'><?= $final[$y][2] ?>
                    <input type='hidden' value='<?= $final[$y][5] ?>' name='f_etap_<?= $y ?>'><br />
            <?php
                }
                $y--;
            }
            ?>
            <input type='hidden' name='final'><!-- Zmienna żeby sprawdzić czy wysłano -->
            <input type='hidden' value='$sezon' name='sezon'>
            <input type='submit' value='AKTUALIZUJ!'>
        </form><br />
    <?php
    }

    // >>>>>>>>>>>>>>>>>>>> FAZA GRUPOWA <<<<<<<<<<<<<<<<<<<<

    // =================== POBIERANIE TERMINARZA ===================
    try {
        $sql = "SELECT * FROM $sezon_terminarz WHERE grupa=1";
        $terminarz_1 = $pdo->query($sql);
        $sql = "SELECT * FROM $sezon_terminarz WHERE grupa=2";
        $terminarz_2 = $pdo->query($sql);
    } catch (PDOException $e) {
        $_SESSION['e_wyniki_baza'] = "Błąd bazy danych: $e";
        header('Location: admin.php');
        exit();
    }

    while ($row = $terminarz_1->fetch()) {
        $termin_1[] = array(
            'id' => $row['id'],
            '1_text' => $row['1_text'],
            '2_text' => $row['2_text'],
            '1_num' => $row['1_num'],
            '2_num' => $row['2_num'],
            '1_wynik' => $row['1_wynik'],
            '2_wynik' => $row['2_wynik'],
            'termin' => $row['termin']
        );
    }

    while ($row = $terminarz_2->fetch()) {
        $termin_2[] = array(
            'id' => $row['id'],
            '1_text' => $row['1_text'],
            '2_text' => $row['2_text'],
            '1_num' => $row['1_num'],
            '2_num' => $row['2_num'],
            '1_wynik' => $row['1_wynik'],
            '2_wynik' => $row['2_wynik'],
            'termin' => $row['termin']
        );
    }
    ?>
    <div id='grupy'>
        <form method='post' action='<?= PREFIX ?>/skrypty/wyniki'>
            <?php

            // =================== WYPISYWANIE INPUTÓW DLA OBU GRUP ===================
            $i = 1;
            foreach ($termin_1 as $termin_1) {
                $termin_1[$i][0] = $termin_1['id'];
                $termin_1[$i][1] = $termin_1['1_text'];
                $termin_1[$i][2] = $termin_1['2_text'];
                $termin_1[$i][3] = $termin_1['1_num'];
                $termin_1[$i][4] = $termin_1['2_num'];
                $termin_1[$i][5] = $termin_1['1_wynik'];
                $termin_1[$i][6] = $termin_1['2_wynik'];
                $termin_1[$i][7] = $termin_1['termin'];

                $d1 = $termin_1[$i][0];
                $d2 = $termin_1[$i][0];
            ?>
                <?= $termin_1[$i][1] ?>
                <input class='wynik' type='number' name='<?= $d1 ?>_1' value='<?= $termin_1[$i][5] ?>'>
                <input type='hidden' name='d1_<?= $i ?>' value='<?= $termin_1[$i][3] ?>'>
                :<input class='wynik' type='number' name='<?= $d2 ?>_2' value='<?= $termin_1[$i][6] ?>'>
                <input type='hidden' name='d2_<?= $i ?>' value='<?= $termin_1[$i][4] ?>'><?= $termin_1[$i][2] ?>
                <br />
            <?php
                $i++;
            }
            ?>
            <input type='hidden' value='$i' name='liczba_1'>
    </div>
    <div id='grupy'>
        <?php
        $i = 1;
        foreach ($termin_2 as $termin_2) {
            $termin_2[$i][0] = $termin_2['id'];
            $termin_2[$i][1] = $termin_2['1_text'];
            $termin_2[$i][2] = $termin_2['2_text'];
            $termin_2[$i][3] = $termin_2['1_num'];
            $termin_2[$i][4] = $termin_2['2_num'];
            $termin_2[$i][5] = $termin_2['1_wynik'];
            $termin_2[$i][6] = $termin_2['2_wynik'];
            $termin_2[$i][7] = $termin_2['termin'];

            $d1 = $termin_2[$i][0];
            $d2 = $termin_2[$i][0];
        ?>
            <?= $termin_2[$i][1] ?><input class='wynik' type='number' name='<?= $d1 ?>_1' value='<?= $termin_2[$i][5] ?>'>
            <input type='hidden' name='d1_<?= $i ?>' value='<?= $termin_2[$i][3] ?>'>
            :<input class='wynik' type='number' name='<?= $d2 ?>_2' value='<?= $termin_2[$i][6] ?>'>
            <input type='hidden' name='d2_<?= $i ?>' value='<?= $termin_2[$i][4] ?>'><?= $termin_2[$i][2] ?>
            <br />
        <?php
            $i++;
        }
        ?>
    </div>";
    <input type='hidden' value='$i' name='liczba_2'>

    <input type='hidden' value='$sezon' name='sezon'>
    <input type='submit' value='AKTUALIZUJ!'>
    </form>
</div>
<div style='clear: both;'></div>

<?php generate_footer(); ?>
