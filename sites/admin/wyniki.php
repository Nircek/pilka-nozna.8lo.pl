<?php
register_style("admin_wyniki");
is_logged();

function page_init()
{
    $sezon = obecny_sezon();
    $sezon_final = "${sezon}_final";
    $sezon_terminarz = "${sezon}_terminarz";
    $harmonogram = array();
    $harmonogram_stmt = PDOS::Instance()->prepare(
        "SELECT *,
            IFNULL(`1_wynik`, '') AS w1,
            IFNULL(`2_wynik`, '') AS w2,
            CASE WHEN termin IS NULL OR YEAR(termin) = 0 THEN 'nie ustalono' ELSE termin END AS data
        FROM `$sezon_terminarz` WHERE grupa = ? ORDER BY termin DESC"
    );
    for ($i = 1; $i <= 2; ++$i) {
        $harmonogram_stmt->execute([$i]);
        $harmonogram[] = $harmonogram_stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    return array(
        'sezon' => $sezon,
        'harmonogram' => $harmonogram,
        'finalowe' => sprawdzanie_tabela($sezon_final) ? PDOS::Instance()->query(
            "SELECT *,
                SUBSTRING_INDEX(SUBSTRING_INDEX('FINAŁ,PÓŁFINAŁ,3 MIEJSCE',',', poziom),',',-1) AS tytul,
                CASE WHEN YEAR(termin) = 0 THEN 'nie ustalono' ELSE termin END AS data,
                IFNULL(wynik_1, '') AS w1, IFNULL(wynik_2, '') AS w2,
                IFNULL(druzyna_1, '???') AS d1, IFNULL(druzyna_2, '???') AS d2
            FROM `$sezon_final` ORDER BY id DESC"
        )->fetchAll(PDO::FETCH_ASSOC) : NULL,
    );
}

function page_render($obj)
{ ?>

    <div id="content">
        <h1> WPISZ WYNIKI (jesli się nie odbył to zostaw puste) </h1>

        <form method='post' action='<?= PREFIX ?>/skrypty/wyniki'>
            <?php if (!is_null($obj['finalowe'])) : ?>
                <?php foreach ($obj['finalowe'] as $mecz) : ?>
                    <?= $mecz['tytul'] ?> | <?= $mecz['data'] ?> <br /><?= $mecz['d1'] ?>
                    <input class='wynik' type='number' value='<?= $mecz['w1'] ?>' name='f<?= $mecz['id'] ?>_1'> :
                    <input class='wynik' type='number' value='<?= $mecz['w2'] ?>' name='f<?= $mecz['id'] ?>_2'>
                    <?= $mecz['d2'] ?>
                    <br />
                <?php endforeach; ?>
            <?php endif; ?>
            <?php for ($grupa = 0; $grupa <= 1; ++$grupa) : ?>
                <div id='grupy'>
                    <?php foreach ($obj['harmonogram'][$grupa] as $mecz) : ?>
                        <?= $mecz['data'] ?><br />
                        <?= $mecz['1_text'] ?>
                        <input class='wynik' type='number' name='<?= $mecz['id'] ?>_1' value='<?= $mecz['w1'] ?>'> :
                        <input class='wynik' type='number' name='<?= $mecz['id'] ?>_2' value='<?= $mecz['w2'] ?>'>
                        <?= $mecz['2_text'] ?>
                        <br />
                    <?php endforeach; ?>
                </div>
            <?php endfor; ?>
            <div style="clear: both;"></div>
            <input type='hidden' value='<?= $obj['sezon'] ?>' name='sezon'>
            <input type='submit' value='AKTUALIZUJ!'>
        </form>
    </div>
    <div style='clear: both;'></div>

<?php }
