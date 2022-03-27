<?php
is_logged();

// Check if form has been send
if (isset($_POST['grupa-pierwsza'])) {
    $liczba_druzyn_g1 = $_POST['grupa-pierwsza'];
    $_SESSION['liczba_druzyn_g1'] = $_POST['grupa-pierwsza'];
    unset($_POST['grupa-pierwsza']);
    $liczba_druzyn_g2 = $_POST['grupa-druga'];
    $_SESSION['liczba_druzyn_g2'] = $_POST['grupa-druga'];
    unset($_POST['grupa-druga']);

    // ------------------ SPRAWDZANIE PÓL GRUPY PIERWSZEJ ------------------

    // Sprawdzanie czy każda drużyna grupy pierwszej jest przypisana i wpisywanie wszystkiego do jednej tablicy
    for ($i = 1; $i <= $liczba_druzyn_g1; $i++) {
        // Sprawdzanie każdego elementu wysłanego formularzem.
        // Każdy element ma name=g1-$i gdzie $i to kolejna liczba całkowita począwszy od 1 do końca czyli liczby wszystkich drużyn danej grupy
        if (empty($_POST["g1-$i"])) {
            // Usuwanie kontroli przeładowania strony, bo chcemy pozostać na KROKU 2, żeby naprawić błędy
            unset($_SESSION['przeladowanie']);
            $_SESSION['e_druzyny_pola'] = "Wszystkie pola są wymagane!";
            $_SESSION['krok'] = 2;
            header('Location: admin_sezon.php');
            exit();
        }
        // Wkładanie każdego wcześniej sprawdzonego elementu do tablicy
        $druzyny_g1[$i - 1] = $_POST["g1-$i"];
    }

    // ------------------ SPRAWDZANIE PÓL GRUPY DRUGIEJ ------------------

    // Identycznie jak w grupie pierwszej (wyżej)
    for ($i = 1; $i <= $liczba_druzyn_g2; $i++) {
        if (empty($_POST["g2-$i"])) {
            $_SESSION['e_nazwy_druzyn'] = "Wszystkie pola są wymagane!";
            header('Location: admin_sezon.php');
            exit();
        }
        $druzyny_g2[$i - 1] = $_POST["g2-$i"];
    }

    // ------------------ TWORZENIE BAZY NA TE WSZYSTKIE DRUŻYNY ------------------
    include('./skrypty/db-connect.php');

    // Tworzenie tabeli sezonu
    $sezon_tabela = $_SESSION['sezon'] . "_tabela";
    try {
        $sql = "CREATE TABLE `$sezon_tabela` (
                        `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                        `nazwa` text null,
                        `numer` int not null,
                        `grupa` int not null,
                        `pkt` int not null,
                        `zwyciestwa` int not null,
                        `remisy` int not null,
                        `przegrane` int not null,
                        `zdobyte` int not null,
                        `stracone` int not null
                    ) ENGINE=InnoDB;";
        $pdo->exec($sql);
    } catch (PDOException $e) {
        $_SESSION['e_druzyny_baza'] = "Błąd bazy danych: $e";
        header('Location: admin_sezon.php');
        exit();
    }

    // ------------------ WKŁADANIE GRUPY PIERWSZEJ DO BAZY ------------------
    for ($i = 1; $i <= $liczba_druzyn_g1; $i++) {
        try {
            $sql = "INSERT INTO `$sezon_tabela` (
                        `id`, `nazwa`, `numer`, `grupa`, `pkt`, `zwyciestwa`,
                        `remisy`, `przegrane`, `zdobyte`, `stracone`
                    ) VALUES (
                        NULL, '" . $druzyny_g1[$i - 1] . "', '$i', '1', '0', '0', '0', '0', '0', ''
                    )";
            $pdo->exec($sql);
        } catch (PDOException $e) {
            $_SESSION['e_db'] = "Błąd bazy danych: $e";
            header('Location: ../admin.php');
            exit();
        }
    }

    // ------------------ WKŁADANIE GRUPY DRUGIEJ DO BAZY ------------------
    for ($i = 1; $i <= $liczba_druzyn_g2; $i++) {
        try {
            $sql = "INSERT INTO `$sezon_tabela` (
                        `id`, `nazwa`, `numer`, `grupa`, `pkt`, `zwyciestwa`,
                        `remisy`, `przegrane`, `zdobyte`, `stracone`
                    ) VALUES (
                        NULL, '" . $druzyny_g2[$i - 1] . "', '$i', '2', '0', '0', '0', '0', '0', ''
                    )";
            $pdo->exec($sql);
        } catch (PDOException $e) {
            $_SESSION['e_db'] = "Błąd bazy danych: $e";
            header('Location: ../admin.php');
            exit();
        }
    }

    // Usuwanie zmiennej, żeby można było jej użyć w KROKU 3
    unset($_SESSION['przeladowanie']);
    // Jeśli wszystko jest poprawne (wcześniej nas nie wywaliło) to przechodzimy do KROKU 3
    $_SESSION['krok'] = 3;
    header('Location: admin_sezon.php');
    exit();
} else {
    // Formularz nie został wysłany, więc...
    // Sprawdzenie czy strona została przeładowana
    if (isset($_SESSION['przeladowanie'])) {
        // Niszczenie tej zmiennej żeby nie namieszała później
        unset($_SESSION['przeladowanie']);
        // Powrót do KROKU 1
        $_SESSION['krok'] = 1;
        header('Location: admin_sezon.php');
        exit();
    }

    // Ta zmienna ma za zadanie stwierdzić czy strona została przeładowana typu 'F5'
    // Tworzę ją po jej sprawdzeniu, które wykona się po przeładowaniu strony
    $_SESSION['przeladowanie'] = 1;

    // Zapisuję dane odebrane z KROKU 1
    $sezon = $_SESSION['sezon'];
    $liczba_druzyn = $_SESSION['liczba_druzyn'];
}
$_SESSION['krok'] = 2;

?>
<!------------------ TWORZENIE SEZONU KROK 2 ------------------>
<h2> Sezon: <?= $sezon ?>/<?= $sezon + 1 ?> </h2>
<form method="post" action="#">
    <?php
    // Validation errors
    if (isset($_SESSION['e_druzyny_pola'])) {
        // Nie podana wszystkich drużyn
        echo '<div id="error">' . $_SESSION['e_druzyny_pola'] . '</div><br/>';
        unset($_SESSION['e_druzyny_pola']);
    } elseif (isset($_SESSION['e_druzyny_baza'])) {
        // Nie podana wszystkich drużyn
        echo '<div id="error">' . $_SESSION['e_druzyny_baza'] . '</div><br/>';
        unset($_SESSION['e_druzyny_baza']);
    }

    // Sprawdzanie czy liczba drużyn jest podzielna przez 2
    if ($liczba_druzyn % 2 == 0) {
        // Jeśli tak to dzielimy ją po prostu na 2 grupy
        $liczba_druzyn_g1 = $liczba_druzyn / 2;
        $liczba_druzyn_g2 = $liczba_druzyn_g1;
    } else {
        // Jeśli nie to też dzielimy na 2 grupy, ale pierwszą zaokrąglamy w górę, a drugą w dół.
        $liczba_druzyn_g1 = $liczba_druzyn / 2;
        $liczba_druzyn_g1 = ceil($liczba_druzyn_g1);
        $liczba_druzyn_g2 = $liczba_druzyn / 2;
        $liczba_druzyn_g2 = floor($liczba_druzyn_g2);
    }
    ?>
    <div id="grupa-pierwsza">
        <h2> GRUPA PIERWSZA </h2>

        <?php for ($i = 1; $i <= $liczba_druzyn_g1; $i++) : // Wyświetlanie tyle pól dla grupy drugiej ile jest w niej drużyn
        ?>
            #<?= $i ?> <input maxlength='10' type='text' class='druzyny' name='g1-<?= $i ?>'><br /> <br />
        <?php endfor; ?>
        <!-- W polu hidden przesyłam liczbę drużyn grupy drugiej -->
        <input type='hidden' name='grupa-pierwsza' value='<?= $liczba_druzyn_g1 ?>'>
    </div>
    <div id="grupa-druga">
        <h2> GRUPA DRUGA</h2>

        <?php for ($i = 1; $i <= $liczba_druzyn_g2; $i++) : // Wyświetlanie tyle pól dla grupy drugiej ile jest w niej drużyn
        ?>
            #<?= $i ?> <input maxlength='10' type='text' class='druzyny' name='g2-<?= $i ?>'><br /> <br />
        <?php endfor; ?>
        <!-- W polu hidden przesyłam liczbę drużyn grupy drugiej -->
        <input type='hidden' name='grupa-druga' value='<?= $liczba_druzyn_g2 ?>'>
    </div>
    <div style="clear: both"></div>
    <input type="submit" id="submit" value="STWÓRZ!">
</form>
