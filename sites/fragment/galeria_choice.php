<?php
register_additional_title("Wybierz");

function page_init()
{
    return PDOS::Instance()->query("SELECT DISTINCT sezon FROM zdjecia ORDER BY sezon DESC")->fetchAll(PDO::FETCH_ASSOC);
}

function page_render($obj)
{
?>
    <div id="content">
        <h1> GALERIA </h1>
        <?php foreach ($obj as $sezon) : ?>
            <div class='sezon'>
                <a href='<?= PREFIX ?>/galeria/<?= $sezon['sezon'] ?>'>
                    <?= $sezon['sezon'] ?>/<?= $sezon['sezon'] + 1 ?>
                </a>
            </div>
        <?php endforeach; ?>
        <div style="clear: both;"></div>
    </div>

<?php }
