<?php
date_default_timezone_set("Europe/Warsaw");
$rok = date('Y');
?>
<div id="footer">
    <div id="left-footer">
        <ol>
            <li><a href="<?= PREFIX ?>/informacje"> INFORMACJE </a></li>
            <li><a href="<?= PREFIX ?>/o-nas"> O NAS </a></li>
            <li><a href="<?= PREFIX ?>/kontakt"> KONTAKT </a></li>
            <li><a href="<?= PREFIX ?>/regulamin"> REGULAMIN </a></li>
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
                <a href="<?= PREFIX ?>/sezony?s=<?= /* provided by menu.php */ $obecny_sezon ?>">
                    OBECNY SEZON
                </a>
            </li>
            <li>
                <a href="<?= PREFIX ?>/sezony">
                    WSZYSTKIE SEZONY
                </a>
            </li>
        </ol>
    </div>
    <div style="clear: both;"></div>
</div>
