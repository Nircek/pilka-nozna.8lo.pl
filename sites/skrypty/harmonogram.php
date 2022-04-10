<?php
is_logged();
define("HARMONOGRAM_URL", PREFIX . "/admin/harmonogram");
header('Location: ' . HARMONOGRAM_URL);

$sezon = cast_int($_POST['sezon']);
if (is_null($sezon)) {
    report_error("sezon violation", NULL);
    exit();
}

$sezon_terminarz = "${sezon}_terminarz";
$sezon_final = "${sezon}_final";
if (sprawdzanie_tabela($sezon_final)) {
    $final = PDOS::Instance()->query("SELECT id FROM `$sezon_final`")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($final as $id) {
        $termin = $_POST['f' . $id];
        PDOS::Instance()->prepare("UPDATE `$sezon_final` SET termin = ? WHERE id = ?")->execute([$termin, $id]);
    }
}
$ids = PDOS::Instance()->query("SELECT id FROM `$sezon_terminarz`")->fetchAll(PDO::FETCH_COLUMN);
foreach ($ids as $id) {
    $termin = $_POST[$id];
    PDOS::Instance()->prepare("UPDATE `$sezon_terminarz` SET `termin` = ? WHERE id = ?")->execute([$termin, $id]);
}

exit();
