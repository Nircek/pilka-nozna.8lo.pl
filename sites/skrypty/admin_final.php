<?php
is_logged();

$sezon = obecny_sezon();

$grouping_stmt = PDOS::Instance()->prepare( // get_season_details(season)
    "SELECT `description`, `grouping_type` FROM `ng_season` WHERE `season_id` = ?;"
);
$grouping_stmt->execute([$sezon]);
$grouping = $grouping_stmt->fetch(PDO::FETCH_ASSOC)['grouping_type'];
if($grouping !== "two_groups") {
    header("Location: " . PANEL_URL);
    report_error("Tylko grupowanie `two_groups` pozwala na utworzenie rundy finałowej.", NULL);
    exit();
}

PDOS::Instance()->prepare( // delete_finals(season)
    "DELETE FROM `ng_game` WHERE `season_id` = ? AND `type` NOT IN ('first', 'second');"
)->execute([$sezon]);

$tabela_stmt = PDOS::Instance()->prepare( // get_group_table(season, all?, group)
    "SELECT
        tt.team AS `id`, T.`name` AS team, 3*win+tie AS points, win, tie, los,
        our AS gain, their AS lost, CONCAT(IF((our - their)>0,'+',''),our - their) AS delta
    FROM (
        SELECT `season_id`, us AS team,
            SUM(IF(our>their, 1, 0)) AS win,
            SUM(IF(our=their, 1, 0)) AS tie,
            SUM(IF(our<their, 1, 0)) AS los,
            IFNULL(SUM(our), 0) AS our, IFNULL(SUM(their),0) AS their
        FROM (
            SELECT `season_id`, `type`, `A_team_id` AS us, `A_score` AS our, `B_score` AS their FROM `ng_game`
            UNION ALL
            SELECT `season_id`, `type`, `B_team_id` AS us, `B_score` AS our, `A_score` AS their FROM `ng_game`
        ) t
        WHERE `season_id` = ? AND `type` IN ('first', 'second') AND (? XOR `type` = ?) GROUP BY `us`
    ) tt
    LEFT JOIN `ng_team` T ON T.`season_id` = tt.`season_id` AND T.`team_id` = tt.team
    ORDER BY points DESC, delta DESC, team ASC;"
);

$tabela_stmt->execute([$sezon, false, 'first']);
$grupa_1 = $tabela_stmt->fetchAll(PDO::FETCH_COLUMN);
$tabela_stmt->execute([$sezon, false, 'second']);
$grupa_2 = $tabela_stmt->fetchAll(PDO::FETCH_COLUMN);

array_splice($grupa_1, 2);
array_splice($grupa_2, 2);


if (count($grupa_1) + count($grupa_2) < 4) {
    header('Location: ' . PANEL_URL);
    report_error("Za mało drużyn by stworzyć rundę finałową", NULL);
    exit();
}

$insert_stmt = PDOS::Instance()->prepare(// add_finals(season, f1, s1, f2, s2)
    "INSERT INTO `ng_game` (`game_id`, `season_id`, `type`, `A_team_id`, `B_team_id`)
        SELECT maxi+i, id, `type`, A, B FROM
            (SELECT max(`game_id`) AS maxi, id FROM `ng_game` g, (SELECT ? AS id) i WHERE `season_id` = id) m,
            (
                SELECT 1 AS i, 'half1' AS `type`, ? AS A, ? AS B UNION
                SELECT 2 AS i, 'half2' AS `type`, ? AS A, ? AS B UNION
                SELECT 3 AS i, 'third' AS `type`, NULL AS A, NULL AS B UNION
                SELECT 4 AS i, 'final' AS `type`, NULL AS A, NULL AS B
            ) t;"
);

$insert_stmt->execute([$sezon, $grupa_1[0], $grupa_2[1], $grupa_2[0], $grupa_1[1]]);

header('Location: ' . PANEL_URL);
exit();
