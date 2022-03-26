<?php
    is_logged();

    include("./skrypty/db-connect.php");

    $_SESSION['krok'] = 3;
    $sezon = $_SESSION['sezon'];
    $sezon_tabela = $sezon . "_tabela";
    $sezon_terminarz = $sezon . "_terminarz";

    if(isset($_POST['czy_wyslano'])) {

    //------------------------------- PRZYPORZĄDKOWANIE GRUPY PIERWSZEJ ----------------------//
        $liczba_meczy_g1 = $_POST['numer_g1'];
        for($i=1; $i < $liczba_meczy_g1; $i++) {
            $termin = $_POST["termin_g1_$i"];
            try {
                //Przyporządkowanie terminu do danego meczu
                $sql =  "UPDATE `$sezon_terminarz` SET `termin` = '$termin' WHERE `id`='$i'";
                $stmt = $pdo->prepare($sql);
                $stmt->execute();
            } catch(PDOException $e) {
                unset($_SESSION['przeladowanie']);
                $_SESSION['e_terminarz_baza'] = "Błąd bazy danych: $e";
                $_SESSION['krok'] = 3;
                header('Location: admin_sezon.php');
                exit();
            }
        }

    //------------------------------- PRZYPORZĄDKOWANIE GRUPY DRUGIEJ ----------------------//
        $liczba_meczy_g2 = $_POST['numer_g2'];
        for($i=$liczba_meczy_g1; $i <= $liczba_meczy_g2; $i++) {
            $termin = $_POST["termin_g2_$i"];
            try {
                //Przyporządkowanie terminu do danego meczu
                $stmt = $pdo->prepare("UPDATE `$sezon_terminarz` SET `termin` = '$termin' WHERE `id`='$i'");
                $stmt->execute();
            } catch(PDOException $e) {
                unset($_SESSION['przeladowanie']);
                $_SESSION['e_terminarz_baza'] = "Błąd bazy danych: $e";
                $_SESSION['krok'] = 3;
                header('Location: admin_sezon.php');
                exit();
            }
        }

        try {
            //OSTATECZNE WPISANIE NOWEGO SEZONU NA LISTĘ SEZONÓW
            $stmt = $pdo->prepare("INSERT INTO `sezony` (`id`, `sezon`) VALUES (NULL, '$sezon')");
            $stmt->execute();
        } catch(PDOException $e) {
            unset($_SESSION['przeladowanie']);
            $_SESSION['e_terminarz_baza'] = "Błąd bazy danych: $e";
            $_SESSION['krok'] = 3;
            header('Location: admin_sezon.php');
            exit();
        }

        unset($_SESSION['przeladowanie']);
        unset($_SESSION['krok']);
        $_SESSION['sezon_sukces'] = "Utworzono nowy sezon!";
        header('Location: admin.php');
        exit();
    } else {
        //Sprawdzenie czy strona została przeładowana
        if(isset($_SESSION['przeladowanie'])) {
            unset($_SESSION['przeladowanie']);

            //Powrót do KROKU 1
            $_SESSION['krok'] = 1;
            header('Location: admin_sezon.php');
            exit();
        }
        $_SESSION['przeladowanie'] = 1;
    }
    $_SESSION['krok'] = 3;
?>






<!-------------------------- TWORZENIE SEZONU KROK 3 ------------------------>
<h2><?php echo "Sezon: ". $sezon ."/". ($sezon+1); ?></h2>
<h3>WYZNACZ TERMINY (Jeśli nieokreślony to nie zaznaczaj nic)</h3>

<?php
    //Sprawdzenie wkładanie terminu do bazy waliło jakieś błędy
    if(isset($_SESSION['e_terminarz_baza']) == true) {
        //Nie podana wszystkich drużyn
        echo '<div id="error">' . $_SESSION['e_terminarz_baza'] . '</div><br/>';
        unset($_SESSION['e_terminarz_baza']);
    }
?>
<form method="post" action="#" c>
    <div id="grupa-pierwsza">
        <h2> GRUPA PIERWSZA </h2>
        <table cellspacing="0">
            <tr>
                <th>KTO?</th>
                <th>KIEDY?</th>
            </tr>
            <?php

            $liczba_druzyn_g1 = $_SESSION['liczba_druzyn_g1'];
            $liczba_druzyn_g2 = $_SESSION['liczba_druzyn_g2'];

    /* ------------------------------------ TWORZENIE TABELI TERMINARZU ---------------------- */
            try
            {
                //Wszystkiego jest po dwie (1_... ; 2_...), bo to mecze, więc muszą być dwie drużyny.
                $sql = "CREATE TABLE `$sezon_terminarz` (
                            `id` int NOT NULL AUTO_INCREMENT,
                            `1_num` int," . //Numer "wygenerowany" przez skrypt sortujący dla pierwszej drużyny z pary
                            "`2_num` int," . //Numer drugiej drużyny
                            "`1_text` text," . //Nazwa słowna pierwszej drużyny, która na podstawie numeru pobierana jest z tabeli $sezon_tabela
                            "`2_text` text," . //Nazwa drugiej drużyny
                            "`1_wynik` int null," . //Wynik (liczba bramek) pierwszej drużyny z pary
                            "`2_wynik` int null," . //Wynik drugiej drużyny
                            "`termin` date null," . //Ustalony termin meczu
                            "`grupa` int," . //Numer grupy drużyny
                             "PRIMARY KEY(id)
                            )";
                $stmt = $pdo->prepare($sql);
                $stmt->execute();
            }
            catch(PDOException $e)
            {
                echo "<div id='error'> Błąd bazy danych:" . $e . "</div>";
            }

    /* ----------------------------------- LOSOWANIE GRUPY PIERWSZEJ ------------------------- */
            $druzyny = $liczba_druzyn_g1;
            #liczba początkowa (1 drużyna)
            $i = 1;
            #pętla zewnętrzna zmieniająca 'pierwszą drużynę czyli $a' z pary dwóch drużyn
            for($a = 1; $a <= $druzyny - 1; $a++)
            {
                #druga drużyna z pary... Dodajemy 1 żeby drużyna nie trafiła na samą siebie
                $i++;

                #musimy stworzyć tymczasową drużynę drugą ($i_tym) żeby pętla wewnętrzna nie zepsuła nam zmiennych w pętli zew.
                #pętla while przy każdym przejściu będzie ją nadpisywała jednak tutaj zawsze powróci do stanu zmiennej $i.
                $i_tym = $i;

                #pętla zewnętrzna zmienia drugą drużynę czyli $tym_i tak długo aż $tym_i będzie równe liczbie drużyn
                while($i_tym <= $druzyny)
                {
                    try
                    {
                        //Wkładanie do bazy danych pierwszego zespołu z pary do '1_num' i drugiego do '2_num'
                        $sql = "INSERT INTO `$sezon_terminarz` (`id`, `1_num`, `2_num` , `grupa`) VALUES (NULL, '$a', '$i_tym', '1')";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute();
                    }
                    catch(PDOException $e)
                    {
                        echo "<div id='error'> Błąd bazy danych:" . $e . "</div>";
                    }
                    #dodaje 1 do liczby drużyn i ponawia pentlę
                    $i_tym++;
                }
                /*w tym momencie mamy wypisaną jedną liczbę $a czyli pierwszą drużynę i wszystkie pasujące do niej drugie drużyny takie, że
                    $i != $a oraz $i <= $druzyny */
            }

    /* ----------------------------------- LOSOWANIE GRUPY DRUGIEJ ------------------------- */
            $druzyny = $liczba_druzyn_g2;

            $i = 1;
            for($a = 1; $a <= $druzyny - 1; $a++)
            {
                $i++;
                $i_tym = $i;

                while($i_tym <= $druzyny)
                {
                    try
                    {
                        $sql = "INSERT INTO `$sezon_terminarz` (`id`, `1_num`, `2_num` , `grupa`) VALUES (NULL, '$a', '$i_tym', '2')";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute();
                    }
                    catch(PDOException $e)
                    {
                        echo "<div id='error'> Błąd bazy danych:" . $e . "</div>";
                    }

                    $i_tym++;
                }
            }

    /* --------------------- PRZYPORZĄDKOWANIE NAZW DO NUMERÓW GRUPY PIERWSZEJ -------------- */
            //Wklejanie zespołów odpowiadających liczbom wygenerowanym przez powyższy algorytm sortujący
            try
            {
                //1. Pobieranie nazwy zespołu z tabeli $sezon_tabela, którego 'numer' równa się '1_num' i 'grupa' = 1
                //2. wkładanie ich do '1_text'
                $sql = "UPDATE `$sezon_terminarz` t1
                            INNER JOIN `$sezon_tabela` t2
                                ON t1.1_num = t2.numer
                            SET t1.1_text = t2.nazwa
                            WHERE t2.grupa = 1 AND t1.grupa=1";
                $stmt = $pdo->prepare($sql);
                $stmt->execute();

                $sql = "UPDATE `$sezon_terminarz` t1
                            INNER JOIN `$sezon_tabela` t2
                                ON t1.2_num = t2.numer
                            SET t1.2_text = t2.nazwa
                            WHERE t2.grupa = 1 AND t1.grupa=1";
                $stmt = $pdo->prepare($sql);
                $stmt->execute();
            }
            catch(PDOExcpetion $e)
            {
                echo "<div id='error'> Błąd bazy danych:" . $e . "</div>";
            }
    /* --------------------- PRZYPORZĄDKOWANIE NAZW DO NUMERÓW GRUPY DRUGIEJ -------------- */
            try
            {
                $sql = "UPDATE `$sezon_terminarz` t1
                            INNER JOIN `$sezon_tabela` t2
                                ON t1.1_num = t2.numer
                            SET t1.1_text = t2.nazwa
                            WHERE t2.grupa = 2 AND t1.grupa=2";
                $stmt = $pdo->prepare($sql);
                $stmt->execute();

                $sql = "UPDATE `$sezon_terminarz` t1
                            INNER JOIN `$sezon_tabela` t2
                                ON t1.2_num = t2.numer
                            SET t1.2_text = t2.nazwa
                            WHERE t2.grupa = 2 AND t1.grupa=2";
                $stmt = $pdo->prepare($sql);
                $stmt->execute();
            }
            catch(PDOExcpetion $e)
            {
                echo "<div id='error'> Błąd bazy danych:" . $e . "</div>";
            }
    /* ------------------------------ WYPISYWANIE TERMINARZU GRUPY PIERWSZEJ ------------------ */
            //Gdy już mamy wszystko co potrzebne następuje ostateczne wybieranie wszystkiego z tabeli $sezon_terminarz
            try
            {
                $sql = "SELECT * FROM `$sezon_terminarz` WHERE grupa=1";
                $result = $pdo->query($sql);
            }
            catch(PDOException $e)
            {
                echo "<div id='error'> Błąd bazy danych:" . $e . "</div>";
            }
            while($row = $result->fetch())
            {
                $druzyny_g1[] = array('pierwsza' => $row['1_text'], 'druga' => $row['2_text']);
            }

            $numer_g1=1;
            foreach($druzyny_g1 as $druzyny_g1)
            {
                echo "<tr>
                            <td>". $druzyny_g1['pierwsza'] ." vs ". $druzyny_g1['druga'] ."</td>
                            <td> <input type='date' name='termin_g1_$numer_g1' id='termin'> </td>
                        </tr> ";
                $numer_g1++;
            }
            echo "<input type='hidden' name='numer_g1' value='$numer_g1'>";
        ?>
        </table>


    </div>
    <div id="grupa-druga">
        <h2> GRUPA DRUGA </h2>
        <table cellspacing="0">
            <tr>
                <th>KTO?</th>
                <th>KIEDY?</th>
            </tr>
            <?php
/* ------------------------------ WYPISYWANIE TERMINARZU GRUPY DRUGIEJ ------------------ */
        try
        {
            $sql = "SELECT * FROM $sezon_terminarz WHERE grupa=2";
            $result = $pdo->query($sql);
        }
        catch(PDOException $e)
        {
            echo "<div id='error'> Błąd bazy danych:" . $e . "</div>";
        }
        while($row = $result->fetch())
        {
            $druzyny_g2[] = array('pierwsza' => $row['1_text'], 'druga' => $row['2_text']);
        }

        $numer_g2=$numer_g1;
        foreach($druzyny_g2 as $druzyny_g2)
        {
            echo "<tr>
                        <td>". $druzyny_g2['pierwsza'] ." vs ". $druzyny_g2['druga'] ." </td>
                        <td> <input type='date' name='termin_g2_$numer_g2' id='termin'> </td>
                    </tr>";
            $numer_g2++;
        }
        echo "<input type='hidden' name='numer_g2' value='$numer_g2'>";
    ?>
        </table>
    </div>
    <div style="clear: both;"></div>
    <input type="hidden" name="czy_wyslano" value="WYSŁANO">
    <input type="submit" value="AKCEPTUJĘ SEZON!" id="akceptuje-sezon">
</form>
