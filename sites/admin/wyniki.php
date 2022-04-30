<?php
register_style("admin_wyniki");
is_logged();

function page_init()
{
    $sezon = obecny_sezon();
    $grupowe = array();
    $harmonogram_stmt = PDOS::Instance()->prepare( // schedule(season, finals?, group)
        "SELECT
            g.`game_id`, a.`name` AS `A_team`, b.`name` AS `B_team`,
            g.`A_score`, g.`B_score`,
            CASE WHEN g.`date` IS NULL OR YEAR(g.`date`) = 0 THEN NULL ELSE g.`date` END AS `date`, g.`type`,
            SUBSTRING_INDEX(SUBSTRING_INDEX('PÓŁFINAŁ,FINAŁ,3 MIEJSCE', ',', FIND_IN_SET(g.`type`, 'final,third')+1), ',', -1) AS `title`
        FROM `ng_game` g
            LEFT JOIN `ng_team` a ON g.`season_id` = a.`season_id` AND g.`A_team_id` = a.`team_id`
            LEFT JOIN `ng_team` b ON g.`season_id` = b.`season_id` AND g.`B_team_id` = b.`team_id`
        WHERE g.`season_id` = ? AND ((? AND g.`type` NOT IN ('first', 'second')) OR g.`type` = ?) ORDER BY g.`type`, `date`, `game_id`;"
    );
    $harmonogram_stmt->execute([$sezon, false, 'first']);
    $grupowe[] = $harmonogram_stmt->fetchAll(PDO::FETCH_ASSOC);
    $harmonogram_stmt->execute([$sezon, false, 'second']);
    $grupowe[] = $harmonogram_stmt->fetchAll(PDO::FETCH_ASSOC);
    $harmonogram_stmt->execute([$sezon, true, '']);
    $finalowe = $harmonogram_stmt->fetchAll(PDO::FETCH_ASSOC);
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
