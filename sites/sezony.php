<?php

register_style("sezony");
register_title("Sezony");
$param = HIT_UNPACK();
if ($param === null) {
    $param = "choice";
}
if ($param === "obecny") {
    $obecny = obecny_sezon();
    $param = empty($obecny) ? "choice" : $obecny;
}
if ($param === "choice") {
    require(ROOT_PATH . "/sites/fragment/sezony_choice.php");
} else {
    HIT_PACK($param);
    require(ROOT_PATH . "/sites/fragment/sezony_one.php");
}
