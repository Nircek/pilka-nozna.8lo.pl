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
    if ($_POST['type'] === "edit") {
        PDOS::Instance()->cmd(
            "update_season(name, html_name, description, id)",
            [$_POST['name'], $_POST['html_name'], $_POST['description'], $sezon]
        );
    } elseif ($_POST['type'] === "add_team") {

        $details = PDOS::Instance()->cmd("get_season(season)", [$sezon])->fetch(PDO::FETCH_ASSOC);

        try {
            PDOS::Instance()->beginTransaction();
            if ($details['grouping_type'] == "two_rounds") {
                $id = PDOS::Instance()->cmd("add_new_team(season_id, name)", [$sezon, $_POST['team']])->fetch(PDO::FETCH_COLUMN)[0];
                PDOS::Instance()->cmd("add_new_team_games(season_id, new_team_id)", [$sezon, $id]);
            } elseif ($details['grouping_type'] == "two_groups") {
                $id = PDOS::Instance()->cmd(
                    "add_new_team_in_group(season, group, name)",
                    [$sezon, $_POST['group'], $_POST['team']]
                )->fetch(PDO::FETCH_COLUMN)[0];
                PDOS::Instance()->cmd("add_new_team_in_group_games(season_id, new_team_id)", [$sezon, cast_int($id)]);
            }
            PDOS::Instance()->commit();
        } catch (Exception $e) {
            PDOS::Instance()->rollback();
            throw $e;
        }
    }
}

function page_init()
{
    global $sezon;

    $details = PDOS::Instance()->cmd("get_season(season)", [$sezon])->fetch(PDO::FETCH_ASSOC);
    $druzyny = array();
    if ($details['grouping_type'] == "two_rounds") {
        $druzyny[] = PDOS::Instance()->cmd(
            "list_teams(season_id, ignore_group?, group)",
            [$sezon, true, '']
        )->fetchAll(PDO::FETCH_ASSOC);
    } elseif ($details['grouping_type'] == "two_groups") {
        for ($i = 1; $i <= 2; ++$i) {
            $druzyny[] = PDOS::Instance()->cmd(
                "list_teams(season_id, ignore_group?, group)",
                [$sezon, false, $i == 1 ? 'first' : 'second']
            )->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    return array(
        'sezon' =>  $details,
        'sezony' => PDOS::Instance()->cmd("get_seasons()")->fetchAll(PDO::FETCH_ASSOC),
        'druzyny' => $druzyny
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
        <?php foreach ($obj['druzyny'] as $ig => $grupa) : ?>
            <div class="group">
                <?php if (count($obj['druzyny']) > 1) : ?>
                    <h2> GRUPA <?= $ig + 1 ?> </h2>
                <?php else : ?>
                    <h2> DRUŻYNY </h2>
                <?php endif; ?>
                <?php foreach ($grupa as $i => $team) : ?>
                    <?= $i + 1 ?>. <?= $team['name'] ?>
                    <!-- <?= $team['team_id'] ?> --> <br>
                <?php endforeach; ?>
                <br>
                <form method="post" action="#">
                    <input type="text" name="team">
                    <br>
                    <input type="hidden" name="group" value="<?= $ig == 0 ? 'first' : 'second' ?>" />
                    <input type="hidden" name="type" value="add_team" />
                    <input type="submit" value="DODAJ DUŻYNĘ!">
                </form>
                <br>
            </div>
        <?php endforeach; ?>
    </div>
<?php
}
