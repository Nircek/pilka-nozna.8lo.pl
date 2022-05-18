<?php
define("SEZONY_URL", PREFIX . "/sezony");
$sezon = cast_int(HIT_UNPACK());

$name_stmt = PDOS::Instance()->prepare("SELECT `name` FROM `ng_season` WHERE `season_id` = ?"); // get_season_name(season)
$name_stmt->execute([$sezon]);
$name = $name_stmt->fetchAll(PDO::FETCH_COLUMN);
$name = count($name) > 0 ? $name[0] : null;

if (is_null($name)) {
    header("Location: " . SEZONY_URL);
    report_error("Podany sezon nie istnieje...", NULL);
    exit();
}
HIT_PACK($sezon);

register_additional_title("Sezon $name");

function page_init()
{
    $sezon = HIT_UNPACK();
    $name_stmt = PDOS::Instance()->prepare("SELECT `name` FROM `ng_season` WHERE `season_id` = ?;"); // get_season_name(season)
    $name_stmt->execute([$sezon]);
    $name = $name_stmt->fetchAll(PDO::FETCH_COLUMN);
    $name = count($name) > 0 ? $name[0] : null;
    $details_stmt = PDOS::Instance()->prepare( // get_season_details(season)
        "SELECT `description`, `grouping_type` FROM `ng_season` WHERE `season_id` = ?;"
    );
    $details_stmt->execute([$sezon]);
    $details = $details_stmt->fetch(PDO::FETCH_ASSOC);
    if ($details['grouping_type'] != 'no_grouping') {
        $tabele = array();
        $harmonogram = array();
        $tabela_stmt = PDOS::Instance()->prepare( // get_group_table(season, all?, group)
            "SELECT
                T.`name` AS team, 3*win+tie AS points, win, tie, los,
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
            ORDER BY points DESC, delta DESC, team ASC"
        );
        $harmonogram_stmt = PDOS::Instance()->prepare( // get_games(season, finals?, group)
            "SELECT
                g.`game_id`, a.`name` AS `A_team`, b.`name` AS `B_team`,
                g.`A_score`, g.`B_score`,
                CASE WHEN g.`date` IS NULL OR YEAR(g.`date`) = 0 THEN NULL ELSE g.`date` END AS `date`, g.`type`,
                IF(g.`type` IN ('first', 'second'), NULL,
                    SUBSTRING_INDEX(SUBSTRING_INDEX('PÓŁFINAŁ,FINAŁ,3 MIEJSCE', ',', FIND_IN_SET(g.`type`, 'final,third')+1), ',', -1)) AS `title`
            FROM `ng_game` g
                LEFT JOIN `ng_team` a ON g.`season_id` = a.`season_id` AND g.`A_team_id` = a.`team_id`
                LEFT JOIN `ng_team` b ON g.`season_id` = b.`season_id` AND g.`B_team_id` = b.`team_id`
            WHERE g.`season_id` = ? AND ((? AND g.`type` NOT IN ('first', 'second')) OR g.`type` = ?) ORDER BY g.`type`, `date`, `game_id`;"
        );
        for ($i = 1; $i <= 2; ++$i) {
            $tabela_stmt->execute([$sezon, false, $i == 1 ? 'first' : 'second']);
            $tabele[] = $tabela_stmt->fetchAll(PDO::FETCH_ASSOC);
            $harmonogram_stmt->execute([$sezon, false, $i == 1 ? 'first' : 'second']);
            $harmonogram[] = $harmonogram_stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        if ($details['grouping_type'] == "two_rounds") {
            $tabela_stmt->execute([$sezon, true, '']);
            $cala_tabela = $tabela_stmt->fetchAll(PDO::FETCH_ASSOC);
        } else if ($details['grouping_type'] == "two_groups") {
            $harmonogram_stmt->execute([$sezon, true, '']);
            $finalowe = $harmonogram_stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
    return array(
        'sezon' => $sezon,
        'sezon_nazwa' => $name,
        'tabele' => isset($tabele) ? $tabele : NULL,
        'opis' => $details['description'],
        'podzial' => $details['grouping_type'] == "two_rounds" ? array("RUNDA ZASADNICZA", "RUNDA REWANŻOWA") : array("GRUPA PIERWSZA", "GRUPA DRUGA"),
        'cala_tabela' => isset($cala_tabela) ? $cala_tabela : NULL,
        'harmonogram' => isset($harmonogram) ? $harmonogram : NULL,
        'finalowe' => (isset($finalowe) and count($finalowe) > 0) ? $finalowe : NULL,
    );
}

function page_render($obj)
{
?>
    <div id="content">
        <div id="head">
            <div id="powrot">
                <a href="<?= PREFIX ?>/sezony"> &#8592 POWRÓT </a>
            </div>

            <span>
                <h1> SEZON <?= $obj["sezon_nazwa"] ?> </h1>
            </span>
            <?php if (!empty($obj['opis'])) : ?>
                <p class="description"> <?= $obj['opis'] ?> </p>
            <?php endif; ?>
        </div>
        <?php if (!is_null($obj["finalowe"])) : ?>
            <div id="runda-finalowa">
                <?php
                foreach ($obj["finalowe"] as $i => $mecz)
                    if ($i > 1) : // half1, half2, final, third
                ?>
                    <h2> <?= $mecz["title"] ?> </h2>
                    <table class="tabela" cellspacing="0">
                        <tr>
                            <th colspan="3"> <?= coalesce($mecz['date'], 'nie ustalono') ?> </th>
                        </tr>
                        <td style="width: 33%;"> <?= coalesce($mecz["A_team"], '???') ?> </td>
                        <td style="width: 33%;"> <?= coalesce($mecz["A_score"], '-') ?> : <?= coalesce($mecz["B_score"], '-') ?> </td>
                        <td style="width: 33%;"> <?= coalesce($mecz["B_team"], '???') ?> </td>
                        <tr>
                    </table>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <?php if (!is_null($obj["tabele"])) for ($grupa = 1; $grupa <= 2; ++$grupa) : ?>
            <div id="grupa-<?= $grupa == 1 ? "pierwsza" : "druga" ?>">
                <?php
                if (!is_null($obj["finalowe"])) :
                    $mecz = $obj['finalowe'][$grupa - 1]; // half1, half2, final, third
                ?>
                    <h2> <?= $mecz["title"] ?> </h2>
                    <table class="tabela" cellspacing="0">
                        <tr>
                            <th colspan="3"> <?= coalesce($mecz['date'], 'nie ustalono') ?> </th>
                        </tr>
                        <td style="width: 33%;"> <?= coalesce($mecz["A_team"], '???') ?> </td>
                        <td style="width: 33%;"> <?= coalesce($mecz["A_score"], '-') ?> : <?= coalesce($mecz["B_score"], '-') ?> </td>
                        <td style="width: 33%;"> <?= coalesce($mecz["B_team"], '?') ?> </td>
                        <tr>
                    </table>
                <?php endif; ?>

                <h2> <?= $obj["podzial"][$grupa - 1] ?> </h2>
                <!------------------ TEBELA ------------------>
                <p class="big spacious"> TABELA </p>
                <table class="tabela" cellspacing="0">
                    <tr>
                        <th> LP </th>
                        <th> ZESPÓŁ </th>
                        <th> PKT </th>
                        <th> Z </th>
                        <th> R </th>
                        <th> P </th>
                        <th> Strzel. </th>
                        <th> Strac. </th>
                        <th> +/- </th>
                    </tr>
                    <?php foreach ($obj["tabele"][$grupa - 1] as $i => $t) : ?>
                        <tr>
                            <td> <?= $i + 1 ?> </td>
                            <td> <?= $t['team'] ?> </td>
                            <td> <?= $t['points'] ?> </td>
                            <td> <?= $t['win'] ?> </td>
                            <td> <?= $t['tie'] ?> </td>
                            <td> <?= $t['los'] ?> </td>
                            <td> <?= $t['gain'] ?> </td>
                            <td> <?= $t['lost'] ?> </td>
                            <td> <?= $t['delta'] ?> </td>
                        </tr>
                    <?php endforeach; ?>
                </table>

                <p class="big spacious"> HARMONOGRAM </p>
                <?php
                foreach ($obj["harmonogram"][$grupa - 1] as $mecz) :
                ?>
                    <table class="terminarz" cellspacing="0">
                        <tr class="termin">
                            <td colspan="3"> <?= coalesce($mecz['date'], 'nie ustalono') ?> </td>
                        </tr>
                        <tr class="wynik">
                            <td> <?= coalesce($mecz['A_team'], '???') ?> </td>
                            <td class="wynik">
                                <?= coalesce($mecz['A_score'], '-') ?>:<?= coalesce($mecz['B_score'], '-') ?>
                            </td>
                            <td> <?= coalesce($mecz['B_team'], '???') ?> </td>
                        </tr>
                    </table>
                <?php
                endforeach;
                ?>
            </div>
        <?php endfor; ?>
    </div>
<?php }
