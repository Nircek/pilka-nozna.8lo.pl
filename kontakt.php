<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pl-PL">

<head>
    <?php include('./szablon/meta.php'); ?>
    <title> PIK Piłka Nożna </title>
    <!------------------ STYLE CSS DOTYCZĄCE TYLKO TEJ PODSTRONY STRONY ------------------>
    <style>
        #kontakt {
            margin-top: 15px;
            font-size: 20px;
            line-height: 130%;
            font-weight: normal;
        }

        #fanpage-content {
            margin-top: 20px;
            font-size: 20px;
            line-height: 130%;
        }

        #facebook-link a {
            -webkit-transition: background-color 0.15s linear 0s;
            transition: background-color 0.15s linear 0s;
            text-decoration: none;
            background-color: rgba(0, 0, 0, 0.35);
            color: #3c5a9a;
            display: block;
            width: 500px;
            height: 30px;
            padding: 10px;
            font-size: 25px;
            margin: auto;
            margin-top: 20px;
        }

        #facebook-link a:hover {
            background-color: #3c5a9a;
            color: #ffffff;
        }
    </style>
</head>

<body>
    <div id="container">
        <?php include('./szablon/menu.php'); ?>

        <div id="content-border">
            <div id="content">
                <h1> KONTAKT </h1>
                <div id="kontakt">
                    Wszelkie pytania, sugestie i propozycje należy kierować do prof. Jacka Burzyńskiego.
                </div>
                <div id="fanpage-content">
                    Można skontaktować się z nami również poprzez nasz oficjalny fanpage na facebook'u:<br />
                    <div id="facebook-link">
                        <a target="_blank" href="https://www.facebook.com/Pi%C5%82ka-no%C5%BCna-VIII-LO-w-Katowicach-182879961837032/">
                            facebook.com/PILKA-NOZNA-8-LO
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <?php include('./szablon/footer.php'); ?>

    </div>
</body>

</html>
