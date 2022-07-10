<?php
is_logged();
define("HARMONOGRAM_URL", PREFIX . "/admin/harmonogram");
header('Location: ' . HARMONOGRAM_URL);

$sezon = cast_int($_POST['sezon']);
if (is_null($sezon)) {
    report_error("sezon violation", NULL);
    exit();
}

$ids = PDOS::Instance()->cmd("get_game_ids(season)", [$sezon])->fetchAll(PDO::FETCH_COLUMN);
foreach ($ids as $id) {
    $termin = $_POST[$id];
    PDOS::Instance()->cmd(
        "set_game_date(date, season, game_id)",
        [$termin, $sezon, $id]
    );
}

exit();
