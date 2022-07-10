<?php
define("GALERIA_URL", PREFIX . "/galeria");
$sezon = cast_int(HIT_UNPACK());

$name = PDOS::Instance()->cmd("get_season_name(season)", [$sezon])->fetchAll(PDO::FETCH_COLUMN);
$name = count($name) > 0 ? $name[0] : null;
if (is_null($name) or PDOS::Instance()->cmd("count_gallery_photos(season)", [$sezon])->fetchAll(PDO::FETCH_COLUMN)[0] == 0) {
    header("Location: " . GALERIA_URL);
    report_error("Podany sezon nie istnieje...", NULL);
    exit();
}
register_additional_title("Sezon $name");
HIT_PACK($sezon);

function page_init()
{
    $sezon = HIT_UNPACK();
    $name = PDOS::Instance()->cmd(
        "get_season_name(season)",
        [$sezon]
    )->fetchAll(PDO::FETCH_COLUMN);
    $name = count($name) > 0 ? $name[0] : null;
    return array(
        'sezon_nazwa' => $name,
        'zdjecia' => PDOS::Instance()->cmd(
            "get_gallery_photos(PREFIX, season)",
            [PREFIX, $sezon]
        )->fetchAll(PDO::FETCH_ASSOC)
    );
}

function page_render($obj)
{
?>
    <div id="content">
        <div id="powrot"><a href="<?= PREFIX ?>/galeria"> &#8592; POWRÓT </a></div>
        <h1> GALERIA <?= $obj['sezon_nazwa'] ?> </h1>
        <div id='podglad'>
            <div id='lewo' onclick='lewo()'></div>
            <img id='glowne_zdjecie' src='<?= $obj[0]["url"] ?>' value='' />
            <div id='prawo' onclick='prawo()'></div>
            <div style='clear: both'></div>
        </div>
        <?php foreach ($obj['zdjecia'] as $i => $zdj) :
            // W 'id' i skrypcie 'laduj()' znajduje się taka sama liczba przez co JS może ją stąd pobrać
            // Jak pobierze liczbę w ID to od razu zna liczbę sciezki przez co może ją dopasować i podmienić w zdjęciu na 'podgladzie'
        ?>
            <div class='zdjecie'>
                <img width='172' id='<?= $i ?>' height='98' src='<?= $zdj['thumb_url'] ?>' srcfull='<?= $zdj['url'] ?>' onclick='laduj(<?= $i ?>)' />
            </div>
        <?php endforeach; ?>
        <div style='clear: both;'></div>
        <script src="<?= PREFIX ?>/js/galeria.js"></script>
    </div>
<?php }
