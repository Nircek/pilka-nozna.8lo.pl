<?php

is_logged();
function page_init()
{
    return PDOS::Instance()->cmd("get_seasons()")->fetchAll(PDO::FETCH_ASSOC);
}

function page_render($obj)
{
    ?>
    <div id="sezony" class="wybierz">
        <h1> WYBIERZ SEZON </h1>
        <?php foreach ($obj as $sezon) : ?>
            <div class='sezon'>
                <a href='<?= PREFIX ?>/sezony/<?= $sezon['season_id'] ?>' class="sezon_link">
                    <?= $sezon['html_name'] ?>
                </a>
            </div>
        <?php endforeach; ?>
        <div class="sezon link">
            <a href="http://www.pilka-nozna.8lo.pl/archiwum/" target="_blank" style="color: chartreuse;">
                ARCHIWUM
            </a>
        </div>
        <div style="clear: both;"></div>
    </div>
<?php
}
