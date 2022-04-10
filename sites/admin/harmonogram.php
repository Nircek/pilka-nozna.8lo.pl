<?php
register_style("admin_harmonogram");
is_logged();

function page_init()
{
    $sezon = obecny_sezon();
    $sezon_final = "${sezon}_final";
    $sezon_terminarz = "${sezon}_terminarz";
    return array(
        'sezon' => $sezon,
        'harmonogram' => PDOS::Instance()->query(
            "SELECT *,
                IFNULL(`1_wynik`, '') AS w1,
                IFNULL(`2_wynik`, '') AS w2,
                CASE WHEN termin IS NULL OR YEAR(termin) = 0 THEN 'nie ustalono' ELSE termin END AS data
            FROM `$sezon_terminarz` ORDER BY grupa, termin DESC"
        )->fetchAll(PDO::FETCH_ASSOC),
        'finalowe' => sprawdzanie_tabela($sezon_final) ? PDOS::Instance()->query(
            "SELECT *,
                SUBSTRING_INDEX(SUBSTRING_INDEX('FINAŁ,PÓŁFINAŁ,3 MIEJSCE',',', poziom),',',-1) AS tytul,
                CASE WHEN YEAR(termin) = 0 THEN '' ELSE termin END AS data,
                IFNULL(wynik_1, '') AS w1, IFNULL(wynik_2, '') AS w2,
                IFNULL(druzyna_1, '???') AS d1, IFNULL(druzyna_2, '???') AS d2
            FROM `$sezon_final` ORDER BY id DESC"
        )->fetchAll(PDO::FETCH_ASSOC) : NULL,
    );
}
function page_render($obj)
{
?>
    <div id="content">
        <h1> WPISYWANIE HARMONOGRAMU </h1>
        <form method='post' action='<?= PREFIX ?>/skrypty/harmonogram'>
            <?php if (!is_null($obj['finalowe'])) : ?>
                <h2> FAZA FINAŁOWA </h2>
                <?php foreach ($obj['finalowe'] as $final) : ?>
                    <input class='termin' type='date' name='f<?= $final['id'] ?>' value='<?= $final['data'] ?>'> <?= $final['druzyna_1'] ?> vs <?= $final['druzyna_2'] ?> (<?= $final['tytul'] ?>) <br />
                <?php endforeach; ?>
            <?php endif; ?>
            <h2> FAZA GRUPOWA </h2>
            <?php foreach ($obj['harmonogram'] as $grupa) : ?>
                <input class='termin' type='date' name='<?= $grupa['id'] ?>' value='<?= $grupa['data'] ?>'> <?= $grupa['1_text'] ?> vs <?= $grupa['2_text'] ?><br />
            <?php endforeach; ?>
            <input type='hidden' value='<?= $obj['sezon'] ?>' name='sezon'>
            <input type='submit' value='AKTUALIZUJ!'>
        </form>
    </div>
<?php }
