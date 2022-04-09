<?php
include(ROOT_PATH . "/funkcje/funkcje_admin.php");
is_logged();
?>
<?php generate_header("admin,admin_harmonogram"); ?>

<div id="content">
    <h1> WPISYWANIE HARMONOGRAMU </h1>
    <?php
    include(ROOT_PATH . "/funkcje/funkcje.php");

    $sezon = obecny_sezon();
    $sezon_terminarz = "${sezon}_terminarz";
    $sezon_final = "${sezon}_final";

    // =================== FAZA FINAŁOWA ===================
    if (sprawdzanie_tabela($sezon_final) == true) {
        echo "<h2> FAZA FINAŁOWA </h2>";

        try {
            $sql = "SELECT * FROM $sezon_final ORDER BY id DESC";
            $result = PDOS::Instance()->query($sql);
        } catch (PDOException $e) {
            reportError("db", $e->getMessage());
        }
        while ($row = $result->fetch()) {
            $runda_finalowa[] = array(
                'id' => $row['id'],
                'druzyna_1' => $row['druzyna_1'],
                'druzyna_2' => $row['druzyna_2'],
                'termin' => $row['termin'],
                'etap' => $row['poziom']
            );
        }

        // =================== FORMULARZ ===================
    ?>
        <form method='post' action='<?= PREFIX ?>/skrypty/terminarz'>
            <?php
            $id_final = 4;
            foreach ($runda_finalowa as $final) {
                if ($final['etap'] == 1) {
                    $final['etap'] = "FINAŁ";
                } elseif ($final['etap'] == 2) {
                    $final['etap'] = "PÓŁFINAŁ";
                } elseif ($final['etap'] == 3) {
                    $final['etap'] = "3 MIEJSCE";
                }
            ?>
                <input class='termin' type='date' name='f_<?= $id_final ?>' value='<?= $final['termin'] ?>'> <?= $final['druzyna_1'] ?> vs <?= $final['druzyna_2'] ?> (<?= $final['etap'] ?>) <br />
            <?php
                $id_final--;
            }
            ?>
            <input type='hidden' value='$id_final' name='final_ilosc'>
            <input type='hidden' value='$sezon' name='sezon'>
            <input type='submit' value='AKTUALIZUJ!'>
        </form>
    <?php
    }

    // =================== FAZA GRUPOWA ===================
    echo "<h2> FAZA GRUPOWA </h2>";

    try {
        $sql = "SELECT * FROM $sezon_terminarz ORDER BY id ASC";
        $result = PDOS::Instance()->query($sql);
    } catch (PDOException $e) {
        reportError("db", $e->getMessage());
    }
    while ($row = $result->fetch()) {
        $grupa[] = array(
            'id' => $row['id'],
            'druzyna_1' => $row['1_text'],
            'druzyna_2' => $row['2_text'],
            'termin' => $row['termin']
        );
    }
    ?>
    <form method='post' action='<?= PREFIX ?>/skrypty/terminarz'>
        <?php
        // =================== FORMULARZ ===================
        $id_grupa = 1;
        foreach ($grupa as $grupa) {
        ?>
            <input class='termin' type='date' name='<?= $id_grupa ?>' value='<?= $grupa['termin'] ?>'> <?= $grupa['druzyna_1'] ?> vs <?= $grupa['druzyna_2'] ?><br />
        <?php
            if ($id_grupa % 2 == 0) {
            }
            $id_grupa++;
        }
        ?>
        <input type='hidden' value='$id_grupa' name='grupa_ilosc'>
        <input type='hidden' value='$sezon' name='sezon'>
        <input type='submit' value='AKTUALIZUJ!'>
    </form>
</div>

<?php generate_footer() ?>
