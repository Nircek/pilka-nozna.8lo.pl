<?php

is_logged();

$sezon = HIT_UNPACK();
$sezon =  cast_int($sezon);
$sezon = $sezon === null ? obecny_sezon() : $sezon;

$return_url = preg_match('/[a-zA-Z0-9]+/', $_GET['return_url']) ? $_GET['return_url'] : PANEL_URL;

$grouping = PDOS::Instance()->cmd(
    "get_season(season)",
    [$sezon]
)->fetch(PDO::FETCH_ASSOC)['grouping_type'];
if ($grouping !== "two_groups") {
    header("Location: " . $return_url);
    report_error("Tylko grupowanie `two_groups` pozwala na utworzenie rundy finałowej.", null);
    exit();
}

PDOS::Instance()->cmd("delete_finals(season)", [$sezon]);

$grupa_1 = PDOS::Instance()->cmd(
    "get_group_table(season, all?, group)",
    [$sezon, false, 'first']
)->fetchAll(PDO::FETCH_COLUMN);
$grupa_2 = PDOS::Instance()->cmd(
    "get_group_table(season, all?, group)",
    [$sezon, false, 'second']
)->fetchAll(PDO::FETCH_COLUMN);

array_splice($grupa_1, 2);
array_splice($grupa_2, 2);


if (count($grupa_1) + count($grupa_2) < 4) {
    header('Location: ' . $return_url);
    report_error("Za mało drużyn by stworzyć rundę finałową", null);
    exit();
}

$insert_stmt = PDOS::Instance()->cmd(
    "add_finals(season, f1, s1, f2, s2)",
    [$sezon, $grupa_1[0], $grupa_2[1], $grupa_2[0], $grupa_1[1]]
);

header('Location: ' . $return_url);
exit();
