<?php
register_additional_title("Wybierz");

function page_init()
{
    return PDOS::Instance()->query( // gallery_seasons()
        "SELECT
            p.`season_id`, s.`name`, s.`html_name`
        FROM `ng_photo` p
            LEFT JOIN `ng_season` s ON p.`season_id` = s.`season_id`
        GROUP BY `season_id` ORDER BY s.`created_at` DESC"
    )->fetchAll(PDO::FETCH_ASSOC);
}

function page_render($obj)
{
?>
    <div id="content">
        <h1> GALERIA </h1>
        <?php foreach ($obj as $sezon) : ?>
            <div class='sezon'>
                <a href='<?= PREFIX ?>/galeria/<?= $sezon['season_id'] ?>'>
                    <?= $sezon['html_name'] ?>
                </a>
            </div>
        <?php endforeach; ?>
        <div style="clear: both;"></div>
    </div>

<?php }
