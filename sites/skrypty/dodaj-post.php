<?php
is_logged();
$autor = 0;
header('Location: ' . PANEL_URL);

$tytul = $_POST['info_tytul'];
$tresc = $_POST['info_tresc'];
if (empty($tytul) or empty($tresc)) {
    report_error("Oba pola muszą być wypełnione!", NULL);
    exit();
}
PDOS::Instance()->cmd(
    "add_info(title, author, content)",
    [$tytul, $autor, $tresc]
);
exit();
