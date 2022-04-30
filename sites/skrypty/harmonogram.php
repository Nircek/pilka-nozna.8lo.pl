<?php
is_logged();
define("HARMONOGRAM_URL", PREFIX . "/admin/harmonogram");
header('Location: ' . HARMONOGRAM_URL);

$sezon = cast_int($_POST['sezon']);
if (is_null($sezon)) {
    report_error("sezon violation", NULL);
    exit();
}

$ids_stmt = PDOS::Instance()->prepare( // get_game_ids(season)
    "SELECT `game_id` FROM `ng_game` WHERE `season_id` = ?;"
);
$ids_stmt->execute([$sezon]);
$ids = $ids_stmt->fetchAll(PDO::FETCH_COLUMN);
foreach ($ids as $id) {
    $termin = $_POST[$id];
    PDOS::Instance()->prepare( // set_game_date(date, season, game_id)
        "UPDATE `ng_game` SET `date` = ? WHERE `season_id` = ? AND `game_id` = ?;"
    )->execute([$termin, $sezon, $id]);
}

exit();
