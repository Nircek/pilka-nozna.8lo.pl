<?php
session_start();
require_once("utils.php");
include_once('./funkcje/funkcje_admin.php');
is_logged();

// Check if step exists
if (!isset($_SESSION['krok'])) {
    $_SESSION['krok'] = 1;
    $krok = $_SESSION['krok'];
} elseif ($_SESSION['krok'] == 1 or $_SESSION['krok'] == 2 or $_SESSION['krok'] == 3) {
    $krok = $_SESSION['krok'];
} else {
    header('Location: admin.php');
    exit();
}
?>
<?php generate_header("admin,admin_sezon"); ?>

<div id="content">
    <h1> TWORZENIE SEZONU - KROK <?= $krok; ?> </h1>

    <div id="panel">
        <?php
        // Loading same script as step of creating next season
        include('./skrypty/sezon_krok' . $krok . '.php');
        ?>
    </div>
</div>

<?php generate_footer(); ?>
