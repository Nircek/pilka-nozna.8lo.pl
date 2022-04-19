<?php
register_style("index");
register_title("Strona główna");

function page_init()
{
    $sezon = obecny_sezon();
    if (!is_null($sezon)) {
        $name_stmt = PDOS::Instance()->prepare("SELECT `name` FROM `ng_season` WHERE `season_id` = ?;"); // get_season_name(season)
        $name_stmt->execute([$sezon]);
        $name = $name_stmt->fetchAll(PDO::FETCH_COLUMN);
        $name = count($name) > 0 ? $name[0] : null;
        $details_stmt = PDOS::Instance()->prepare("SELECT `description`, `grouping_type` FROM `ng_season` WHERE `season_id` = ?;");
        $details_stmt->execute([$sezon]);
        $details = $details_stmt->fetchAll(PDO::FETCH_ASSOC)[0];
        $tabele = array();
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
        for ($i = 1; $i <= 2; ++$i) {
            $tabela_stmt->execute([$sezon, false, $i == 1 ? 'first' : 'second']);
            $tabele[] = $tabela_stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
    $zdjecia_stmt = PDOS::Instance()->prepare( // get_random_photos(PREFIX, count)
        "SELECT
            CONCAT(IF(`type`='filename', CONCAT(?, '/zdjecia/thumb.'), ''), `content`) AS `thumb_url`
        FROM `ng_photo` ORDER BY RAND() LIMIT 4;"
    );
    $zdjecia_stmt->execute([PREFIX]);
    return array(
        'zdjecia' => $zdjecia_stmt->fetchAll(PDO::FETCH_COLUMN),
        'sezon' => $sezon,
        'sezon_name' => isset($name) ? $name : NULL,
        'tabele' => isset($tabele) ? $tabele : NULL,
        'podzial' => isset($details) ?
            ($details['grouping_type'] == "two_rounds" ?
                array("RUNDA ZASADNICZA", "RUNDA REWANŻOWA") :
                array("GRUPA PIERWSZA", "GRUPA DRUGA"))
            : NULL,
        'informacje' => PDOS::Instance()->query(
            "SELECT
                `article_id`, `title`, `content`, `created_at`
            FROM `ng_article` WHERE `publish_on_news_page` = 1
            ORDER BY `article_id` DESC LIMIT 6;"
        )->fetchAll(PDO::FETCH_ASSOC),
        'tabele' => isset($tabele) ? $tabele : NULL
    );
}

function page_render($obj)
{
?>
    <div id="content">
        <div id="columns">
            <div id="left-content">
                <h1> GALERIA </h1>
                <?php foreach ($obj["zdjecia"] as $zdjecie) : ?>
                    <div class='image'>
                        <img src='<?= $zdjecie ?>' width='192' />
                        <!--wysokość auto. Nadwyżka zostanie ucięta-->
                    </div>
                <?php endforeach; ?>
                <div id="image-button">
                    <a href="<?= PREFIX ?>/galeria"><br /> ... </a>
                </div>
            </div>
            <div id="center-content">
                <h1> INFORMACJE </h1>
                <div id="informacje-content">
                    <?php foreach ($obj["informacje"] as $info) : ?>
                        <div class='info'>
                            <h3> <?= $info['title'] ?> </h3>
                            <span id='tresc'>
                                <?= $info['content'] ?>
                            </span>
                            <br />
                            <div id='data'>
                                <?= $info['created_at'] ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div id="info-button">
                    <a href="<?= PREFIX ?>/informacje"><br />...</a>
                </div>
            </div>
            <div id="right-content">
                <?php if (is_null($obj["sezon"])) :   ?>
                    <div class="error"> Nie ma rozgrywek... </div>
                <?php else : ?>
                    <h2> TABELA <?= $obj["sezon_name"] ?> </h2>
                    <?php if (!is_null($obj["tabele"])) for ($grupa = 0; $grupa < 2; ++$grupa) : ?>
                        <h2> <?= $obj["podzial"][$grupa] ?> </h2>
                        <table id="tabela" cellspacing="0">
                            <tr>
                                <th> LP </th>
                                <th> ZESPÓŁ </th>
                                <th> PKT </th>
                                <th> Z </th>
                                <th> R </th>
                                <th> P </th>
                            </tr>
                            <?php foreach ($obj["tabele"][$grupa] as $i => $t) : ?>
                                <tr>
                                    <td> <?= $i + 1 ?> </td>
                                    <td> <?= $t['team'] ?> </td>
                                    <td> <?= $t['points'] ?> </td>
                                    <td> <?= $t['win'] ?> </td>
                                    <td> <?= $t['tie'] ?> </td>
                                    <td> <?= $t['los'] ?> </td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    <?php endfor; ?>
                <?php endif; ?>
            </div>
            <div style="clear: both;"></div>
        </div>
    </div>

<?php }
