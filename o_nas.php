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
    h3 {
        font-weight: normal;
        letter-spacing: 0.3px;
        line-height: 120%;
    }
    </style>
</head>

<body>
    <div id="container">
        <?php  include('./szablon/menu.php'); ?>

        <div id="content-border">
            <div id="content">
                <h1> O NAS </h1>
                <h3>
                    Rozgrywki piłki nożnej w naszej szkole odbywają się co roku od wielu lat.
                    Mają one na celu popularyzację piłki nożnej wśród młodzieży, wdrażanie zasad fair-play oraz
                    organizację czasu wolnego. Zdjęcia z naszych turniejów można zobaczyć w dziale "galeria".
                    Aby uzykaś, więcej informacji o rozgrywak z poprzednich lat zapraszamy do działu "WSZYSTKIE SEZONY"
                    do zakładki "ARCHIWUM".
                </h3>

            </div>
        </div>

        <?php include('./szablon/footer.php'); ?>
    </div>
</body>

</html>
