<?php

is_logged();

global $sezon;
$sezon = cast_int(HIT_UNPACK());
if ($sezon === null) {
    header("Location: ". PANEL_URL . "/wyniki/" . obecny_sezon());
    exit();
}

function page_perform()
{
    global $sezon;
    $games = PDOS::Instance()->cmd(
        "get_game_types(season)",
        [$sezon]
    )->fetchAll(PDO::FETCH_ASSOC);
    try {
        PDOS::Instance()->beginTransaction();
        $update_final = false;
        foreach ($games as $game) {
            $w1 = cast_int($_POST[$game['game_id'] . '_1']);
            $w2 = cast_int($_POST[$game['game_id'] . '_2']);
            $half = (substr($game['type'], 0, 4) == 'half');
            if ($half and $w1 !== null and $w1 === $w2) {
                report_error("W półfinale nie może być remisu!", null);
                continue;
            }
            if ($half) {
                $update_final = true;
            }
            PDOS::Instance()->cmd(
                "set_game_score(A, B, season, game_id)",
                [$w1, $w2, $sezon, $game['game_id']]
            );
        }
        if ($update_final) {
            PDOS::Instance()->cmd("update_final_participants(season)", [$sezon]);
        }
        PDOS::Instance()->commit();
    } catch (Exception $e) {
        PDOS::Instance()->rollback();
        throw $e;
    }
}

function page_init()
{
    global $sezon;
    $grupowe = array();
    $grupowe[] = PDOS::Instance()->cmd(
        "get_games(season, finals?, group)",
        [$sezon, false, 'first']
    )->fetchAll(PDO::FETCH_ASSOC);
    $grupowe[] = PDOS::Instance()->cmd(
        "get_games(season, finals?, group)",
        [$sezon, false, 'second']
    )->fetchAll(PDO::FETCH_ASSOC);
    $finalowe = PDOS::Instance()->cmd(
        "get_games(season, finals?, group)",
        [$sezon, true, '']
    )->fetchAll(PDO::FETCH_ASSOC);
    return array(
        'sezon' => $sezon,
        'grupowe' => $grupowe,
        'finalowe' => count($finalowe) > 0 ? $finalowe : null,
    );
}

function page_render($obj)
{ ?>

    <div id="content">
        <h1> WPISZ WYNIKI (jesli się nie odbył to zostaw puste) </h1>

        <form method='post'>
            <?php if (!is_null($obj['finalowe'])) : ?>
                <?php foreach ($obj['finalowe'] as $mecz) : ?>
                    <?= $mecz['title'] ?> | <?= coalesce($mecz['date'], 'nie ustalono') ?> <br /><?= coalesce($mecz['A_team'], '???') ?>
                    <input class='wynik' type='number' value='<?= $mecz['A_score'] ?>' name='<?= $mecz['game_id'] ?>_1'> :
                    <input class='wynik' type='number' value='<?= $mecz['B_score'] ?>' name='<?= $mecz['game_id'] ?>_2'>
                    <?= coalesce($mecz['B_team'], '???') ?>
                    <br />
                <?php endforeach; ?>
            <?php endif; ?>
            <?php for ($grupa = 0; $grupa <= 1; ++$grupa) : ?>
                <div class='grupy'>
                    <h2>Grupa <?= coalesce($grupa + 1) ?></h2>
                    <table style="text-align: center;">
                        <?php foreach ($obj['grupowe'][$grupa] as $mecz) : ?>
                            <tr>
                                <td></td><td><?= coalesce($mecz['date'], 'nie ustalono') ?><br /></td>
                            </tr>
                            <tr style="margin-bottom: 10px;">
                                <td style="text-align: right;vertical-align: middle;"><?= coalesce($mecz['A_team'], '???') ?>
                                </td>
                                <td style="vertical-align: bottom;"><input class='wynik' type='number' name='<?= $mecz['game_id'] ?>_1' value='<?= $mecz['A_score'] ?>'> :
                                    <input class='wynik' type='number' name='<?= $mecz['game_id'] ?>_2' value='<?= $mecz['B_score'] ?>'>
                                </td>
                                <td style="text-align: left; vertical-align: middle;"> <?= coalesce($mecz['B_team'], '???') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            <?php endfor; ?>
            <div style="clear: both;"></div>
            <input type='submit' value='AKTUALIZUJ!'>
        </form>
    </div>
    <div style='clear: both;'></div>

<?php }
