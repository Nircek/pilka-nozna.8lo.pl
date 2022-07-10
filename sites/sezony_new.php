<?php
is_logged();
register_style("index");
// TODO: Move to sites and switch to sezony.css or index.css
function page_init()
{
    return PDOS::Instance()->cmd("get_seasons()")->fetchAll(PDO::FETCH_ASSOC);
}

function page_render($obj)
{
    ?>
    <div id="sezony" class="wybierz">
        <h1> WYBIERZ SEZON DO EDYCJI</h1>
        <?php foreach ($obj as $sezon) : ?>
            <div class='sezon'>
                <a href='<?= PREFIX ?>/admin/<?= $sezon['season_id'] ?>' class="sezon_link">
                    <?= $sezon['html_name'] ?>
                </a>
            </div>
        <?php endforeach; ?>

        <div class="sezon">
            <a href="http://www.pilka-nozna.8lo.pl/admin/nowy_sezon" style="color: chartreuse; text-decoration: none;">
                DODAJ NOWY SEZON
            </a>
        </div>
        <!-- <br>
        <div style="clear: both;"></div> -->
    </div>
<?php
}
