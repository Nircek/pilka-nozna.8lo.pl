<?php
define("GALERIA_URL", PREFIX . "/galeria");
$sezon = cast_int(HIT_UNPACK());
if (is_null($sezon)) {
    header("Location: " . GALERIA_URL);
    report_error("sezon violation", NULL);
    exit();
}

register_additional_title("Sezon $sezon/" . ($sezon + 1));

$exist_stmt = PDOS::Instance()->prepare("SELECT COUNT(id) FROM `zdjecia` WHERE sezon = ?");
$exist_stmt->execute([$sezon]);
if ($exist_stmt->fetchAll(PDO::FETCH_COLUMN)[0] == 0) {
    header("Location: " . GALERIA_URL);
    report_error("Podany sezon nie istnieje...", NULL);
    exit();
}
HIT_PACK($sezon);

function page_init()
{
    $sezon = HIT_UNPACK();
    $galeria_stmt = PDOS::Instance()->prepare("SELECT * FROM zdjecia WHERE sezon = ? ORDER BY data");
    $galeria_stmt->execute([$sezon]);
    return $galeria_stmt->fetchAll(PDO::FETCH_ASSOC);
}

function page_render($obj)
{
?>
    <div id="content">
        <div id="powrot"><a href="<?= PREFIX ?>/galeria"> &#8592; POWRÓT </a></div>
        <h1> GALERIA </h1>
        <div id='podglad'>
            <div id='lewo' onclick='lewo()'></div>
            <img id='glowne_zdjecie' src='<?= PREFIX ?>/<?= $obj[0]["sciezka"] ?>' value='' />
            <div id='prawo' onclick='prawo()'></div>
            <div style='clear: both'></div>
        </div>
        <?php foreach ($obj as $i => $zdj) :
            // W 'id' i skrypcie 'laduj()' znajduje się taka sama liczba przez co JS może ją stąd pobrać
            // Jak pobierze liczbę w ID to od razu zna liczbę sciezki przez co może ją dopasować i podmienić w zdjęciu na 'podgladzie'
            $pathinf = pathinfo($zdj['sciezka']);
        ?>
            <div class='zdjecie'>
                <img width='172' id='<?= $i ?>' height='98' src='<?= PREFIX ?>/<?= $pathinf['dirname'] ?>/thumb.<?= $pathinf['basename'] ?>' srcfull='<?= PREFIX ?>/<?= $zdj['sciezka'] ?>' onclick='laduj(<?= $i ?>)' />
            </div>
        <?php endforeach; ?>
        <div style='clear: both;'></div>
        <script src="<?= PREFIX ?>/js/galeria.js"></script>
    </div>
<?php }
