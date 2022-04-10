<?php
define("SEZONY_URL", PREFIX . "/sezony");
$sezon = cast_int(HIT_UNPACK());
if (is_null($sezon)) {
    report_error("sezon violation", NULL);
    exit();
}

register_additional_title("Sezon $sezon/" . ($sezon + 1));

if (!sprawdzanie_tabela("${sezon}_tabela") || !sprawdzanie_tabela("${sezon}_terminarz")) {
    header("Location: " . SEZONY_URL);
    report_error("Podany sezon nie istnieje...", NULL);
    exit();
}
HIT_PACK($sezon);

function page_init()
{
    $sezon = HIT_UNPACK();
    $sezon_tabela = "${sezon}_tabela";
    $sezon_final = "${sezon}_final";
    $sezon_terminarz = "${sezon}_terminarz";
    $tabele = array();
    $harmonogram = array();
    $tabela_stmt = PDOS::Instance()->prepare(
        "SELECT CONCAT(@rownum := @rownum + 1, '.') AS i, t.*,
            CONCAT(IF((zdobyte - stracone)>0,'+',''),zdobyte - stracone) AS bilans
        FROM (SELECT @rownum := 0) r, `$sezon_tabela` t WHERE grupa = ? ORDER BY pkt DESC, (zdobyte - stracone) DESC, nazwa ASC"
    );
    $harmonogram_stmt = PDOS::Instance()->prepare(
        "SELECT *,
            IFNULL(`1_wynik`, '-') AS w1,
            IFNULL(`2_wynik`, '-') AS w2,
            CASE WHEN termin IS NULL OR YEAR(termin) = 0 THEN 'nie ustalono' ELSE termin END AS data
        FROM `$sezon_terminarz` WHERE grupa = ? ORDER BY termin DESC"
    );
    for ($i = 1; $i <= 2; ++$i) {
        $tabela_stmt->execute([$i]);
        $tabele[] = $tabela_stmt->fetchAll(PDO::FETCH_ASSOC);
        $harmonogram_stmt->execute([$i]);
        $harmonogram[] = $harmonogram_stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    return array(
        'sezon' => $sezon,
        'tabele' => $tabele,
        'harmonogram' => $harmonogram,
        'finalowe' => sprawdzanie_tabela($sezon_final) ? PDOS::Instance()->query(
            "SELECT *,
                SUBSTRING_INDEX(SUBSTRING_INDEX('FINAŁ,PÓŁFINAŁ,3 MIEJSCE',',', poziom),',',-1) AS tytul,
                CASE WHEN YEAR(termin) = 0 THEN 'nie ustalono' ELSE termin END AS data,
                IFNULL(wynik_1, '?') AS w1, IFNULL(wynik_2, '?') AS w2
            FROM `$sezon_final` ORDER BY id DESC"
        )->fetchAll(PDO::FETCH_ASSOC) : NULL,
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
                <h1> SEZON <?= $obj["sezon"] ?>/<?= $obj["sezon"] + 1 ?> </h1>
            </span>
        </div>
        <?php if (!is_null($obj["finalowe"])) : ?>
            <div id="runda-finalowa">
                <?php
                foreach ($obj["finalowe"] as $mecz)
                    if ($mecz["tytul"] == "FINAŁ" or $mecz["tytul"] == "3 MIEJSCE") : ?>
                    <h2> <?= $mecz["tytul"] ?> </h2>
                    <table id='tabela' cellspacing='0'>
                        <tr>
                            <th colspan='3'> <?= $mecz["data"] ?> </th>
                        </tr>
                        <td style='width: 33%;'> <?= $mecz["druzyna_1"] ?> </td>
                        <td style='width: 33%;'> <?= $mecz["w1"] ?> : <?= $mecz["w2"] ?> </td>
                        <td style='width: 33%;'> <?= $mecz["druzyna_2"] ?> </td>
                        <tr>
                    </table>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <?php for ($grupa = 1; $grupa <= 2; ++$grupa) : ?>
            <div id="grupa-<?= $grupa == 1 ? "pierwsza" : "druga" ?>">
                <?php
                if (!is_null($obj["finalowe"])) :
                    $mecz = $obj['finalowe'][$grupa == 1 ? 3 : 2];
                ?>
                    <h2> <?= $mecz["tytul"] ?> </h2>
                    <table id='tabela' cellspacing='0'>
                        <tr>
                            <th colspan='3'> <?= $mecz["data"] ?> </th>
                        </tr>
                        <td style='width: 33%;'> <?= $mecz["druzyna_1"] ?> </td>
                        <td style='width: 33%;'> <?= $mecz["w1"] ?> : <?= $mecz["w2"] ?> </td>
                        <td style='width: 33%;'> <?= $mecz["druzyna_2"] ?> </td>
                        <tr>
                    </table>
                <?php endif; ?>

                <h2> GRUPA <?= $grupa == 1 ? "PIERWSZA" : "DRUGA" ?> </h2>
                <!------------------ TEBELA ------------------>
                <h3> TABELA </h3>
                <table id="tabela" cellspacing="0">
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
                    <?php foreach ($obj["tabele"][$grupa - 1] as $t) : ?>
                        <tr>
                            <td> <?= $t['i'] ?> </td>
                            <td> <?= $t['nazwa'] ?> </td>
                            <td> <?= $t['pkt'] ?> </td>
                            <td> <?= $t['zwyciestwa'] ?> </td>
                            <td> <?= $t['remisy'] ?> </td>
                            <td> <?= $t['przegrane'] ?> </td>
                            <td> <?= $t['zdobyte'] ?> </td>
                            <td> <?= $t['stracone'] ?> </td>
                            <td> <?= $t['bilans'] ?> </td>
                        </tr>
                    <?php endforeach; ?>
                </table>

                <h3> HARMONOGRAM </h3>
                <?php
                foreach ($obj["harmonogram"][$grupa - 1] as $mecz) :
                ?>
                    <table id='terminarz' cellspacing='0'>
                        <tr id='tr_termin'>
                            <td colspan='3'> <?= $mecz['data'] ?> </td>
                        </tr>
                        <tr id='tr_wynik'>
                            <td> <?= $mecz['1_text'] ?> </td>
                            <td id='td_wynik'>
                                <?= $mecz['w1'] ?>:<?= $mecz['w2'] ?>
                            </td>
                            <td> <?= $mecz['2_text'] ?> </td>
                        </tr>
                    </table>
                <?php
                endforeach;
                ?>
            </div>
        <?php endfor; ?>
    </div>
<?php }
