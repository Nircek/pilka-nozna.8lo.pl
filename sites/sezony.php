<?php
register_style("sezony");
register_title("Sezony");
$param = HIT_UNPACK();
if ($param === null) $param = "choice";
if ($param === "obecny") {
    $obecny = PDOS::Instance()->query("SELECT sezon FROM sezony ORDER BY sezon DESC LIMIT 1")->fetchAll(PDO::FETCH_COLUMN);
    $param = empty($obecny) ? "choice" : $obecny[0];
}
if ($param === "choice") require(ROOT_PATH . "/sites/fragment/sezony_choice.php");
else {
    HIT_PACK($param);
    require(ROOT_PATH . "/sites/fragment/sezony_one.php");
}
