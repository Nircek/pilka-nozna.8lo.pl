<?php generate_header("galeria"); ?>

<div id="content">
    <?php if (isset($_GET['s'])) : ?>
        <div id="powrot"><a href="<?= PREFIX ?>/galeria"> &#8592; POWRÓT </a></div>';
    <?php endif; ?>
    <h1> GALERIA </h1>
    <div style="clear: both;"></div>
    <?php
    // sprawdzanie czy użytkownik wybrał, któryś sezon
    if (isset($_GET['s'])) {
        $sezon = $_GET['s'];
        // Sezon zapisywany w bazie to tylko jego liczba początkowa
        // 2016/2017 to tylko 2016, dlatego $_GET musi rozdzielić te dwie liczby
        $sezon_arr = explode("/", $sezon);
        $sezon = $sezon_arr[0];

        // Nawiązywanie połączenia z bazą
        include(ROOT_PATH . "/funkcje/db-connect.php");

        // Pobieranie z bazy sciezke zdjęć z danego sezonu
        try {
            // Wysyłanie zapytania
            // $sql = "SELECT * FROM zdjecia WHERE sezon = '$sezon' ORDER BY data ";
            // $result = $pdo->query($sql);

            $sql = $pdo->prepare("SELECT * FROM zdjecia WHERE sezon=? ORDER BY data");
            $sql->bindValue(1, $sezon);

            $sql->execute();
            $liczba_zdjec = $sql->rowCount();
        } catch (PDOException $e) {
            echo "Błąd bazy danych: $e </div>";
        }
        // Przypisanie każdej sciezce klucza $i++ gdzie $i to kolejna liczba całkowita
        for ($i = 0; $row = $sql->fetch(PDO::FETCH_ASSOC); $i++) {
            $zdjecie[] = array("$i" => $row['sciezka']);
        }

        // Jeśli są:
        // Wyświetla na samej górze jedno duże zdjęcie 'podglad'
    ?>
        <div id='podglad'>
            <div id='lewo' onclick='lewo()'></div>
            <img id='glowne_zdjecie' src='<?= $zdjecie[0][0] ?>' value='' />
            <div id='prawo' onclick='prawo()'></div>
            <div style='clear: both'></div>
        </div>
        <?php
        // Wypisywanie wszystkich zdjęć wraz z odpowiadającymi im ścieżkami
        $i = 0;
        foreach ($zdjecie as $zdjecie) {
            // W 'id' i skrypcie 'laduj()' znajduje się taka sama liczba przez co JS może ją stąd pobrać
            // Jak pobierze liczbę w ID to od razu zna liczbę sciezki przez co może ją dopasować i podmienić w zdjęciu na 'podgladzie'
            $pathinf = pathinfo($zdjecie[$i]);
        ?><div class='zdjecie'>
                <img width='172' id='<?= $i ?>' height='98' src='<?= $pathinf['dirname'] ?>/thumb<?= $pathinf['basename'] ?>' srcfull='<?= $zdjecie[$i] ?>' onclick='laduj($i)' />
            </div>
        <?php
            $i++;
        }
        ?>
        <div style='clear: both;'></div>
        <?php
    } else {
        // Jeśli nie wybrano jeszcze sezonu to wyświetla się menu, z pobranymi z bazy danych wszystkimi sezonami

        include(ROOT_PATH . "/funkcje/db-connect.php");
        // Pobieranie z bazy wszystkich sezonów (2014/2015 itp...)
        try {
            $sql = "SELECT DISTINCT    sezon FROM zdjecia ORDER BY sezon DESC";
            $result = $pdo->query($sql);
        } catch (PDOException $e) {
            echo '<div id="error">Błąd bazy danych: ' . $e . '</div>';
        }

        $sezon = array();
        while ($row = $result->fetch()) {
            $sezon[] = array('sezon' => $row['sezon']);
        }

        // Składanie "kafelka" z odnośnikiem do galerii danego sezonu
        foreach ($sezon as $sezon) :
        ?>
            <div class='sezon'>
                <a href='?s=<?= $sezon['sezon'] ?>/<?= $sezon['sezon'] + 1 ?>'>
                    <?= $sezon['sezon'] ?>/<?= $sezon['sezon'] + 1 ?>
                </a>
            </div>
        <?php endforeach; ?>
        <div style="clear: both;"></div>
    <?php
    }
    ?>
</div>
<script src="<?= PREFIX ?>/js/galeria.js"></script>

<?php generate_footer(); ?>
