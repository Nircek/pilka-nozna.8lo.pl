<?php generate_header("index"); ?>

<div id="content">
    <div id="columns">
        <div id="left-content">
            <h1> GALERIA </h1>
            <?php
            include(ROOT_PATH . "/funkcje/db-connect.php");
            // POBIERANIE ZDJĘĆ Z BAZY
            try {
                $sql = "SELECT `sciezka` FROM `zdjecia` ORDER BY RAND() LIMIT 4";
                $result = $pdo->query($sql);
            } catch (PDOException $e) {
                echo '<div id="error">Błąd bazy danych: ' . $e . '</div>';
            }
            $zdjecie = array();
            while ($row = $result->fetch()) {
                $zdjecie[] = array('sciezka' => $row['sciezka']);
            }

            foreach ($zdjecie as $zdjecie) :
            ?>
                <div class='image'>
                    <img src='<?= PREFIX ?>/<?= $zdjecie['sciezka'] ?>' width='192' />"
                    <!--wysokość auto. Nadwyżka zostanie ucięta-->
                </div>
            <?php endforeach; ?>
            <div id="image-button">
                <a href="<?= PREFIX ?>/galeria"><br /> ... </a>
            </div>
        </div>
        <div id="center-content">
            <h1> INFORMACJE </h1>
            <div id="informacje-content">
                <?php
                // POBIERANIE INFORMACJI Z BAZY
                try {
                    $sql = "SELECT * FROM informacje ORDER BY id DESC";
                    $result = $pdo->query($sql);
                } catch (PDOException $e) {
                    echo '<div id="error">Błąd bazy danych: ' . $e . '</div>';
                }

                $info = array();
                while ($row = $result->fetch()) {
                    $info[] = array(
                        'id' => $row['id'],
                        'tytul' => $row['tytul'],
                        'tresc' => $row['tresc'],
                        'data' => $row['data']
                    );
                }

                foreach ($info as $info) :
                ?>
                    <div class='info'>
                        <h3> <?= $info['tytul'] ?> </h3>
                        <span id='tresc'>
                            <?= $info['tresc'] ?>
                        </span>
                        <br />
                        <div id='data'>
                            <?= $info['data'] ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div id="info-button">
                <a href="<?= PREFIX ?>/informacje"><br />...</a>
            </div>
        </div>
        <div id="right-content">
            <?php
            include(ROOT_PATH . "/funkcje/funkcje.php");

            try {
                $sql = "SELECT * FROM sezony ORDER BY sezon DESC LIMIT 1";
                $result = $pdo->query($sql);
                $liczba = $result->rowCount();
                if ($liczba == 0) {
                    echo "<div id='error'> Nie ma żadnego sezonu... </div>";
                }
            } catch (PDOException $e) {
                echo "<div id='error'> $e </div>";
            }
            $sezon = array();
            while ($row = $result->fetch()) {
                $sezon[] = array('sezon' => $row['sezon']);
            }

            if ($liczba != 0) :
                foreach ($sezon as $sezon) :
                    $sezon = $sezon['sezon'];
            ?>
                    <h2> TABELA <?= $sezon ?>/<?= $sezon + 1 ?> </h2>
                <?php
                    $sezon_tabela = "${sezon}_tabela";
                endforeach;
                ?>
                <h3> GRUPA PIERWSZA </h3>
                <?php show_tabela(2, $sezon_tabela, 1); ?>
                <h3> GRUPA DRUGA </h3>
                <?php show_tabela(2, $sezon_tabela, 2); ?>
            <?php endif; ?>
        </div>
        <div style="clear: both;"></div>
    </div>
</div>

<?php generate_footer(); ?>
