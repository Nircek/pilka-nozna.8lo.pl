<?php

include(ROOT_PATH . "/funkcje/funkcje_admin.php");
is_logged();

include(ROOT_PATH . "/funkcje/db-connect.php");

$sezon = $_POST['sezon'];
$sezon_final = "${sezon}_final";
$sezon_terminarz = "${sezon}_terminarz";

if (isset($_POST['final_ilosc'])) {
    for ($y = 1; $y <= 4; $y++) {
        $termin = $_POST['f_' . $y];
        try {
            $pdo->exec("UPDATE `$sezon_final` SET `termin` = '$termin' WHERE `id` = '$y'");
        } catch (PDOException $e) {
            $_SESSION['e_harmonogram_baza'] = "Błąd bazy danych: $e";
            header('Location: ../admin_harmonogram.php');
            exit();
        }
    }
} elseif (isset($_POST['grupa_ilosc'])) {
    $ilosc = $_POST['grupa_ilosc'];
    for ($y = 1; $y <= $ilosc; $y++) {
        $termin = $_POST[$y];
        try {
            $pdo->exec("UPDATE `$sezon_terminarz` SET `termin` = '$termin' WHERE `id` = '$y'");
        } catch (PDOException $e) {
            $_SESSION['e_harmonogram_baza'] = "Błąd bazy danych: $e";
            header('Location: ../admin_harmonogram.php');
            exit();
        }
    }
}

header('Location: ../admin_harmonogram.php');
exit();
