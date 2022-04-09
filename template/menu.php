<?php
$error_queue = popReports();
foreach ($error_queue as $error) :
?>
    <div class="error"><?= $error[0] ?>
        <?php if ($error[1] !== null) : ?>
            <br><?= ERROR_REPORTING ? var_export($error[1]) : false ?><br><?= ERROR_REPORTING ? $error[2] : false ?>
        <?php endif; ?>
    </div>
<?php endforeach; ?>
<?php
// Nawiązywanie połączenia z bazą
try {
    $sql = "SELECT * FROM sezony ORDER BY sezon DESC LIMIT 1";
    $result = PDOS::Instance()->query($sql);
    $liczba = $result->rowCount();
} catch (PDOException $e) {
    reportError("db", $e->getMessage());
}
if ($liczba === 1) {
    // TODO: refeactor
    while ($row = $result->fetch()) {
        $obecny_sezon[] = array('sezon' => $row['sezon']);
    }
    foreach ($obecny_sezon as $obecny_sezon) {
        $obecny_sezon = $obecny_sezon['sezon'];
        $obecny_sezon = $obecny_sezon . "/" . ($obecny_sezon + 1);
    }
}
?>
<?php if (isset($_SESSION['zalogowany'])) : ?>
    <div id='zalogowany' style='font-weight: bold; padding: 5px 0; margin: auto; text-align: center; background-color: #22c12d; width: 1000px; font-size: 25px;'>
        ADMIN ZALOGOWANY | <a href='<?= PREFIX ?>/skrypty/logout'> WYLOGUJ </a> | <a href='<?= PREFIX ?>/admin'> PANEL ADMINA </a>
    </div>
<?php endif; ?>
<div id="menu">
    <div id="logo">
        <a href="<?= PREFIX ?>/"><img src="<?= PREFIX ?>/img/logo.png" height="170" style="margin-top: 5px;"></a>
    </div>
    <div id="title">
        <div id="title-content">
            VIII LO "PIK"
            PIŁKA NOŻNA
        </div>
    </div>
    <div id="options">
        <div id="top-options">
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
        </div>
        <div id="bottom-options">
            <div id="obecny-sezon">
                <a <?php if ($liczba === 1) : ?> href="<?= PREFIX ?>/sezony?s=<?= $obecny_sezon ?>" <?php endif; ?>>
                    OBECNY SEZON
                </a>
            </div>
            <div id="wszystkie-sezony">
                <a href="<?= PREFIX ?>/sezony">
                    WSZYSTKIE SEZONY
                </a>
            </div>
            <div style="clear: both;"></div>
        </div>
    </div>
    <div style="clear: both;"></div>
</div>
