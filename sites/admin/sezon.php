<?php
is_logged();

global $sezon;
$sezon = HIT_UNPACK();
$sezon =  cast_int($sezon);
if ($sezon === null) {
    header("Location: " . PANEL_URL . "/sezon/" . obecny_sezon());
    exit();
}

function page_perform()
{
    global $sezon;
    PDOS::Instance()->cmd(
        "update_season(name, html_name, description, id)",
        [$_POST['name'], $_POST['html_name'], $_POST['description'], $sezon]
    );
}

function page_init()
{
    global $sezon;
    return array(
        'sezon' =>  PDOS::Instance()->cmd("get_season(season)", [$sezon])->fetchAll(PDO::FETCH_ASSOC)[0],
        'sezony' => PDOS::Instance()->cmd("get_seasons()")->fetchAll(PDO::FETCH_ASSOC)
    );
}

function page_render($obj)
{ ?>
    <script type="text/javascript">
        window.onload = () => {
            document.getElementById('go_season').onclick = () => window.location.href = document.getElementById('season').value;
        }
    </script>
    <div id="content">
        <h1> EDYCJA SEZONU </h1>
        <label for="season">Wybierz sezon: </label>
        <select name="season" id="season">
            <option value='../nowy_sezon'>Dodaj nowy...</option>
            <?php foreach ($obj['sezony'] as $sezon) : ?>
                <option value='<?= $sezon['season_id'] ?>' <?= $sezon['season_id'] == $obj['sezon']['season_id'] ? ' selected' : '' ?>>
                    <?= htmlentities($sezon['name']) ?>
                </option>
            <?php endforeach; ?>
            <option value='../nowy_sezon'>Dodaj nowy...</option>
        </select>
        <button id="go_season">Idź</button>
        <br>
        <div id="desc">
            <form method="POST" style="display:flex;">
                <div style="flex:4;">
                    <p>id: <?= $obj['sezon']['season_id'] ?></p>
                    <label for="name">Nazwa: </label><br>
                    <input type="text" name="name" id="name" value="<?= htmlentities($obj['sezon']['name']) ?>" /><br>
                    <label for="html_name">Nazwa w html: </label><br>
                    <textarea id="html_name" name="html_name" rows="2" cols="30" style="text-align: left;"><?= htmlentities($obj['sezon']['html_name']) ?></textarea><br>
                    <label for="description">description: </label><br>
                    <textarea id="description" name="description" rows="5" cols="30" style="text-align: left;"><?= htmlentities($obj['sezon']['description']) ?></textarea><br>
                    <p>Schemat grupowania: <?= htmlentities($obj['sezon']['grouping_type']) ?> <?php if ($obj['sezon']['grouping_type'] === "no_grouping") : ?>
                            <a href="../zmien/<?= $obj['sezon']['season_id'] ?>">zmień</a>
                        <?php endif; ?>
                    </p>
                </div>
                <div style="flex:3;">
                    <div style="height:40%;"></div>
                    <input type="hidden" name="type" value="edit" />
                    <input type="submit" value="Aktualizuj" style="height:20%; width:70%;">
                </div>
                <div style="clear:both;"></div>
        </div>
        </form>
        <div class="update"><a class="tile" href="/admin/harmonogram/<?= $obj['sezon']['season_id'] ?>">Zmień harmonogram</a></div>
        <div class="update"><a class="tile" href="/admin/wyniki/<?= $obj['sezon']['season_id'] ?>">Zmień wyniki</a></div>
        <div class="update"><a class="tile" href="/admin/zdjecia/<?= $obj['sezon']['season_id'] ?>"><s>Zarządzaj zdjęciami</s></a></div>
        <div class="update"><a class="tile" href="/admin/artykuly/<?= $obj['sezon']['season_id'] ?>"><s>Zarządzaj artykułami</s></a></div>
        <div class="group">
            <h2>GRUPA 1</h2>
            1d <br>
            2d <br>
            3d <br>
            4d <br>
            <br>
            <br>
            <form method="post" action="#">
                <input type="text" name="drużyna">
                <br>
                <input type="submit" value="DODAJ DUŻYNĘ!">
            </form>
            <br>
        </div>
        <div class="group">
            <h2>GRUPA 2</h2>
            1dd <br>
            2dd <br>
            3dd <br>
            4dd <br>
            <br>
            <br>
            <form method="post" action="#">
                <input type="text" name="drużyna">
                <br>
                <input type="submit" value="DODAJ DUŻYNĘ!">
            </form>
            <br>
        </div>
    </div>
<?php
}
