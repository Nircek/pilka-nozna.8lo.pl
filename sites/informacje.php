<?php
register_style("informacje");
register_title("Informacje");

function page_init()
{
    return PDOS::Instance()->query("SELECT * FROM informacje ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
}

function page_render($obj)
{
?>
    <div id="content">
        <h1> INFORMACJE </h1>
        <?php foreach ($obj as $info) : ?>
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
<?php }
