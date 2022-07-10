<?php
register_style("admin.css");
is_logged();

function page_init()
{
    return PDOS::Instance()->cmd("get_seasons()")->fetchAll(PDO::FETCH_ASSOC);
}

function page_render($obj)
{
?>
    <div id="panel">
        <h1> PANEL ADMINISTRATORA </h1>
        <label for="wybor_zezonu">Wybierz sezon: </label>
        <select name="sezon" id="wybor_sezonu">
        <?php foreach ($obj as $sezon) : ?>
            <option value='<?= $sezon['season_id'] ?>'>
                    <?= $sezon['html_name'] ?>
            </option>
        <?php endforeach; ?>
        </select>
        <br>
        <div id="desc">
            <p>Sezon: 2019/2020</p>
            <p>html nazwa: <&span style="color: grey;">2019/2020</p>
            <p>Opis: Ten sezon jeszcze nie istnieje</p>
            <p>Format rozgrywek: Dwie grupy</p>
        </div>
        <div class="update"><a class="tile" href="/admin/harmonogram">Zmień harmonogram</a></div> <!-- TODO: /sezonId -->
        <div class="update"><a class="tile" href="/admin/wyniki">Zmień wyniki</a></div> <!-- TODO: /sezonId -->
        <div class="update">Dodaj zdjęcia</div> <!-- TODO: popping-up window? -->
        <div class="update">Dodaj nowy artykuł</div> <!-- TODO: popping-up window? -->
        <div class="group">
            <h2>GRUPA A</h2>
            1d <br>
            2d <br>
            3d <br>
            4d <br>
            <br>
            <br>
            <form method="post" action="">
                <input type="text" name="drużyna">
                <br>
                <input type="submit" value="DODAJ DUŻYNĘ!">
            </form>
            <br>
        </div>
        <div class="group">
            <h2>GRUPA B</h2>
            1dd <br>
            2dd <br>
            3dd <br>
            4dd <br>
            <br>
            <br>
            <form method="post" action="index.php">
                <input type="text" name="drużyna">
                <br>
                <input type="submit" value="DODAJ DUŻYNĘ!">
            </form>
            <br>
        </div>
    </div>
<?php
}
