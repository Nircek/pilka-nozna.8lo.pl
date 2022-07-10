<?php
register_style("informacje");
register_title("Informacje");

function page_init()
{
    return PDOS::Instance()->cmd("get_news()")->fetchAll(PDO::FETCH_ASSOC);
}

function page_render($obj)
{
?>
    <div id="content">
        <h1> INFORMACJE </h1>
        <?php foreach ($obj as $info) : ?>
            <div class="info">
                <div class="informacja-tytul"> <?= $info['title'] ?> </div>
                <div class="informacja-tresc"> <?= $info['content'] ?> </div>
                <div class="informacja-data"> <?= $info['created_at'] ?> </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php }
