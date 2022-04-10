<?php
is_logged();
define("WYNIKI_URL", PREFIX . "/admin/wyniki");
header('Location: ' . WYNIKI_URL);

$sezon = cast_int($_POST['sezon']);
if (is_null($sezon)) {
    report_error("sezon violation", NULL);
    exit();
}

$sezon_tabela = "${sezon}_tabela";
$sezon_terminarz = "${sezon}_terminarz";
$sezon_final = "${sezon}_final";
if (sprawdzanie_tabela($sezon_final)) {
    $final = PDOS::Instance()->query("SELECT id, poziom FROM `$sezon_final`")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($final as $mecz) {
        $w1 = cast_int($_POST['f' . $mecz['id'] . '_1']);
        $w2 = cast_int($_POST['f' . $mecz['id'] . '_2']);
        if (is_null($w1) or is_null($w2)) continue;
        if ($mecz['poziom'] = 2 and $w1 == $w2) {
            report_error("W półfinale nie może być remisu!", NULL);
            continue;
        }
        PDOS::Instance()->prepare("UPDATE `$sezon_final` SET wynik_1 = ?, wynik_2 = ? WHERE id = ?")->execute([$w1, $w2, $mecz['id']]);
    }

    $druzyny = PDOS::Instance()->query(
        "SELECT
            IF(wynik_1-wynik_2,IF(wynik_1>wynik_2,druzyna_1,druzyna_2),NULL) AS w,
            IF(wynik_1-wynik_2,IF(wynik_1<wynik_2,druzyna_1,druzyna_2),NULL) AS p
        FROM `$sezon_final` WHERE poziom=2"
    )->fetchAll(PDO::FETCH_ASSOC);
    PDOS::Instance()->prepare("UPDATE $sezon_final SET druzyna_1 = ?, druzyna_2 = ? WHERE poziom=1")->execute([$druzyny[0]['w'], $druzyny[1]['w']]);
    PDOS::Instance()->prepare("UPDATE $sezon_final SET druzyna_1 = ?, druzyna_2 = ? WHERE poziom=3")->execute([$druzyny[0]['p'], $druzyny[1]['p']]);
}
$ids = PDOS::Instance()->query("SELECT id FROM `$sezon_terminarz`")->fetchAll(PDO::FETCH_COLUMN);
foreach ($ids as $id) {
    $w1 = cast_int($_POST[$id . '_1']);
    $w2 = cast_int($_POST[$id . '_2']);
    if (is_null($w1) or is_null($w2)) continue;
    PDOS::Instance()->prepare("UPDATE `$sezon_terminarz` SET `1_wynik` = ?, `2_wynik` = ? WHERE id = ?")->execute([$w1, $w2, $id]);
}
try {
    PDOS::Instance()->beginTransaction();
    PDOS::Instance()->prepare("TRUNCATE `$sezon_tabela`")->execute();
    if (true) { // TODO: jeden lub dwa sezony
        PDOS::Instance()->prepare(
            "INSERT INTO `$sezon_tabela` (`numer`,`nazwa`,`grupa`,`zwyciestwa`,`remisy`,`przegrane`,`zdobyte`,`stracone`,`pkt`)
            SELECT id, name, g, win, tie, los, gain, lost, 3*win+tie AS pkt FROM (
                SELECT us AS id, name, g, SUM(IF(our>their,1,0)) AS win, SUM(IF(our=their,1,0)) AS tie, SUM(IF(our<their,1,0)) AS los, SUM(our) AS gain, SUM(their) AS lost FROM
                    (SELECT 1 AS g, `1_num` AS us, `1_text` AS name, `1_wynik` AS our, `2_wynik` AS their FROM `$sezon_terminarz` WHERE grupa=1
                    UNION
                    SELECT 1 AS g, `2_num` AS us, `2_text` AS name, `2_wynik` AS our, `1_wynik` AS their FROM `$sezon_terminarz` WHERE grupa=1
                    UNION
                    SELECT 2 AS g, `1_num` AS us, `1_text` AS name, `1_wynik` AS our, `2_wynik` AS their FROM `$sezon_terminarz`
                    UNION
                    SELECT 2 AS g, `2_num` AS us, `2_text` AS name, `2_wynik` AS our, `1_wynik` AS their FROM `$sezon_terminarz`) AS t GROUP BY g, name
            ) AS tt ORDER BY g, id"
        )->execute();
    } else {
        PDOS::Instance()->prepare(
            "INSERT INTO `$sezon_tabela` (`numer`,`nazwa`,`grupa`,`zwyciestwa`,`remisy`,`przegrane`,`zdobyte`,`stracone`,`pkt`)
            SELECT id, name, g, win, tie, los, gain, lost, 3*win+tie AS pkt FROM (
                SELECT us AS id, name, g, SUM(IF(our>their,1,0)) AS win, SUM(IF(our=their,1,0)) AS tie, SUM(IF(our<their,1,0)) AS los, SUM(our) AS gain, SUM(their) AS lost FROM
                    (SELECT 1 AS g, `1_num` AS us, `1_text` AS name, `1_wynik` AS our, `2_wynik` AS their FROM `$sezon_terminarz` WHERE grupa=1
                    UNION
                    SELECT 1 AS g, `2_num` AS us, `2_text` AS name, `2_wynik` AS our, `1_wynik` AS their FROM `$sezon_terminarz` WHERE grupa=1
                    UNION
                    SELECT 2 AS g, `1_num` AS us, `1_text` AS name, `1_wynik` AS our, `2_wynik` AS their FROM `$sezon_terminarz` WHERE grupa=2
                    UNION
                    SELECT 2 AS g, `2_num` AS us, `2_text` AS name, `2_wynik` AS our, `1_wynik` AS their FROM `$sezon_terminarz` WHERE grupa=2) AS t GROUP BY g, name
            ) AS tt ORDER BY g, id"
        )->execute();
    }
    PDOS::Instance()->commit();
} catch (Exception $e) {
    PDOS::Instance()->rollback();
    throw $e;
}

exit();
