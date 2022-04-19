<?php
register_additional_title("Wybierz sezon");

function page_init()
{
    return PDOS::Instance()->query("SELECT `season_id`, `name`, `html_name` FROM `ng_season` ORDER BY `created_at` DESC;")->fetchAll(PDO::FETCH_ASSOC);
}

function page_render($obj)
{
?>
    <div id='content' class="wybierz">
        <h1> WYBIERZ SEZON </h1>
        <?php foreach ($obj as $sezon) : ?>
            <div class='sezon'>
                <a href='<?= PREFIX ?>/sezony/<?= $sezon['season_id'] ?>'>
                    <?= $sezon['html_name'] ?>
                </a>
            </div>
        <?php endforeach; ?>

        <div class='sezon'>
            <a href='http://www.pilka-nozna.8lo.pl/archiwum/' target='_blank' style="color: chartreuse;">
                ARCHIWUM
            </a>
        </div>
        <div style='clear: both;'></div>
    </div>
<?php }
