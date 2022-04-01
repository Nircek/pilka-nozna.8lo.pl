<?php

include(ROOT_PATH . "/funkcje/funkcje_admin.php");
is_logged();
include(ROOT_PATH . "/funkcje/db-connect.php");

$sezon_terminarz = $_POST['sezon'] . "_terminarz";
$sezon_tabela = $_POST['sezon'] . "_tabela";
$sezon_final = $_POST['sezon'] . "_final";

// =================== FAZA FINAŁOWA ===================
if (isset($_POST['final'])) {
    // Początkowy reset finału i 3 miejsca, gdybym pomylono wyniki i drużyny musiały być rozdzielone na nowo (żeby nie trzeba było wchodzić do bazy danych)
    try {
        $sql = "UPDATE `$sezon_final`
                        SET `druzyna_1` = NULL,
                            `druzyna_2` = NULL,
                            `wynik_1` = NULL,
                          `wynik_2` = NULL
                        WHERE `poziom` = '3' OR `poziom` = '1'";
        $pdo->exec($sql);
    } catch (PDOException $e) {
        $_SESSION['e_wyniki_baza'] = "Błąd bazy danych: $e";
        header('Location: ../admin_wyniki.php');
        exit();
    }

    for ($y = 1; $y <= 4; $y++) {
        if (isset($_POST['f_etap_' . $y])) {
            $etap = $_POST['f_etap_' . $y];
            $d1 = $_POST['f_d1_' . $y];
            $d2 = $_POST['f_d2_' . $y];
            $wynik_1 = $_POST['f_wynik_1_' . $y];
            $wynik_2 = $_POST['f_wynik_2_' . $y];

            // Jeśli wpisane dane są puste ale nie są też "0"
            if ((empty($wynik_1) and !is_numeric($wynik_1)) or (empty($wynik_2) and !is_numeric($wynik_1))) {
                $sql_1 = "UPDATE $sezon_final SET wynik_1 =NULL, wynik_2=NULL WHERE id='$y'";
                try {
                    $pdo->exec($sql_1);
                } catch (PDOException $e) {
                    $_SESSION['e_wyniki_baza'] = "Błąd tabeli finałowej: $e";
                    header('Location: ../admin_wyniki.php');
                    exit();
                }
            } else {
                $sql_1 = "UPDATE $sezon_final SET wynik_1 ='$wynik_1', wynik_2='$wynik_2' WHERE id='$y'";

                // Jeśli analizuje półfinały
                if ($etap == 2) {
                    if ($wynik_1 == $wynik_2) {
                        $_SESSION['e_wyniki_remis'] = "W finale nie może być remisu!";
                        header('Location: ../admin_wyniki.php');
                        exit();
                    } else {
                        // Przydzielanie drużyn odpowiednio do finały lub 3 miejsca
                        if (max($wynik_1, $wynik_2) == $wynik_1) {
                            $sql_2 = "UPDATE $sezon_final SET druzyna_$y ='$d1' WHERE poziom='1'";
                            $sql_3 = "UPDATE $sezon_final SET druzyna_$y ='$d2' WHERE poziom='3'";
                        } elseif (max($wynik_1, $wynik_2) == $wynik_2) {
                            $sql_2 = "UPDATE $sezon_final SET druzyna_$y ='$d2' WHERE poziom='1'";
                            $sql_3 = "UPDATE $sezon_final SET druzyna_$y ='$d1' WHERE poziom='3'";
                        }
                    }

                    try {
                        $pdo->exec($sql_1);
                        $pdo->exec($sql_2);
                        $pdo->exec($sql_3);
                    } catch (PDOException $e) {
                        $_SESSION['e_wyniki_baza'] = "Błąd tabeli finałowej: $e";
                        header('Location: ../admin_wyniki.php');
                        exit();
                    }
                } else {
                    // Jeśli finał lub 3 miejsce
                    try {
                        $pdo->exec($sql_1);
                    } catch (PDOException $e) {
                        $_SESSION['e_wyniki_baza'] = "Błąd tabeli finałowej: $e";
                        header('Location: ../admin_wyniki.php');
                        exit();
                    }
                }
            }
        }
    }
} elseif (isset($_POST['liczba_1']) == true) {
    // =================== FAZA GRUPOWA ===================
    $ilosc_1 = $_POST['liczba_1'] - 1;
    $ilosc_2 = $ilosc_1 + $_POST['liczba_2'] - 1;

    // Resetowanie tabeli dla nowych danych
    resetowanie_tabeli($sezon_tabela);

    // =================== PĘTLA GRUPY 1 ===================
    for ($i = 1; $i <= $ilosc_1; $i++) {
        $dane[$i][0] = $i;
        $dane[$i][1] = $_POST["${i}_1"];
        $dane[$i][2] = $_POST["${i}_2"];
        $dane[$i][3] = $_POST["d1_$i"];
        $dane[$i][4] = $_POST["d2_$i"];
        $wynik_1 = $dane[$i][1];
        $wynik_2 = $dane[$i][2];
        $id = $i;

        tabela($sezon_tabela, 1, $dane[$i][3], $dane[$i][4], $wynik_1, $wynik_2);
        dodawanie_wyniku($sezon_terminarz, $id, $wynik_1, $wynik_2);
    }

    $x = 1;
    // =================== PĘTLA GRUPY 2 ===================
    for ($i = $i; $i <= $ilosc_2; $i++) {
        $dane[$i][0] = $i;
        $dane[$i][1] = $_POST[$i . "_1"];
        $dane[$i][2] = $_POST[$i . "_2"];
        $dane[$i][3] = $_POST["d1_" . $x];
        $dane[$i][4] = $_POST["d2_" . $x];
        $wynik_1 = $dane[$i][1];
        $wynik_2 = $dane[$i][2];
        $id = $i;

        tabela($sezon_tabela, 2, $dane[$i][3], $dane[$i][4], $wynik_1, $wynik_2);
        dodawanie_wyniku($sezon_terminarz, $id, $wynik_1, $wynik_2);
        $x++;
    }
}

header('Location: ../admin_wyniki.php');
exit();
