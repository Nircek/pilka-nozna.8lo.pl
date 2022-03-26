<?php
date_default_timezone_set("Europe/Warsaw");
$rok = date('Y');
?>
<div id="footer">
    <div id="left-footer">
        <ol>
            <li><a href="informacje"> INFORMACJE </a></li>
            <li><a href="o-nas"> O NAS </a></li>
            <li><a href="kontakt"> KONTAKT </a></li>
            <li><a href="regulamin"> REGULAMIN </a></li>
        </ol>
    </div>
    <div id="center-footer">
        <div id="top-center-footer"></div>
        <div id="bottom-center-footer">
            &copy <?= $rok; ?>
        </div>
    </div>
    <div id="right-footer">
        <ol>
            <li>
                <a href="sezony.php?s=<?= /* tą zmienną deklaruje menu.php */ $obecny_sezon ?>">
                    OBECNY SEZON
                </a>
            </li>
            <li>
                <a href="sezony.php">
                    WSZYSTKIE SEZONY
                </a>
            </li>
        </ol>
    </div>
    <div style="clear: both;"></div>
</div>
