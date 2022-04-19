<?php
is_logged();
register_style("admin");

function page_init()
{ // seasons()
    return PDOS::Instance()->query("SELECT `season_id`, `name` FROM `ng_season` ORDER BY `created_at` DESC")->fetchAll(PDO::FETCH_ASSOC);
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
                    <h3> TYTUŁ </h3>
                    <textarea cols="30" rows="2" id="info_tytul" maxlength="50" name="info_tytul"></textarea><br />
                    <h3> TREŚĆ </h3>
                    <textarea cols="32" rows="10" id="info_tresc" name="info_tresc"></textarea><br />
                    <input type="submit" value="PUBLIKUJ">
                </form>
            </div>

            <!------------------ WPISYWANIE WYNIKÓW ------------------>
            <div id="aktualizacja-sezonu">
                <h2> AKTUALIZUJ SEZON </h2>
                <h3> WPISZ WYNIKI </h3>
                <div id="dalej-button">
                    <a href="<?= PREFIX ?>/admin/wyniki">+</a>
                </div>
                <h3> AKTUALIZUJ HARMONOGRAM </h3>
                <div id="dalej-button">
                    <a href="<?= PREFIX ?>/admin/harmonogram">+</a>
                </div>
            </div>
            <div style="clear: both;"></div>

            <!------------------ DODAWANIE ZDJĘĆ ------------------>
            <div id="zdjecia">
                <h2> DODAJ ZDJĘCIA </h2>
                <form method="post" enctype="multipart/form-data" action="<?= PREFIX ?>/skrypty/dodaj-zdjecie">
                    <h3> WYBIERZ SEZON: </h3>
                    <select id="zdjecia_sezon" name="zdjecie_sezon">
                        <?php foreach ($obj as $sezon) : ?>
                            <option value="<?= $sezon['season_id'] ?>"><?= $sezon['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                    <h3> WYBIERZ ZDJĘCIA (.jpg) </h3>
                    <input type="file" name="files[]" id="zdjecia_wybor" accept=".jpg,.jpeg,.png" multiple><br />
                    <input type="submit" value="DODAJ">
                </form>
            </div>

            <!------------------ TWORZENIE SEZONU ------------------>
            <div id="tworzenie-sezonu">
                <h2> STWÓRZ SEZON </h2>
                <div id="dalej-button">
                    <a href="<?= PREFIX ?>/admin/sezon">+</a>
                </div>
                <br />
                <h2> STWÓRZ RUNDĘ FINAŁOWĄ </h2>
                <div id="dalej-button">
                    <a href="<?= PREFIX ?>/skrypty/admin_final">+</a>
                </div>
            </div>

            <div style="clear: both;"></div>
        </div>
    </div>
<?php }
