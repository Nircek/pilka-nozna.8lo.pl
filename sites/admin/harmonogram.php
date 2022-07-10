<?php
register_style("admin_harmonogram");
is_logged();

function page_init()
{
    $sezon = obecny_sezon();
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
        'finalowe' => count($finalowe) > 0 ? $finalowe : null
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
                <?php foreach ($obj['finalowe'] as $mecz) : ?>
                    <input class='termin' type='date' name='<?= $mecz['game_id'] ?>' value='<?= coalesce($mecz['date'], '') ?>'> <?= coalesce($mecz['A_team'], '???') ?> vs <?= coalesce($mecz['B_team'], '???') ?> (<?= $mecz['title'] ?>) <br />
                <?php endforeach; ?>
            <?php endif; ?>
            <h2> FAZA GRUPOWA </h2>
            <?php for ($grupa = 0; $grupa <= 1; ++$grupa) : ?>
                <div id='grupy'>
                    <?php foreach ($obj['grupowe'][$grupa] as $mecz) : ?>
                        <input class='termin' type='date' name='<?= $mecz['game_id'] ?>' value='<?= coalesce($mecz['date'], '') ?>'> <?= coalesce($mecz['A_team'], '???') ?> vs <?= coalesce($mecz['B_team'], '???') ?><br />
                    <?php endforeach; ?>
                </div>
            <?php endfor; ?>
            <input type='hidden' value='<?= $obj['sezon'] ?>' name='sezon'>
            <input type='submit' value='AKTUALIZUJ!'>
        </form>
    </div>
<?php
}
