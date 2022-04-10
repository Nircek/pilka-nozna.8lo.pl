<?php
register_style("index");
register_title("Strona główna");

function page_init()
{
    $obecny_sezon = PDOS::Instance()->query("SELECT sezon FROM sezony ORDER BY sezon DESC LIMIT 1")->fetchAll(PDO::FETCH_COLUMN);
    $tabele = array();
    if (!empty($obecny_sezon)) {
        $obecny_sezon = $obecny_sezon[0];
        $sezon_tabela = "${obecny_sezon}_tabela";
        $tabela_stmt = PDOS::Instance()->prepare(
            "SELECT CONCAT(@rownum := @rownum + 1, '.') AS i, t.*
            FROM $sezon_tabela t, (SELECT @rownum := 0) r
            WHERE grupa = ? ORDER BY pkt DESC, (zdobyte - stracone) DESC, nazwa ASC"
        );
        for ($i = 1; $i <= 2; ++$i) {
            $tabela_stmt->execute([$i]);
            $tabele[] = $tabela_stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    } else $obecny_sezon = NULL;
    return array(
        'zdjecia' => PDOS::Instance()->query("SELECT sciezka FROM zdjecia ORDER BY RAND() LIMIT 4")->fetchAll(PDO::FETCH_COLUMN),
        'sezon' => $obecny_sezon,
        'informacje' => PDOS::Instance()->query("SELECT * FROM informacje ORDER BY id DESC LIMIT 6")->fetchAll(PDO::FETCH_ASSOC),
        'tabele' => $tabele
    );
}

function generate_table($tabela)
{
?>
    <table id="tabela" cellspacing="0">
        <tr>
            <th> LP </th>
            <th> ZESPÓŁ </th>
            <th> PKT </th>
            <th> Z </th>
            <th> R </th>
            <th> P </th>
        </tr>
        <?php foreach ($tabela as $t) :
        ?>
            <tr>
                <td> <?= $t['i'] ?> </td>
                <td> <?= $t['nazwa'] ?> </td>
                <td> <?= $t['pkt'] ?> </td>
                <td> <?= $t['zwyciestwa'] ?> </td>
                <td> <?= $t['remisy'] ?> </td>
                <td> <?= $t['przegrane'] ?> </td>
            </tr>

        <?php endforeach; ?>
    </table>
<?php
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
                        <img src='<?= PREFIX ?>/<?= $zdjecie ?>' width='192' />"
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
                            <h3> <?= $info['tytul'] ?> </h3>
                            <span id='tresc'>
                                <?= $info['tresc'] ?>
                            </span>
                            <br />
                            <div id='data'>
                                <?= $info['data'] ?>
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
                    <div class="error"> Nie ma żadnego sezonu... </div>
                <?php else : ?>
                    <h2> TABELA <?= $obj["sezon"] ?>/<?= $obj["sezon"] + 1 ?> </h2>
                    <h3> GRUPA PIERWSZA </h3>
                    <?php generate_table($obj["tabele"][0]) ?>
                    <h3> GRUPA DRUGA </h3>
                    <?php generate_table($obj["tabele"][1]) ?>
                <?php endif; ?>
            </div>
            <div style="clear: both;"></div>
        </div>
    </div>

<?php }
