<?php

// Pobieranie nazwy obecnego sezonu
function obecny_sezon($pdo)
{
    try {
        $sql = "SELECT sezon FROM sezony ORDER BY sezon DESC LIMIT 1";
        $result = $pdo->query($sql);
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
function sprawdzanie_tabela($pdo, $tabela)
{
    try {
        $x = $pdo->query("SELECT * FROM $tabela");
    } catch (PDOException $e) {
        return false;
    }
    return true;
}

// Wypisuje harmonogram w formie pojedynczych tabelek na podstawie sezonu i grupy
function harmonogram_grupy($sezon_terminarz, $grupa)
{
    include('./skrypty/db-connect.php');

    try {
        $sql = "SELECT * FROM $sezon_terminarz WHERE grupa=$grupa ORDER BY termin DESC";
        $result_terminarz = $pdo->query($sql);
    } catch (PDOException $e) {
        echo "<div id='error'> " . $e . " </div>";
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

    foreach ($grupa_1_wyniki as $grupa_1_wyniki) {
        if ($grupa_1_wyniki['gole_1'] == null or $grupa_1_wyniki['gole_2'] == null) {
            $grupa_1_wyniki['gole_1'] = $grupa_1_wyniki['gole_2'] = "-";
        }

        if ($grupa_1_wyniki['termin'] == "0000-00-00" or $grupa_1_wyniki['termin'] == null) {
            $grupa_1_wyniki['termin'] = "nie ustalono";
        }

        echo "<table id='terminarz' cellspacing='0'>
                <tr id='tr_termin'>
                <td colspan='3'>" . $grupa_1_wyniki['termin'] . "</td>
                </tr>
                <tr id='tr_wynik'>
                <td>" . $grupa_1_wyniki['druzyna_1'] . "</td>
                <td id='td_wynik'>" .
                    $grupa_1_wyniki['gole_1'] . ":" . $grupa_1_wyniki['gole_2'] . "
                </td>
                <td>" . $grupa_1_wyniki['druzyna_2'] . "</td>
                </tr>
            </table>";
    }
}

function show_tabela($wariant, $sezon_tabela, $grupa)
{
    /*
        wariant 1 = wersja rozszerzona ze wszystkim co jest w tabeli
        wariant 2 = wersja skrocona do najwazniejszych informacji
    */
    include('./skrypty/db-connect.php');
    if ($wariant == 1) {
        echo '<table id="tabela" cellspacing="0">
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
                </tr> ';

        try {
            $sql = "SELECT * FROM $sezon_tabela WHERE grupa=$grupa ORDER BY pkt DESC, (zdobyte - stracone) DESC, nazwa ASC";
            $result_tabela = $pdo->query($sql);
        } catch (PDOException $e) {
            echo "<div id='error'> " . $e . " </div>";
        }
        while ($row = $result_tabela->fetch()) {
            $tabela[] = array('nazwa' => $row['nazwa'],
                            'pkt' => $row['pkt'],
                            'z' => $row['zwyciestwa'],
                            'r' => $row['remisy'],
                            'p' => $row['przegrane'],
                            'zdobyte' => $row['zdobyte'],
                            'stracone' => $row['stracone']);
        }
        $i = 1;
        foreach ($tabela as $tabela) {
            $bilans = $tabela['zdobyte'] - $tabela['stracone'];
            if ($bilans > 0) {
                $bilans = "+" . $bilans;
            }

            echo "<tr>
                    <td> $i. </td>
                    <td>" . $tabela['nazwa'] . "</td>
                    <td>" . $tabela['pkt'] . "</td>
                    <td>" . $tabela['z'] . "</td>
                    <td>" . $tabela['r'] . "</td>
                    <td>" . $tabela['p'] . "</td>
                    <td>" . $tabela['zdobyte'] . "</td>
                    <td>" . $tabela['stracone'] . "</td>
                    <td>" . $bilans . "</td>
                </tr>";
            $i++;
        }
        echo '</table>';
    } elseif ($wariant == 2) {
        echo '<table id="tabela" cellspacing="0">
                <tr>
                    <th> LP </th>
                    <th> ZESPÓŁ </th>
                    <th> PKT </th>
                    <th> Z </th>
                    <th> R </th>
                    <th> P </th>
                </tr> ';

        try {
            $sql = "SELECT * FROM $sezon_tabela WHERE grupa=$grupa ORDER BY pkt DESC, (zdobyte - stracone) DESC, nazwa ASC";
            $result_tabela = $pdo->query($sql);
        } catch (PDOException $e) {
            echo "<div id='error'> " . $e . " </div>";
        }

        while ($row = $result_tabela->fetch()) {
            $tabela[] = array('nazwa' => $row['nazwa'],
                            'pkt' => $row['pkt'],
                            'z' => $row['zwyciestwa'],
                            'r' => $row['remisy'],
                            'p' => $row['przegrane']);
        }

        $i = 1;
        foreach ($tabela as $tabela) {
            echo "<tr>
                    <td> $i. </td>
                    <td>" . $tabela['nazwa'] . "</td>
                    <td>" . $tabela['pkt'] . "</td>
                    <td>" . $tabela['z'] . "</td>
                    <td>" . $tabela['r'] . "</td>
                    <td>" . $tabela['p'] . "</td>
                </tr>";
            $i++;
        }
        echo '</table>';
    }
}
