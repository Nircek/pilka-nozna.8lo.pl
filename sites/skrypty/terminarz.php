<?php

include(ROOT_PATH . "/funkcje/funkcje_admin.php");
is_logged();
$sezon = $_POST['sezon'];
$sezon_final = "${sezon}_final";
$sezon_terminarz = "${sezon}_terminarz";

if (isset($_POST['final_ilosc'])) {
    for ($y = 1; $y <= 4; $y++) {
        $termin = $_POST['f_' . $y];
        try {
            PDOS::Instance()->exec("UPDATE `$sezon_final` SET `termin` = '$termin' WHERE `id` = '$y'");
        } catch (PDOException $e) {
            reportError("db", $e->getMessage());
        }
    }
} elseif (isset($_POST['grupa_ilosc'])) {
    $ilosc = $_POST['grupa_ilosc'];
    for ($y = 1; $y <= $ilosc; $y++) {
        $termin = $_POST[$y];
        try {
            PDOS::Instance()->exec("UPDATE `$sezon_terminarz` SET `termin` = '$termin' WHERE `id` = '$y'");
        } catch (PDOException $e) {
            reportError("db", $e->getMessage());
        }
    }
}

header('Location: ../admin_harmonogram.php');
exit();
