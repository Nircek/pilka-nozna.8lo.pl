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
        "update_grouping_type(new, season_id)",
        [$_POST['grouping'], $sezon]
    );
    header('Location: ' . PANEL_URL . '/sezon/' . $sezon);
}


function page_render()
{
    global $sezon;
?>
    <div id="content">
        <h1> TRANSFORMACJA SEZONU </h1>
        <div id="panel">
            <form method="post">
                <p>ID sezonu: <?= $sezon ?></p>
                <label for="grouping">Wybierz format rozgrywek (nieodwracalna akcja): </label><br>
                <select name="grouping" id="grouping">
                    <option value="two_groups">Dwie grupy</option>
                    <option value="two_rounds">Dwie rundy</option>
                </select><br>
                <input type="submit" value="Otwórz sezon!">
            </form>
        </div>
    <?php
}
