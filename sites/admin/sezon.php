<?php
register_style("admin_sezon");
is_logged();

if (!isset($_SESSION['sezon_krok']) or !in_array($_SESSION['sezon_krok'], array(1, 2, 3))) {
    $_SESSION['sezon_krok'] = 1;
}
$krok = $_SESSION['sezon_krok'];
require(ROOT_PATH . "/sites/fragment/sezon_krok$krok.php");

function page_render()
{
?>
    <div id="content">
        <h1> TWORZENIE SEZONU - KROK <?= $_SESSION['sezon_krok'] ?> </h1>

        <div id="panel">
            <?php page_krok_render(); ?>
        </div>
    </div>
<?php }
