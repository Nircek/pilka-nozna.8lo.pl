<?php
define("ADMIN_LOGIN_URL", "$PREFIX/admin/login");
function is_logged($admin_site = true, $redirect_url = ADMIN_LOGIN_URL)
{
    if (!isset($_SESSION['zalogowany'])) {
        if ($admin_site) {
            header("Location: $redirect_url");
        }
    }
}

// Sprawdza czy cokolwiek wpisano i robi update odpowiednich rekordów
function dodawanie_wyniku($sezon_terminarz, $id, $wynik_1, $wynik_2)
{
    include(ROOT_PATH . "/funkcje/db-connect.php");

    if ((empty($wynik_1) and !is_numeric($wynik_1)) or (empty($wynik_2) and !is_numeric($wynik_1))) {
        $wynik_1 = null;
        $wynik_2 = null;
        $sql = "UPDATE $sezon_terminarz SET 1_wynik =NULL, 2_wynik=NULL WHERE id='$id' ";
    } else {
        $sql = "UPDATE $sezon_terminarz SET 1_wynik ='$wynik_1', 2_wynik='$wynik_2' WHERE id='$id' ";
    }

    // Wkładanie wszystkich wyników do bazy danych
    try {
        $pdo->exec($sql);
    } catch (PDOException $e) {
        $_SESSION['e_wyniki_baza'] = "Błąd bazy danych: $e <br/> $sql";
        header('Location: ../nowe_wyniki.php');
        exit();
    }
}

function resetowanie_tabeli($sezon_tabela)
{
    include(ROOT_PATH . "/funkcje/db-connect.php");

    // RESETOWANIE TABELI Z PUNKTAMI
    // Trzeba to zrobić ze względu na to iż pkt dodaje się do już zapisanych i byłby problem gdyby chciało się jakiś mecz anulować
    // Dlatego wszystko zawsze zlicza się od poczatku
    try {
        $sql = "UPDATE `$sezon_tabela`
                SET `pkt` = 0,
                    `zwyciestwa` = 0,
                    `przegrane` = 0,
                    `remisy` = 0,
                    `zdobyte` = 0,
                    `stracone` = 0";
        $pdo->exec($sql);
    } catch (PDOException $e) {
        $_SESSION['e_wyniki_baza'] = "Błąd bazy danych: $e";
        header('Location: admin_wyniki.php');
        exit();
    }
}

function tabela($sezon_tabela, $grupa, $d1, $d2, $gole_1, $gole_2)
{
    include(ROOT_PATH . "/funkcje/db-connect.php");

    if ($gole_1 != null and $gole_2 != null) {
        // Dodawanie odpowiednich danych odpowiednim zespołom
        if ($gole_1 == $gole_2) {
            try {
                $sql = "UPDATE `$sezon_tabela`
                            SET `remisy` = `remisy` + 1, `pkt` = `pkt` + 1, `zdobyte` = `zdobyte` + '$gole_1', `stracone` = `stracone` + '$gole_2'
                            WHERE `numer` = '$d1' AND `grupa` = '$grupa'";
                $pdo->exec($sql);
            } catch (PDOException $e) {
                $_SESSION['e_wyniki_baza'] = "Błąd bazy danych: $e <br/> $sql";
                header('Location: ../nowe_wyniki.php');
                exit();
            }

            try {
                $sql = "UPDATE `$sezon_tabela`
                            SET `remisy` = `remisy` + 1, `pkt` = `pkt` + 1, `zdobyte` = `zdobyte` + '$gole_2', `stracone` = `stracone` + '$gole_1'
                            WHERE `numer` = '$d2' AND `grupa` = '$grupa'";
                $pdo->exec($sql);
            } catch (PDOException $e) {
                $_SESSION['e_wyniki_baza'] = "Błąd bazy danych: $e <br/> $sql";
                header('Location: ../nowe_wyniki.php');
                exit();
            }
        } elseif ($gole_1 > $gole_2) {
            try {
                $sql = "UPDATE `$sezon_tabela`
                            SET `zwyciestwa` = `zwyciestwa` + 1, `pkt` = `pkt` + 3, `zdobyte` = `zdobyte` + '$gole_1', `stracone` = `stracone` + '$gole_2'
                            WHERE `numer` = '$d1' AND `grupa` = '$grupa'";
                $pdo->exec($sql);
            } catch (PDOException $e) {
                $_SESSION['e_wyniki_baza'] = "Błąd bazy danych: $e <br/> $sql";
                header('Location: ../nowe_wyniki.php');
                exit();
            }

            try {
                $sql = "UPDATE `$sezon_tabela`
                            SET `przegrane` = `przegrane` + 1, `zdobyte` = `zdobyte` + '$gole_2', `stracone` = `stracone` + '$gole_1'
                            WHERE `numer` = '$d2' AND `grupa` = '$grupa'";
                $pdo->exec($sql);
            } catch (PDOException $e) {
                $_SESSION['e_wyniki_baza'] = "Błąd bazy danych: $e <br/> $sql";
                header('Location: ../nowe_wyniki.php');
                exit();
            }
        } elseif ($gole_1 < $gole_2) {
            try {
                $sql = "UPDATE `$sezon_tabela`
                            SET `przegrane` = `przegrane` + 1, `zdobyte` = `zdobyte` + '$gole_1', `stracone` = `stracone` + '$gole_2'
                            WHERE `numer` = '$d1' AND `grupa` = '$grupa'";
                $pdo->exec($sql);
            } catch (PDOException $e) {
                $_SESSION['e_wyniki_baza'] = "Błąd bazy danych: $e <br/> $sql";
                header('Location: ../nowe_wyniki.php');
                exit();
            }

            try {
                $sql = "UPDATE `$sezon_tabela`
                            SET `zwyciestwa` = `zwyciestwa` + 1, `pkt` = `pkt` + 3, `zdobyte` = `zdobyte` + '$gole_2', `stracone` = `stracone` + '$gole_1'
                            WHERE `numer` = '$d2' AND `grupa` = '$grupa'";
                $pdo->exec($sql);
            } catch (PDOException $e) {
                $_SESSION['e_wyniki_baza'] = "Błąd bazy danych: $e <br/> $sql";
                header('Location: ../nowe_wyniki.php');
                exit();
            }
        }
    }
}
