<?php

is_logged();

function page_perform()
{
    $id = cast_int($_POST['id']);
    try {
        PDOS::Instance()->cmd(
            "add_season(id, name, html_name, grouping)",
            [$id, $_POST['name'], $_POST['name'], $_POST['grouping']]
        );
        header('Location: ' . PANEL_URL . '/sezon/' . $id);
    } catch (PDOException $e) {
        if ($e->errorInfo[1] == 1062) {
            report_error($id !== null ? "Sezon o id=$id już istnieje!" : "Wprowadzono nieprawidłowe id", null);
        } else {
            throw $e;
        }
    }
}


function page_render()
{
?>
    <script src="<?= PREFIX ?>/js/nowy_sezon.js"></script>
    <div id="content">
        <h1> TWORZENIE SEZONU </h1>
        <div id="panel">
            <form method="post">
                <label for="name">Podaj nazwę sezonu (wpisz rok rozpoczęcia roku szkolnego):</label><br>
                <input type="text" name="name" id="name" required><br>
                <label for="id">Podaj id sezonu (będzie się wyświetlać we wszystkich linkach): </label><br>
                <input type="number" name="id" id="id" required><br>
                <label for="grouping">Wybierz format rozgrywek: (wybierz "Bez rozgrywek" jeżeli jeszcze nie wiesz)</label><br>
                <select name="grouping" id="grouping">
                    <option value="no_grouping">Bez rozgrywek</option>
                    <option value="two_groups">Dwie grupy</option>
                    <option value="two_rounds">Dwie rundy</option>
                </select><br>
                <input type="submit" value="Utwórz sezon!">
            </form>
        </div>
    <?php
}
