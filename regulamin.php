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
        .punkt {
            font-size: 20px;
            margin-bottom: 15px;
            font-weight: normal;
            line-height: 110%;
        }

        #regulamin-content {
            text-align: left;
        }

        .podpunkt {
            margin-left: 40px;
            font-size: 18px;
            margin-top: 5px;
        }
    </style>
</head>

<body>
    <div id="container">
        <?php include('./szablon/menu.php'); ?>

        <div id="content-border">
            <div id="content">
                <h1> REGULAMIN ROZGRYWEK</h1>
                <div id="regulamin-content">
                    <ol>
                        <li class="punkt">
                            I. Organizatorami turnieju są uczniowie 8LO w Katowicach.
                        </li>
                        <li class="punkt">
                            II. Celem trunieju jest popularyzowanie piłki nożnej wśród młodzieży, wdrażanie
                            zasad fair play, oraz organizacja czasu wolnego.
                        </li>
                        <li class="punkt">
                            III. Miejscem rozgrywek jest boisko „SŁOWIAN” w Katowicach przy ul. 1 maja 99
                        </li>
                        <li class="punkt">
                            IV. W turnieju mogą uczestniczyć wszyscy uczniowie naszej szkoły.
                            Jeżeli jakaś klasa nie wystawi drużyny, pojedynczy zawodnik może zostać
                            dokooptowany do drużyny innej klasy i reprezentować ją przez cały turniej.
                        </li>
                        <li class="punkt">
                            V. Zespół składa się z dziesięciu zawodników.
                        </li>
                        <li class="punkt">
                            VI. Mecze odbywają się w następującym systemie:
                            <ul class="podpunkt">
                                <li>
                                    a) Wszystkie zgłoszone do trunieju drużyny rodzielane są na dwie grupy.
                                </li>
                                <li>
                                    b) W grupach spotkania rozgrywane są w systemie "każdy z każdym".
                                </li>
                                <li>
                                    c) Po rozegraniu wszystkich meczów zostaje wyłoniona grupa finałowa,
                                    gdzie najlepsze drużyny grają o mistrzostwo.
                                </li>
                            </ul>
                        </li>
                        <li class="punkt">
                            VII. Czas gry to 2 połowy po 30 min
                        </li>
                        <li class="punkt">
                            VIII. Obowiązuje piłka rozmiaru 5.
                        </li>
                        <li class="punkt">
                            IX. Gramy bez spalonych.
                        </li>
                        <li class="punkt">
                            X. Rzuty karne wykonywane są z odległości 10 m od bramki.
                        </li>
                        <li class="punkt">
                            XI. Za rażące przewinienie zawodnik może zostać usunięty z gry
                            na dwie minuty lub do końca spotkania.
                        </li>
                        <li class="punkt">
                            XII. W sytuacjach spornych decyduje organizator.
                        </li>
                    </ol>
                </div>
            </div>
        </div>

        <?php include('./szablon/footer.php'); ?>

    </div>
</body>

</html>
