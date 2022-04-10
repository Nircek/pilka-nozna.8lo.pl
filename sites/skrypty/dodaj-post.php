<?php
is_logged();
header('Location: ' . PANEL_URL);

$tytul = $_POST['info_tytul'];
$tresc = $_POST['info_tresc'];
if (empty($tytul) or empty($tresc)) {
    report_error("Oba pola muszą być wypełnione!", NULL);
    exit();
}
PDOS::Instance()->prepare("INSERT INTO `informacje` (`tytul`, `tresc`, `data`) VALUES (?, ?, CURDATE())")->execute([$tytul, $tresc]);
exit();
