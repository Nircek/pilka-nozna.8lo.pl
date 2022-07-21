<?php
is_logged();
register_style("admin");

function page_init()
{
    return PDOS::Instance()->cmd("get_seasons()")->fetchAll(PDO::FETCH_ASSOC);
}

function page_render($obj)
{
    ?>
    <div id="content">
        <h1> PANEL ADMINISTRATORA </h1>
        <div id="panel">
            <!------------------ DODAWANIE INFO ------------------>
            <div id="informacje">
                <h2> DODAJ INFORMACJĘ </h2>
                <form method="post" action="<?= PREFIX ?>/skrypty/dodaj-post" id="informacje-form">
                    <p class="big"> TYTUŁ </p>
                    <textarea cols="24" rows="2" id="info_tytul" maxlength="50" name="info_tytul"></textarea><br />
                    <p class="big"> TREŚĆ </p>
                    <textarea cols="27" rows="10" id="info_tresc" name="info_tresc"></textarea><br />
                    <input type="submit" value="PUBLIKUJ">
                </form>
            </div>

            <!------------------ WPISYWANIE WYNIKÓW ------------------>
            <div id="aktualizacja-sezonu">
                <h2> AKTUALIZUJ SEZON </h2>
                <p class="big"> WPISZ WYNIKI </p>
                <div id="dalej-button" class="link">
                    <a href="<?= PREFIX ?>/admin/wyniki">+</a>
                </div>
                <p class="big"> AKTUALIZUJ HARMONOGRAM </p>
                <div id="dalej-button" class="link">
                    <a href="<?= PREFIX ?>/admin/harmonogram">+</a>
                </div>
            </div>
            <div style="clear: both;"></div>

            <!------------------ DODAWANIE ZDJĘĆ ------------------>
            <div id="zdjecia">
                <h2> DODAJ ZDJĘCIA </h2>
                <form method="post" enctype="multipart/form-data" action="<?= PREFIX ?>/skrypty/dodaj-zdjecie">
                    <p class="big"> WYBIERZ SEZON: </p>
                    <select id="zdjecia_sezon" name="zdjecie_sezon">
                        <?php foreach ($obj as $sezon) : ?>
                            <option value="<?= $sezon['season_id'] ?>"><?= $sezon['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                    <p class="big"> WYBIERZ ZDJĘCIA (.jpg) </p>
                    <input type="file" name="files[]" id="zdjecia_wybor" accept=".jpg,.jpeg,.png" multiple><br />
                    <input type="submit" value="DODAJ">
                </form>
            </div>

            <!------------------ TWORZENIE SEZONU ------------------>
            <div id="tworzenie-sezonu">
                <h2> STWÓRZ SEZON </h2>
                <div id="dalej-button" class="link">
                    <a href="<?= PREFIX ?>/admin/sezon">+</a>
                </div>
                <br />
                <h2> STWÓRZ RUNDĘ FINAŁOWĄ </h2>
                <div id="dalej-button" class="link">
                    <a href="<?= PREFIX ?>/skrypty/admin_final">+</a>
                </div>
            </div>

            <div style="clear: both;"></div>
        </div>
    </div>
<?php
}
