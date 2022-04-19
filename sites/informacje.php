<?php
register_style("informacje");
register_title("Informacje");

function page_init()
{
    return PDOS::Instance()->query(
        "SELECT
            `article_id`, `title`, `content`, `created_at`
        FROM `ng_article` WHERE `publish_on_news_page` = 1
        ORDER BY `article_id` DESC;"
    )->fetchAll(PDO::FETCH_ASSOC);
}

function page_render($obj)
{
?>
    <div id="content">
        <h1> INFORMACJE </h1>
        <?php foreach ($obj as $info) : ?>
            <div class='info'>
                <span id='tytul'>
                    <?= $info['title'] ?>
                </span>
                <br />
                <span id='tresc'>
                    <?= $info['content'] ?>
                </span>
                <br />
                <span id='data'>
                    <?= $info['created_at'] ?>
                </span>
            </div>
        <?php endforeach; ?>
    </div>
<?php }
