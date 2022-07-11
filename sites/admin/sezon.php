<?php
is_logged();

function page_init()
{
    return PDOS::Instance()->cmd("get_seasons()")->fetchAll(PDO::FETCH_ASSOC);
}

function page_render($obj)
{
    ?>
    <script type="text/javascript">
        function display() {
            let s = document.getElementById('wybor_sezonu');
            let ss = s[s.selectedIndex].text;
            document.getElementById('wyswietl_sezon').innerText = `Sezon: ${ss}`;
        }
    </script>
    <div id="content">
        <h1> TWORZENIE SEZONU </h1>
        <div class="error">Funkcja nie została zaimplementowana.</div>
        <?php SERVER_ERROR(501); ?>
        <form action="">
            <label for="wybor_sezonu">Wybierz sezon: </label>
            <select name="sezon" id="wybor_sezonu" onchange="display();">
                <?php foreach ($obj as $sezon) : ?>
                    <option value='<?= $sezon['season_id'] ?>'>
                        <?= $sezon['html_name'] ?>
                    </option>
                <?php endforeach; ?>
            </select></form>
        <br>
        <div id="desc">
            <p id="wyswietl_sezon">Sezon: 2021/2022</p>
            <form action="#" method="POST">
                <div>
                    <label for="htmlnazwa">Nazwa w html: </label><br>
                    <textarea id="htmlnazwa" name="htmlnazwa" rows="2" cols="30" style="text-align: left;">html nazwa: <&span style="color:grey;">2019/2020
            </textarea>
                </div>

                <div style="float:left;">
                    <label for="Opis">Opis: </label><br>
                    <textarea id="Opis" name="Opis" rows="5" cols="30" style="text-align: left;">Ten sezon nie istnieje.
            </textarea>
                </div>
                <div style="margin-left: 60%;">
                    <input type="submit" value="Aktualizuj" style="width: 60%; height: 100px;">
                </div>
                <div style="clear:both"></div>
                <div>
                    <label for="format">Format rozgrywek: </label>
                    <select name="Format_rozgrywek" id="format">
                        <option value="groups">Dwie grupy</option>
                        <option value="doublerobin">Double-robin</option>
                        <option value="null">null</option>
                    </select>
                </div>
        </div>
        </form>
        <div class="update"><a class="tile" href="/admin/harmonogram">Zmień harmonogram</a></div> <!-- TODO: /sezonId -->
        <div class="update"><a class="tile" href="/admin/wyniki">Zmień wyniki</a></div> <!-- TODO: /sezonId -->
        <div class="update">Dodaj zdjęcia</div>
        <div class="update">Dodaj nowy artykuł</div>
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
