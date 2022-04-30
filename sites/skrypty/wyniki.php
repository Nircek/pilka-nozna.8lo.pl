<?php
is_logged();
define("WYNIKI_URL", PREFIX . "/admin/wyniki");
header('Location: ' . WYNIKI_URL);

$sezon = cast_int($_POST['sezon']);
if (is_null($sezon)) {
    report_error("sezon violation", NULL);
    exit();
}

$games_stmt = PDOS::Instance()->prepare( // get_game_types(season)
    "SELECT `game_id`, `type` FROM `ng_game` WHERE `season_id` = ?;"
);
$games_stmt->execute([$sezon]);
$games = $games_stmt->fetchAll(PDO::FETCH_ASSOC);

try {
    PDOS::Instance()->beginTransaction();
    $update_final = false;
    foreach ($games as $game) {
        $w1 = $_POST[$game['game_id'] . '_1'];
        $w2 = $_POST[$game['game_id'] . '_2'];
        $w1 = $w1 === '' ? '' : cast_int($w1);
        $w2 = $w2 === '' ? '' : cast_int($w2);
        if (is_null($w1) or is_null($w2)) continue;
        $half = (substr($game['type'], 0, 4) == 'half');
        if ($half and $w1 !== '' and $w1 === $w2) {
            report_error("W półfinale nie może być remisu!", NULL);
            continue;
        }
        if ($half) $update_final = true;
        if($w1 === '' or $w2 === '') $w1 = $w2 = NULL;
        PDOS::Instance()->prepare( // set_game_score(A, B, season, game_id)
            "UPDATE `ng_game` SET `A_score` = ?, `B_score` = ? WHERE `season_id` = ? AND `game_id` = ?;"
        )->execute([$w1, $w2, $sezon, $game['game_id']]);
    }
    if($update_final) PDOS::Instance()->prepare( // update_final_participants(season)
        "UPDATE `ng_game` u,
        (
            SELECT
                IF(`wins`, 'final', 'third') AS type,
                SUM(CASE WHEN `row` = 1 THEN `team` END) AS A,
                SUM(CASE WHEN `row` = 2 THEN `team` END) AS B
            FROM ( SELECT
                SUBSTR(`type`, 5, 1) AS `row`,
                f AS `wins`,
                IF(A_score - B_score, IF(f XOR A_score < B_score, A_team_id, B_team_id), NULL) AS `team`
            FROM `ng_game`, (SELECT 0 AS f UNION SELECT 1 AS f) f WHERE `season_id` = ? AND SUBSTR(`type`, 1, 4) = 'half' ) t GROUP BY `wins`
        ) x
        SET u.`A_team_id` = x.A, u.`B_team_id` = x.B
        WHERE u.`type` = x.type"
    )->execute([$sezon]);
    PDOS::Instance()->commit();
} catch (Exception $e) {
    PDOS::Instance()->rollback();
    throw $e;
}

exit();
