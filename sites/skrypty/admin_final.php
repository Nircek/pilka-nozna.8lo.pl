<?php
is_logged();

// Pobieranie obecnego sezonu
$sezon = obecny_sezon();
$sezon_tabela = "${sezon}_tabela";
$sezon_final = "${sezon}_final";

// Sprawdzanie czy już przypadkiem nie istnieje...
if (sprawdzanie_tabela($sezon_final)) {
    header('Location: ' . PANEL_URL);
    report_error("Finał obecnego sezonu już istnieje!", NULL);
    exit();
}
$stmt = PDOS::Instance()->prepare(
    "CREATE TABLE `$sezon_final` (
        `id` int NOT NULL AUTO_INCREMENT,
        `druzyna_1` text,
        `druzyna_2` text,
        `wynik_1` int,
        `wynik_2` int,
        `termin` date null,
        `poziom` int null,
        PRIMARY KEY(id)
    )"
)->execute();

// =================== POBIERANIE DRUŻYN ===================

$grupa_1 = PDOS::Instance()->query(
    "SELECT nazwa FROM $sezon_tabela WHERE grupa=1 ORDER BY pkt DESC, (zdobyte - stracone) DESC, nazwa ASC LIMIT 2"
)->fetchAll(PDO::FETCH_COLUMN);
$grupa_2 = PDOS::Instance()->query(
    "SELECT nazwa FROM $sezon_tabela WHERE grupa=2 ORDER BY pkt DESC, (zdobyte - stracone) DESC, nazwa ASC LIMIT 2 "
)->fetchAll(PDO::FETCH_COLUMN);

if (count($grupa_1) + count($grupa_2) < 4) {
    header('Location: ' . PANEL_URL);
    report_error("Za mało drużyn by stworzyć rundę finałową", NULL);
    exit();
}

// =================== USTALANIE MECZY ===================



// =================== WSTAWIANIE DO BAZY ===================

$insert_stmt = PDOS::Instance()->prepare(
    "INSERT INTO `$sezon_final` (`druzyna_1`, `druzyna_2`, `poziom`) VALUES (?, ?, 2), (?, ?, 2), (NULL, NULL, 3), (NULL, NULL, 1)"
);

$insert_stmt->execute([$grupa_1[0], $grupa_2[1], $grupa_2[0], $grupa_1[1]]);

header('Location: ' . PANEL_URL);
exit();
