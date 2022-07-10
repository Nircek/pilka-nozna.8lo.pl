<?php
register_style("admin_wyniki");
is_logged();

function page_init()
{
    $sezon = obecny_sezon();
    $grupowe = array();
    $grupowe[] = PDOS::Instance()->cmd(
        "get_games(season, finals?, group)",
        [$sezon, false, 'first']
    )->fetchAll(PDO::FETCH_ASSOC);
    $grupowe[] = PDOS::Instance()->cmd(
        "get_games(season, finals?, group)",
        [$sezon, false, 'second']
    )->fetchAll(PDO::FETCH_ASSOC);
    $finalowe = PDOS::Instance()->cmd(
        "get_games(season, finals?, group)",
        [$sezon, true, '']
    )->fetchAll(PDO::FETCH_ASSOC);
    return array(
        'sezon' => $sezon,
        'grupowe' => $grupowe,
        'finalowe' => count($finalowe) > 0 ? $finalowe : NULL,
    );
}

function page_render($obj)
{ ?>

    <div id="content">
        <h1> WPISZ WYNIKI (jesli się nie odbył to zostaw puste) </h1>

        <form method='post' action='<?= PREFIX ?>/skrypty/wyniki'>
            <?php if (!is_null($obj['finalowe'])) : ?>
                <?php foreach ($obj['finalowe'] as $mecz) : ?>
                    <?= $mecz['title'] ?> | <?= coalesce($mecz['date'], 'nie ustalono') ?> <br /><?= coalesce($mecz['A_team'], '???') ?>
                    <input class='wynik' type='number' value='<?= $mecz['A_score'] ?>' name='<?= $mecz['game_id'] ?>_1'> :
                    <input class='wynik' type='number' value='<?= $mecz['B_score'] ?>' name='<?= $mecz['game_id'] ?>_2'>
                    <?= coalesce($mecz['B_team'], '???') ?>
                    <br />
                <?php endforeach; ?>
            <?php endif; ?>
            <?php for ($grupa = 0; $grupa <= 1; ++$grupa) : ?>
                <div id='grupy'>
                    <?php foreach ($obj['grupowe'][$grupa] as $mecz) : ?>
                        <?= coalesce($mecz['date'], 'nie ustalono') ?><br />
                        <?= coalesce($mecz['A_team'], '???') ?>
                        <input class='wynik' type='number' name='<?= $mecz['game_id'] ?>_1' value='<?= $mecz['A_score'] ?>'> :
                        <input class='wynik' type='number' name='<?= $mecz['game_id'] ?>_2' value='<?= $mecz['B_score'] ?>'>
                        <?= coalesce($mecz['B_team'], '???') ?>
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
