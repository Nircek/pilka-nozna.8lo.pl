<?php

is_logged();

function page_render()
{
?>
    <div id="content">
        <h1> TWORZENIE SEZONU </h1>

        <div id="panel">
            <br>
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
                        <option value="doublerobin">Double-robin</option>
                        <option value="groups">Dwie grupy</option>
                    </select>
                </div>
                <div>
                    <input type="submit" value="Utwórz sezon!">
                </div>
            </form>
        </div>
    </div>
<?php }
