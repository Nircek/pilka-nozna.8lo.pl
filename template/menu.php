<?php

$error_queue = pop_reports();
foreach ($error_queue as $error) :
?>
    <div class="error"><?= $error[0] ?>
        <?php if ($error[1] !== null) : ?>
            <br><?= ERROR_REPORTING ? var_export($error[1]) : false ?><br><?= ERROR_REPORTING ? $error[2] : false ?>
        <?php endif; ?>
    </div>
<?php endforeach; ?>
<?php if (!is_null(MOTD)) : ?>
    <div class="warn">
        <?= MOTD ?>
    </div>
<?php endif; ?>
<?php if (is_logged(false)) : ?>
    <div id='zalogowany' style='font-weight: bold; padding: 5px 0; margin: auto; text-align: center; background-color: #22c12d; width: 1000px; font-size: 25px;'>
        ADMIN ZALOGOWANY | <a href='<?= PREFIX ?>/skrypty/logout' class="standard"> WYLOGUJ </a> | <a href='<?= PREFIX ?>/admin' class="standard"> PANEL ADMINA </a>
    </div>
<?php endif; ?>
<div id="menu">
    <div id="logo" class="left-drawer-top">
        <a href="<?= PREFIX ?>/"><img src="<?= PREFIX ?>/img/logo.png"></a>
    </div>
    <div id="title" class="center-drawer-top">
        <div class="center-vert"> VIII LO "PIK" PIŁKA NOŻNA </div>
    </div>
    <div id="options" class="right-drawer-top link">
        <div id="facebook">
            <a target="_blank" href="#">
                <i class="icon-facebook"></i>
            </a>
        </div>
        <div id="pik">
            <a target="_blank" href="http://8lo.pl/">
                <i class="icon-graduation-cap"></i>
            </a>
        </div>
        <div id="galeria">
            <a href="<?= PREFIX ?>/galeria">
                <i class="icon-camera"></i>
            </a>
        </div>
        <div style="clear: both"></div>
        <div id="obecny-sezon">
            <a href="<?= PREFIX ?>/sezony/obecny">
                <div class="center-vert">
                    OBECNY<br>SEZON
                </div>
            </a>
        </div>
        <div id="wszystkie-sezony">
            <a href="<?= PREFIX ?>/sezony">
                <div class="center-vert">
                    WSZYSTKIE<br>SEZONY
                </div>
            </a>
        </div>
        <div style="clear: both;"></div>
    </div>
    <div style="clear: both;"></div>
</div>
