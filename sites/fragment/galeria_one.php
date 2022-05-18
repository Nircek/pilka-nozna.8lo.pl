<?php
define("GALERIA_URL", PREFIX . "/galeria");
$sezon = cast_int(HIT_UNPACK());

$exist_stmt = PDOS::Instance()->prepare("SELECT COUNT(*) FROM `ng_photo` WHERE `season_id` = ?"); // count_gallery_photos(season)
$exist_stmt->execute([$sezon]);
$name_stmt = PDOS::Instance()->prepare("SELECT `name` FROM `ng_season` WHERE `season_id` = ?"); // get_season_name(season)
$name_stmt->execute([$sezon]);
$name = $name_stmt->fetchAll(PDO::FETCH_COLUMN);
$name = count($name) > 0 ? $name[0] : null;
if ($exist_stmt->fetchAll(PDO::FETCH_COLUMN)[0] == 0 or is_null($name)) {
    header("Location: " . GALERIA_URL);
    report_error("Podany sezon nie istnieje...", NULL);
    exit();
}
register_additional_title("Sezon $name");
HIT_PACK($sezon);

function page_init()
{
    $sezon = HIT_UNPACK();
    $name_stmt = PDOS::Instance()->prepare("SELECT `name` FROM `ng_season` WHERE `season_id` = ?;"); // get_season_name(season)
    $name_stmt->execute([$sezon]);
    $name = $name_stmt->fetchAll(PDO::FETCH_COLUMN);
    $name = count($name) > 0 ? $name[0] : null;
    $galeria_stmt = PDOS::Instance()->prepare( // get_gallery_photos(PREFIX, season)
        "SELECT
            `photo_id`, `game_id`, `date`, `type`, CONCAT(IF(`type`='filename', CONCAT(`PREFIX`, '/zdjecia/'), ''), `content`) AS `url`,
            CONCAT(IF(`type`='filename', CONCAT(`PREFIX`, '/zdjecia/thumb.'), ''), `content`) AS `thumb_url`, `photographer_id`, `credit_photographer`, `comment`
        FROM `ng_photo` p, (SELECT ? AS PREFIX) P
            WHERE `season_id` = ?
        ORDER BY `date`, `photographer_id`, `photo_id`;"
    );
    $galeria_stmt->execute([PREFIX, $sezon]);
    return array(
        'sezon_nazwa' => $name,
        'zdjecia' => $galeria_stmt->fetchAll(PDO::FETCH_ASSOC)
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
