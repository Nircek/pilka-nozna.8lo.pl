<?php
register_style("admin");
is_logged();

if (!isset($_SESSION['sezon_krok']) or !in_array($_SESSION['sezon_krok'], array(1, 2, 3))) {
    $_SESSION['sezon_krok'] = 1;
}
$krok = $_SESSION['sezon_krok'];
require(ROOT_PATH . "/sites/fragment/sezon_krok$krok.php");

function page_render()
{
?>
    <div id="content">
        <h1> TWORZENIE SEZONU - KROK <?= $_SESSION['sezon_krok'] ?> </h1>

        <div id="panel">
            <form method="post">
                <div>
                    <label for="seasonName">Podaj nazwę sezonu: </label>
                    <input type="text" name="htmlNazwa" id="seasonName" required>
                </div>
                <div>
                    <label for="seasonId">Podaj id sezonu: </label>
                    <input type="text" name="Id" id="seasonId" required>
                </div>
                <div>
                    <label for="format">Wybierz format rozgrywek: </label>
                    <select name="Format_rozgrywek" id="format">
                        <option value="null">null</option>
                        <option value="double-robin">Double-robin</option>
                        <option value="robin">Dwie grupy</option>
                    </select>
                </div>
                <div>
                    <input type="submit" value="Utwórz sezon!">
                </div>
            </form>
        </div>
    </div>
<?php }
