<?php
register_additional_title("Wybierz");

function page_init()
{
    return PDOS::Instance()->cmd("get_gallery_seasons()")->fetchAll(PDO::FETCH_ASSOC);
}

function page_render($obj)
{
?>
    <div id="content">
        <h1> GALERIA </h1>
        <?php foreach ($obj as $sezon) : ?>
            <div class="sezon link">
                <a href="<?= PREFIX ?>/galeria/<?= $sezon['season_id'] ?>">
                    <?= $sezon['html_name'] ?>
                </a>
            </div>
        <?php endforeach; ?>
        <div style="clear: both;"></div>
    </div>

<?php }
