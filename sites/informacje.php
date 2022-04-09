<?php generate_header("informacje"); ?>

<div id="content">
    <h1> INFORMACJE </h1>
    <?php
    try {
        $result = PDOS::Instance()->query("SELECT * FROM informacje ORDER BY id DESC");
    } catch (PDOException $e) {
        reportError("db", $e->getMessage());
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
