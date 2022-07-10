<?php
register_style("index");
register_title("Strona główna");

function page_init()
{
    $sezon = obecny_sezon();
    if (!is_null($sezon)) {
        $name = PDOS::Instance()->cmd(
            "get_season_name(season)",
            [$sezon]
        )->fetchAll(PDO::FETCH_COLUMN);
        $name = count($name) > 0 ? $name[0] : null;
        $details = PDOS::Instance()->cmd(
            "get_season_details(season)",
            [$sezon]
        )->fetchAll(PDO::FETCH_ASSOC)[0];
        $tabele = array();
        for ($i = 1; $i <= 2; ++$i) {
            $tabele[] = PDOS::Instance()->cmd(
                "get_group_table(season, all?, group)",
                [$sezon, false, $i == 1 ? 'first' : 'second']
            )->fetchAll(PDO::FETCH_ASSOC);
        }
    }
    return array(
        'zdjecia' => PDOS::Instance()->cmd(
            "get_4_random_photos(PREFIX)",
            [PREFIX]
        )->fetchAll(PDO::FETCH_COLUMN),
        'sezon' => $sezon,
        'sezon_name' => isset($name) ? $name : null,
        'tabele' => isset($tabele) ? $tabele : null,
        'podzial' => isset($details) ?
            ($details['grouping_type'] == "two_rounds" ?
                array("RUNDA ZASADNICZA", "RUNDA REWANŻOWA") :
                array("GRUPA PIERWSZA", "GRUPA DRUGA"))
            : null,
        'informacje' => PDOS::Instance()->cmd("get_recent_news()")->fetchAll(PDO::FETCH_ASSOC),
        'tabele' => isset($tabele) ? $tabele : null
    );
}

function page_render($obj)
{
    ?>
    <div id="content" class="fullish">
        <div class="left-drawer">
            <h1> GALERIA </h1>
            <?php foreach ($obj["zdjecia"] as $zdjecie) : ?>
                <div class='image'>
                    <img src='<?= $zdjecie ?>' />
                    <!--wysokość auto. Nadwyżka zostanie ucięta-->
                </div>
            <?php endforeach; ?>
            <div class="image link button">
                <a href="<?= PREFIX ?>/galeria"><div class="center-vert"> &middot;&middot;&middot; </div></a>
            </div>
        </div>
        <div class="center-drawer">
            <h1> INFORMACJE </h1>
            <div id="informacje-content">
                <?php foreach ($obj["informacje"] as $info) : ?>
                    <div class='info'>
                        <div class="informacja-tytul"> <?= $info['title'] ?> </div>
                        <div class="informacja-tresc"> <?= $info['content'] ?> </div>
                        <div class="informacja-data"> <?= $info['created_at'] ?> </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="link button">
                <a href="<?= PREFIX ?>/informacje"><div class="center-vert"> &middot;&middot;&middot; </div></a>
            </div>
        </div>
        <div class="right-drawer">
            <?php if (is_null($obj["sezon"])) :   ?>
                <div class="error"> Nie ma rozgrywek... </div>
            <?php else : ?>
                <h2> TABELA <?= $obj["sezon_name"] ?> </h2>
                <?php if (!is_null($obj["tabele"])) for ($grupa = 0; $grupa < 2; ++$grupa) : ?>
                    <h2> <?= $obj["podzial"][$grupa] ?> </h2>
                    <div class="tabela-container">
                    <table class="tabela" cellspacing="0">
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
                    </div>
                <?php endfor; ?>
            <?php endif; ?>
        </div>
        <div style="height: 0; clear: both;"></div>
    </div>

<?php
}
