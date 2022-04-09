<?php
define("ADMIN_LOGIN_URL", PREFIX . "/admin/login");
define("ADMIN_MAIN_URL", PREFIX . "/admin");
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
    if ((empty($wynik_1) and !is_numeric($wynik_1)) or (empty($wynik_2) and !is_numeric($wynik_1))) {
        $wynik_1 = null;
        $wynik_2 = null;
        $sql = "UPDATE $sezon_terminarz SET 1_wynik =NULL, 2_wynik=NULL WHERE id='$id' ";
    } else {
        $sql = "UPDATE $sezon_terminarz SET 1_wynik ='$wynik_1', 2_wynik='$wynik_2' WHERE id='$id' ";
    }

    // Wkładanie wszystkich wyników do bazy danych
    try {
        PDOS::Instance()->exec($sql);
    } catch (PDOException $e) {
        reportError("db", $e->getMessage());
        header("Location: " . ADMIN_MAIN_URL);
        exit();
    }
}

function resetowanie_tabeli($sezon_tabela)
{
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
        PDOS::Instance()->exec($sql);
    } catch (PDOException $e) {
        reportError("db", $e->getMessage());
        header("Location: " . ADMIN_MAIN_URL);
        exit();
    }
}

function tabela($sezon_tabela, $grupa, $d1, $d2, $gole_1, $gole_2)
{
    if ($gole_1 != null and $gole_2 != null) {
        // Dodawanie odpowiednich danych odpowiednim zespołom
        if ($gole_1 == $gole_2) {
            try {
                $sql = "UPDATE `$sezon_tabela`
                            SET `remisy` = `remisy` + 1, `pkt` = `pkt` + 1, `zdobyte` = `zdobyte` + '$gole_1', `stracone` = `stracone` + '$gole_2'
                            WHERE `numer` = '$d1' AND `grupa` = '$grupa'";
                PDOS::Instance()->exec($sql);
            } catch (PDOException $e) {
                reportError("db", $e->getMessage());
                header("Location: " . ADMIN_MAIN_URL);
                exit();
            }

            try {
                $sql = "UPDATE `$sezon_tabela`
                            SET `remisy` = `remisy` + 1, `pkt` = `pkt` + 1, `zdobyte` = `zdobyte` + '$gole_2', `stracone` = `stracone` + '$gole_1'
                            WHERE `numer` = '$d2' AND `grupa` = '$grupa'";
                PDOS::Instance()->exec($sql);
            } catch (PDOException $e) {
                reportError("db", $e->getMessage());
                header("Location: " . ADMIN_MAIN_URL);
                exit();
            }
        } elseif ($gole_1 > $gole_2) {
            try {
                $sql = "UPDATE `$sezon_tabela`
                            SET `zwyciestwa` = `zwyciestwa` + 1, `pkt` = `pkt` + 3, `zdobyte` = `zdobyte` + '$gole_1', `stracone` = `stracone` + '$gole_2'
                            WHERE `numer` = '$d1' AND `grupa` = '$grupa'";
                PDOS::Instance()->exec($sql);
            } catch (PDOException $e) {
                reportError("db", $e->getMessage());
                header("Location: " . ADMIN_MAIN_URL);
                exit();
            }

            try {
                $sql = "UPDATE `$sezon_tabela`
                            SET `przegrane` = `przegrane` + 1, `zdobyte` = `zdobyte` + '$gole_2', `stracone` = `stracone` + '$gole_1'
                            WHERE `numer` = '$d2' AND `grupa` = '$grupa'";
                PDOS::Instance()->exec($sql);
            } catch (PDOException $e) {
                reportError("db", $e->getMessage());
                header("Location: " . ADMIN_MAIN_URL);
                exit();
            }
        } elseif ($gole_1 < $gole_2) {
            try {
                $sql = "UPDATE `$sezon_tabela`
                            SET `przegrane` = `przegrane` + 1, `zdobyte` = `zdobyte` + '$gole_1', `stracone` = `stracone` + '$gole_2'
                            WHERE `numer` = '$d1' AND `grupa` = '$grupa'";
                PDOS::Instance()->exec($sql);
            } catch (PDOException $e) {
                reportError("db", $e->getMessage());
                header("Location: " . ADMIN_MAIN_URL);
                exit();
            }

            try {
                $sql = "UPDATE `$sezon_tabela`
                            SET `zwyciestwa` = `zwyciestwa` + 1, `pkt` = `pkt` + 3, `zdobyte` = `zdobyte` + '$gole_2', `stracone` = `stracone` + '$gole_1'
                            WHERE `numer` = '$d2' AND `grupa` = '$grupa'";
                PDOS::Instance()->exec($sql);
            } catch (PDOException $e) {
                reportError("db", $e->getMessage());
                header("Location: " . ADMIN_MAIN_URL);
                exit();
            }
        }
    }
}
