<?php
define("SEZONY_URL", PREFIX . "/sezony");
$sezon = cast_int(HIT_UNPACK());

$name = PDOS::Instance()->cmd("get_season_name(season)", [$sezon])->fetchAll(PDO::FETCH_COLUMN);
$name = count($name) > 0 ? $name[0] : null;

if (is_null($name)) {
    header("Location: " . SEZONY_URL);
    report_error("Podany sezon nie istnieje...", null);
    exit();
}
HIT_PACK($sezon);

register_additional_title("Sezon $name");

function page_init()
{
    $sezon = HIT_UNPACK();
    $name = PDOS::Instance()->cmd("get_season_name(season)", [$sezon])->fetchAll(PDO::FETCH_COLUMN);
    $name = count($name) > 0 ? $name[0] : null;
    $details = PDOS::Instance()->cmd("get_season_details(season)", [$sezon])->fetch(PDO::FETCH_ASSOC);
    if ($details['grouping_type'] != 'no_grouping') {
        $tabele = array();
        $harmonogram = array();
        for ($i = 1; $i <= 2; ++$i) {
            $tabele[] = PDOS::Instance()->cmd(
                "get_group_table(season, all?, group)",
                [$sezon, false, $i == 1 ? 'first' : 'second']
            )->fetchAll(PDO::FETCH_ASSOC);
            $harmonogram[] = PDOS::Instance()->cmd("get_games(season, finals?, group)", [$sezon, false, $i == 1 ? 'first' : 'second'])->fetchAll(PDO::FETCH_ASSOC);
        }
        if ($details['grouping_type'] == "two_rounds") {
            $cala_tabela = PDOS::Instance()->cmd(
                "get_group_table(season, all?, group)",
                [$sezon, true, '']
            )->fetchAll(PDO::FETCH_ASSOC);
        } elseif ($details['grouping_type'] == "two_groups") {
            $finalowe = PDOS::Instance()->cmd(
                "get_games(season, finals?, group)",
                [$sezon, true, '']
            )->fetchAll(PDO::FETCH_ASSOC);
        }
    }
    return array(
        'sezon' => $sezon,
        'sezon_nazwa' => $name,
        'tabele' => isset($tabele) ? $tabele : null,
        'opis' => $details['description'],
        'podzial' => $details['grouping_type'] == "two_rounds" ? array("RUNDA ZASADNICZA", "RUNDA REWANŻOWA") : array("GRUPA PIERWSZA", "GRUPA DRUGA"),
        'cala_tabela' => isset($cala_tabela) ? $cala_tabela : null,
        'harmonogram' => isset($harmonogram) ? $harmonogram : null,
        'finalowe' => (isset($finalowe) and count($finalowe) > 0) ? $finalowe : null,
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
                    // half1, half2, final, third
                    $mecz = $obj['finalowe'][$grupa - 1]; ?>
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
                <?php endforeach; ?>
            </div>
        <?php endfor; ?>
    </div>
<?php
}
