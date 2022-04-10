<?php
register_additional_title("Wybierz sezon");

function page_init()
{
    return PDOS::Instance()->query("SELECT sezon FROM sezony ORDER BY id DESC")->fetchAll(PDO::FETCH_COLUMN);
}

function page_render($obj)
{
?>
    <div id='content' class="wybierz">
        <h1> WYBIERZ SEZON </h1>
        <?php foreach ($obj as $sezon) : ?>
            <div class='sezon'>
                <a href='<?= PREFIX ?>/sezony/<?= $sezon ?>'>
                    <?= $sezon ?>/<?= $sezon + 1 ?>
                </a>
            </div>
        <?php endforeach; ?>

        <div class='sezon'>
            <a href='http://www.pilka-nozna.8lo.pl/archiwum/' target='_blank'>
                ARCHIWUM
            </a>
        </div>
        <div style='clear: both;'></div>
    </div>
<?php }
