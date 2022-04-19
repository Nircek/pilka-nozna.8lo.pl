<?php
register_style("admin_harmonogram");
is_logged();

function page_init()
{
    $sezon = obecny_sezon();
    $harmonogram_stmt = PDOS::Instance()->prepare( // schedule(season, finals?, group)
        "SELECT
            g.`game_id`, a.`name` AS `A_team`, b.`name` AS `B_team`,
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
        'finalowe' => count($finalowe) > 0 ? $finalowe : NULL
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
                    <input class='termin' type='date' name='f<?= $mecz['game_id'] ?>' value='<?= is_null($mecz['date']) ? '' : $mecz['date'] /* TODO: null coalescing operator */ ?>'> <?= $mecz['A_team'] ?> vs <?= $mecz['B_team'] ?> (<?= $mecz['title'] ?>) <br />
                <?php endforeach; ?>
            <?php endif; ?>
            <h2> FAZA GRUPOWA </h2>
            <?php for ($grupa = 0; $grupa <= 1; ++$grupa) : ?>
                <div id='grupy'>
                    <?php foreach ($obj['grupowe'][$grupa] as $mecz) : ?>
                        <input class='termin' type='date' name='<?= $mecz['game_id'] ?>' value='<?= is_null($mecz['date']) ? '' : $mecz['date'] /* TODO: null coalescing operator */ ?>'> <?= $mecz['A_team'] ?> vs <?= $mecz['B_team'] ?><br />
                    <?php endforeach; ?>
                </div>
            <?php endfor; ?>
            <input type='hidden' value='<?= $obj['sezon'] ?>' name='sezon'>
            <input type='submit' value='AKTUALIZUJ!'>
        </form>
    </div>
<?php }
