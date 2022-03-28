<?php
session_start();
require_once("utils.php");
?>
<?php generate_header("o_nas"); ?>

<div id="content">
    <h1> INFORMACJE </h1>
    <?php
    include("./skrypty/db-connect.php");

    try {
        $result = $pdo->query("SELECT * FROM informacje ORDER BY id DESC");
    } catch (PDOException $e) {
        echo "Błąd bazy danych: $e";
    }
    $info = array();
    while ($row = $result->fetch()) {
        $info[] = array(
            'id' => $row['id'],
            'tytul' => $row['tytul'],
            'tresc' => $row['tresc'],
            'data' => $row['data']
        );
    }

    foreach ($info as $info) :
    ?>
        <div class='info'>
            <span id='tytul'>
                <?= $info['tytul'] ?>
            </span>
            <br />
            <span id='tresc'>
                <?= $info['tresc'] ?>
            </span>
            <br />
            <span id='data'>
                <?= $info['data'] ?>
            </span>
        </div>
    <?php endforeach; ?>
</div>

<?php generate_footer(); ?>
