<?php
register_style("galeria");
register_title("Galeria");

$param = HIT_UNPACK();
if ($param === null) $param = "choice";
if ($param === "choice") require(ROOT_PATH . "/sites/fragment/galeria_choice.php");
else {
    HIT_PACK($param);
    require(ROOT_PATH . "/sites/fragment/galeria_one.php");
}
