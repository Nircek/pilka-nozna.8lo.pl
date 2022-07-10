<?php
is_logged();
define("WYNIKI_URL", PREFIX . "/admin/wyniki");
header('Location: ' . WYNIKI_URL);

$sezon = cast_int($_POST['sezon']);
if (is_null($sezon)) {
    report_error("sezon violation", NULL);
    exit();
}

$games = PDOS::Instance()->cmd(
    "get_game_types(season)",
    [$sezon]
)->fetchAll(PDO::FETCH_ASSOC);

try {
    PDOS::Instance()->beginTransaction();
    $update_final = false;
    foreach ($games as $game) {
        $w1 = $_POST[$game['game_id'] . '_1'];
        $w2 = $_POST[$game['game_id'] . '_2'];
        $w1 = $w1 === '' ? '' : cast_int($w1);
        $w2 = $w2 === '' ? '' : cast_int($w2);
        if (is_null($w1) or is_null($w2)) continue;
        $half = (substr($game['type'], 0, 4) == 'half');
        if ($half and $w1 !== '' and $w1 === $w2) {
            report_error("W półfinale nie może być remisu!", NULL);
            continue;
        }
        if ($half) $update_final = true;
        if($w1 === '' or $w2 === '') $w1 = $w2 = NULL;
        PDOS::Instance()->cmd(
            "set_game_score(A, B, season, game_id)",
            [$w1, $w2, $sezon, $game['game_id']]
        );
    }
    if ($update_final) {
        PDOS::Instance()->cmd("update_final_participants(season)", [$sezon]);
    }
    PDOS::Instance()->commit();
} catch (Exception $e) {
    PDOS::Instance()->rollback();
    throw $e;
}

exit();
