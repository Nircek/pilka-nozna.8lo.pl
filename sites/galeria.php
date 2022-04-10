<?php
register_style("galeria");
register_title("Galeria");

$param = HIT_UNPACK();
if ($param === null) $param = "choice";
if ($param === "najnowsza") {
    $obecny = PDOS::Instance()->query("SELECT sezon FROM zdjecia ORDER BY sezon DESC LIMIT 1")->fetchAll(PDO::FETCH_COLUMN);
    $param = empty($obecny) ? "choice" : $obecny[0];
}
if ($param === "choice") require(ROOT_PATH . "/sites/fragment/galeria_choice.php");
else {
    HIT_PACK($param);
    require(ROOT_PATH . "/sites/fragment/galeria_one.php");
}
