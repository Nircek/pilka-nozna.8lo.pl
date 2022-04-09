<?php

// Pobieranie nazwy obecnego sezonu
function obecny_sezon()
{
    try {
        $sql = "SELECT sezon FROM sezony ORDER BY sezon DESC LIMIT 1";
        $result = PDOS::Instance()->query($sql);
        $liczba = $result->rowCount();
    } catch (PDOException $e) {
        return 0;
    }

    if ($liczba === 1) {
        while ($row = $result->fetch()) {
            $obecny_sezon[] = array('sezon' => $row['sezon']);
        }

        foreach ($obecny_sezon as $obecny_sezon) {
            return $obecny_sezon['sezon'];
        }
    }
}

// Sprawdzanie czy tabela istnieje
function sprawdzanie_tabela($tabela)
{
    try {
        $x = PDOS::Instance()->query("SELECT * FROM $tabela");
    } catch (PDOException $e) {
        return false;
    }
    return true;
}

// Wypisuje harmonogram w formie pojedynczych tabelek na podstawie sezonu i grupy
function harmonogram_grupy($sezon_terminarz, $grupa)
{
    try {
        $sql = "SELECT * FROM $sezon_terminarz WHERE grupa=$grupa ORDER BY termin DESC";
        $result_terminarz = PDOS::Instance()->query($sql);
    } catch (PDOException $e) {
        reportError("db", $e->getMessage());
    }
    while ($row = $result_terminarz->fetch()) {
        $grupa_1_wyniki[] = array(
            'druzyna_1' => $row['1_text'],
            'druzyna_2' => $row['2_text'],
            'gole_1' => $row['1_wynik'],
            'gole_2' => $row['2_wynik'],
            'termin' => $row['termin']
        );
    }

    foreach ($grupa_1_wyniki as $grupa_1_wyniki) :
        if ($grupa_1_wyniki['gole_1'] == null or $grupa_1_wyniki['gole_2'] == null) {
            $grupa_1_wyniki['gole_1'] = $grupa_1_wyniki['gole_2'] = "-";
        }

        if ($grupa_1_wyniki['termin'] == "0000-00-00" or $grupa_1_wyniki['termin'] == null) {
            $grupa_1_wyniki['termin'] = "nie ustalono";
        }
?>
        <table id='terminarz' cellspacing='0'>
            <tr id='tr_termin'>
                <td colspan='3'> <?= $grupa_1_wyniki['termin'] ?> </td>
            </tr>
            <tr id='tr_wynik'>
                <td> <?= $grupa_1_wyniki['druzyna_1'] ?> </td>
                <td id='td_wynik'>
                    <?= $grupa_1_wyniki['gole_1'] ?>:<?= $grupa_1_wyniki['gole_2'] ?>
                </td>
                <td> <?= $grupa_1_wyniki['druzyna_2'] ?> </td>
            </tr>
        </table>
    <?php
    endforeach;
}

function show_tabela($wariant, $sezon_tabela, $grupa)
{
    /*
        wariant 1 = wersja rozszerzona ze wszystkim co jest w tabeli
        wariant 2 = wersja skrocona do najwazniejszych informacji
    */
    if ($wariant == 1) :
    ?>
        <table id="tabela" cellspacing="0">
            <tr>
                <th> LP </th>
                <th> ZESPÓŁ </th>
                <th> PKT </th>
                <th> Z </th>
                <th> R </th>
                <th> P </th>
                <th> Strzel. </th>
                <th> Strac. </th>
                <th> +/- </th>
            </tr>
            <?php
            try {
                $sql = "SELECT * FROM $sezon_tabela WHERE grupa=$grupa ORDER BY pkt DESC, (zdobyte - stracone) DESC, nazwa ASC";
                $result_tabela = PDOS::Instance()->query($sql);
            } catch (PDOException $e) {
                reportError("db", $e->getMessage());
            }
            $tabela = array();
            while ($row = $result_tabela->fetch()) {
                $tabela[] = array(
                    'nazwa' => $row['nazwa'],
                    'pkt' => $row['pkt'],
                    'z' => $row['zwyciestwa'],
                    'r' => $row['remisy'],
                    'p' => $row['przegrane'],
                    'zdobyte' => $row['zdobyte'],
                    'stracone' => $row['stracone']
                );
            }
            $i = 1;
            foreach ($tabela as $tabela) {
                $bilans = $tabela['zdobyte'] - $tabela['stracone'];
                if ($bilans > 0) {
                    $bilans = "+" . $bilans;
                }
            ?>
                <tr>
                    <td> <?= $i ?> </td>
                    <td> <?= $tabela['nazwa'] ?> </td>
                    <td> <?= $tabela['pkt'] ?> </td>
                    <td> <?= $tabela['z'] ?> </td>
                    <td> <?= $tabela['r'] ?> </td>
                    <td> <?= $tabela['p'] ?> </td>
                    <td> <?= $tabela['zdobyte'] ?> </td>
                    <td> <?= $tabela['stracone'] ?> </td>
                    <td> <?= $bilans ?> </td>
                </tr>
            <?php
                $i++;
            }
            ?>
        </table>
    <?php elseif ($wariant == 2) : ?>
        <table id="tabela" cellspacing="0">
            <tr>
                <th> LP </th>
                <th> ZESPÓŁ </th>
                <th> PKT </th>
                <th> Z </th>
                <th> R </th>
                <th> P </th>
            </tr>
            <?php
            try {
                $sql = "SELECT * FROM $sezon_tabela WHERE grupa=$grupa ORDER BY pkt DESC, (zdobyte - stracone) DESC, nazwa ASC";
                $result_tabela = PDOS::Instance()->query($sql);
            } catch (PDOException $e) {
                reportError("db", $e->getMessage());
            }

            while ($row = $result_tabela->fetch()) {
                $tabela[] = array(
                    'nazwa' => $row['nazwa'],
                    'pkt' => $row['pkt'],
                    'z' => $row['zwyciestwa'],
                    'r' => $row['remisy'],
                    'p' => $row['przegrane']
                );
            }
            $i = 1;
            foreach ($tabela as $tabela) :
            ?>
                <tr>
                    <td> <?= $i ?> </td>
                    <td> <?= $tabela['nazwa'] ?> </td>
                    <td> <?= $tabela['pkt'] ?> </td>
                    <td> <?= $tabela['z'] ?> </td>
                    <td> <?= $tabela['r'] ?> </td>
                    <td> <?= $tabela['p'] ?> </td>
                </tr>

            <?php
                $i++;
            endforeach; ?>
        </table>
<?php
    endif;
}
