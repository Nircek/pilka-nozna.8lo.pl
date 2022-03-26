<?php
session_start();
include('./funkcje/funkcje_admin.php');
is_logged();
?>
<!DOCTYPE html>
<html lang="pl-PL">

<head>

    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="style/szablon.css">
    <link rel="stylesheet" type="text/css" href="fontello/css/peak.css">
    <link href="https://fonts.googleapis.com/css?family=Monda:400,700&amp;subset=latin-ext" rel="stylesheet">
    <link rel="icon" type="image/png" href="img/logo.png">
    <meta name="robots" content="noindex" />

    <title> PIK Piłka Nożna </title>
    <!------------------ STYLE CSS DOTYCZĄCE TYLKO TEJ PODSTRONY STRONY ------------------>
    <link rel="stylesheet" type="text/css" href="style/admin.css">
    <style>
        #grupa-pierwsza,
        #grupa-druga {
            margin-top: 10px;
        }

        #grupa-pierwsza {
            float: left;
            width: 50%;
        }

        #grupa-druga {
            float: left;
            width: 50%;
            background-color: red;
            height: 300px;
        }

        table {
            margin: auto;
            border-spacing: 0;
            border-collapse: collapse;
            width: 400px;
            text-align: center;
            background-color: rgba(0, 0, 0, 0.4);
        }

        th {
            background-color: #ffffff;
            color: #000000;
            font-weight: bold;
        }

        th,
        td {
            border: black solid 2px;
        }

        #termin {
            width: 150px;
        }
    </style>
</head>

<body>

    <div id="container">

        <?php include('./szablon/menu.php'); ?>

        <div id="content-border">
            <div id="content">
                <h1> WYGENEROWANY TERMINARZ SEZONU <?= $_SESSION['sezon'] ?> </h1>

                <div id="grupa-pierwsza">
                    <h2> GRUPA PIERWSZA </h2>
                    <table>
                        <tr>
                            <th> KTO? </th>
                            <th> KIEDY? </th>
                        </tr>
                        <?php
                        // Liczba drużyn w pierwszej grupie.
                        $g_pierwsza = $_SESSION['g_pierwsza'];
                        // Sezon podany przez admina.
                        $sezon = $_SESSION['sezon'];
                        // Tworzenie nazwy tabeli z punktacją i statami.
                        $sezon_tabela = "${sezon}_tabela";
                        // Tworzenie nazwy tabeli z meczami, wynikami i terminarzem.
                        $sezon_terminarz = "${sezon}_terminarz";

                        // Nawiązywanie połączenia z bazą
                        include("./skrypty/db-connect.php");

                        // Tworzenie tabeli gdzie trzymane będą wyniki poszczególnych meczów.
                        try {
                            // Wszystkiego jest po dwie (1_... ; 2_...), bo to mecze, więc muszą być dwie drużyny.
                            $sql = "CREATE TABLE `$sezon_terminarz` (" .
                                "`id` int NOT NULL AUTO_INCREMENT," .
                                "`1_num` int," . // Numer "wygenerowany" przez skrypt sortujący dla pierwszej drużyny z pary
                                "`2_num` int," . // Numer drugiej drużyny
                                "`1_text` text," . // Nazwa słowna pierwszej drużyny, która na podstawie numeru pobierana jest z tabeli $sezon_tabela
                                "`2_text` text," . // Nazwa drugiej drużyny
                                "`1_wynik` int," . // Wynik (liczba bramek) pierwszej drużyny z pary
                                "`2_wynik` int," . // Wynik drugiej drużyny
                                "PRIMARY KEY(id)" .
                                ")";
                            $stmt = $pdo->prepare($sql);
                            $stmt->execute();
                        } catch (PDOException $e) {
                            echo "<div id='error'> Błąd bazy danych: $e </div>";
                        }

                        // liczba drużyn w rozgrywkach
                        $druzyny = $g_pierwsza;
                        // liczba początkowa (1 drużyna)
                        $i = 1;
                        // pętla zewnętrzna zmieniająca 'pierwszą drużynę czyli $a' z pary dwóch drużyn
                        for ($a = 1; $a <= $druzyny - 1; $a++) {
                            // druga drużyna z pary... Dodajemy 1 żeby drużyna nie trafiła na samą siebie
                            $i++;

                            // musimy stworzyć tymczasową drużynę drugą ($i_tym) żeby pętla wewnętrzna nie zepsuła nam zmiennych w pętli zew.
                            // pętla while przy każdym przejściu będzie ją nadpisywała jednak tutaj zawsze powróci do stanu zmiennej $i.
                            $i_tym = $i;

                            // pętla zewnętrzna zmienia drugą drużynę czyli $tym_i tak długo aż $tym_i będzie równe liczbie drużyn
                            while ($i_tym <= $druzyny) {
                                try {
                                    // Wkładanie do bazy danych pierwszego zespołu z pary do '1_num' i drugiego do '2_num'
                                    $sql = "INSERT INTO `$sezon_terminarz` (`id`, `1_num`, `2_num`) VALUES (NULL, '$a', '$i_tym')";
                                    $stmt = $pdo->prepare($sql);
                                    $stmt->execute();
                                } catch (PDOException $e) {
                                    echo "<div id='error'> Błąd bazy danych: $e </div>";
                                }
                                echo " $a vs $i_tym";
                                // dodaje 1 do liczby drużyn i ponawia pentlę
                                $i_tym++;
                            }
                            // w tym momencie mamy wypisaną jedną liczbę $a czyli pierwszą drużynę i wszystkie pasujące
                            // do niej drugie drużyny takie, że $i != $a oraz $i <= $druzyny
                        }

                        // Wklejanie zespołów odpowiadających liczbom wygenerowanym przez powyższy algorytm sortujący
                        try {
                            // 1. Pobieranie nazwy zespołu z tabeli $sezon_tabela, którego 'numer' równa się '1_num' i 'grupa' = 1
                            // 2. wkładanie ich do '1_text'
                            $sql = "UPDATE $sezon_terminarz t1
                                        INNER JOIN $sezon_tabela t2
                                            ON t1.1_num = t2.numer
                                        SET t1.1_text = t2.nazwa
                                        WHERE t2.grupa = 1";
                            $stmt = $pdo->prepare($sql);
                            $stmt->execute();

                            // 1. Pobieranie nazwy zespołu z tabeli $sezon_tabela, którego 'numer' równa się '2_num' i 'grupa' = 1
                            // 2. wkładanie ich do '2_text'
                            $sql = "UPDATE $sezon_terminarz t1
                                        INNER JOIN $sezon_tabela t2
                                                ON t1.2_num = t2.numer
                                        SET t1.2_text = t2.nazwa
                                        WHERE t2.grupa = 1";
                            $stmt = $pdo->prepare($sql);
                            $stmt->execute();
                        } catch (PDOException $e) {
                            echo "<div id='error'> Błąd bazy danych: $e </div>";
                        }

                        // Gdy już mamy wszystko co potrzebne następuje stateczne wybieranie wszystkiego z tabeli $sezon_terminarz
                        try {
                            $sql = "SELECT * FROM $sezon_terminarz";
                            $result = $pdo->query($sql);
                        } catch (PDOException $e) {
                            echo "<div id='error'> Błąd bazy danych: $e </div>";
                        }

                        while ($row = $result->fetch()) {
                            $druzyny_g1[] = array('pierwsza' => $row['1_text'], 'druga' => $row['2_text']);
                        }
                        // Wypisywanie pierwsza drużyny z pary vs druga drużyna z pary
                        // Wszystko w porządku od góry tabeli do dołu.
                        $i = 1;
                        foreach ($druzyny_g1 as $druzyny_g1) {
                        ?>
                            <tr>
                                <td> <?= $druzyny_g1['pierwsza'] ?> vs <?= $g1_druzyny['druga'] ?> </td>
                                <td> <input type='datetime' name='mecz_<?= $i ?>' id='termin'> </td>
                            </tr> ";
                        <?php
                            $i++;
                        }
                        ?>
                    </table>
                </div>
                <div id="grupa-druga">
                    <h2> GRUPA DRUGA </h2>
                </div>
                <div style="clear: both;"></div>
            </div>
        </div>

        <?php include('./szablon/footer.php'); ?>

    </div>
</body>

</html>
