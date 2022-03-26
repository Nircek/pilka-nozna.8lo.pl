<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="pl-PL">

<head>
    <?php include('./szablon/meta.php'); ?>
    <title>PIK Piłka Nożna</title>

    <!----------------- STYLE CSS DOTYCZĄCE TYLKO TEJ PODSTRONY STRONY -------------------->
    <style>
    .info {
        width: 900px;
        margin: auto;
        margin-top: 30px;
        text-align: center;
        line-height: 150%;
    }

    #tytul {
        text-transform: uppercase;
        font-size: 25px;
        margin: auto;
        font-weight: bold;
        text-transform: uppercase;
    }

    #tresc {
        font-size: 18px;
        font-weight: normal;
    }

    #data {
        font-size: 15px;
        text-align: right;
    }
    </style>
</head>

<body>
    <div id="container">
        <?php include('./szablon/menu.php'); ?>

        <div id="content-border">
            <div id="content">
                <h1> INFORMACJE </h1>
                <?php
                                include("./skrypty/db-connect.php");

                                try    {
                                    $result = $pdo->query("SELECT * FROM informacje ORDER BY id DESC");
                                } catch (PDOException $e) {
                                    echo 'Błąd bazy danych: ' . $e;
                                }
                                $info = array();
                                while ($row = $result->fetch())
                                    $info[] = array('id' => $row['id'],
                                                    'tytul' => $row['tytul'],
                                                    'tresc' => $row['tresc'],
                                                    'data' => $row['data']);

                                foreach($info as $info)
                                    echo "<div class='info'>
                                            <span id='tytul'>" .
                                                $info['tytul'] .
                                            "</span>
                                            <br/>
                                            <span id='tresc'>" .
                                                $info['tresc'] .
                                            "</span>
                                            <br/>
                                            <span id='data'>" .
                                                $info['data'] . "
                                            </span>
                                          </div>";
                ?>
            </div>
        </div>

        <?php include('./szablon/footer.php'); ?>

    </div>
</body>

</html>
