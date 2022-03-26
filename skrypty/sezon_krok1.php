<?php
    is_logged();

    //SPRAWDZANIE WYSŁANIE FORMULARZA KROKU 1
    if(isset($_POST['sezon'])) {
        //Sprawdzanie czy wszystkie pola są wypełnione
        if(empty($_POST['sezon']) OR empty($_POST['liczba_druzyn'])) {
            $_SESSION['e_sezon_pola'] = "Wszystkie pola są wymagane!";
            $_SESSION['krok'] = 1;
        } else {
            //Zmienne z formularza przesyłam do kroku drugiego.
            $_SESSION['liczba_druzyn'] = $_POST['liczba_druzyn'];
            $_SESSION['sezon'] = $_POST['sezon'];

            //Ustawiam KROK na 2 i przechodzę do tegoż kroku
            $_SESSION['krok'] = 2;
            header('Location: admin_sezon.php');
            exit();
        }
    }
?>
<!-------------------------- TWORZENIE SEZONU KROK 1 ------------------------>

<div id="panel">
    <form action="#" method="post" autocomplete="off">

        <?php
            // Validation errors
            if(isset($_SESSION['e_sezon_pola'])) {
                //Nie podano sezonu
                echo '<div id="error">' . $_SESSION['e_sezon_pola'] . '</div><br/>';
                unset($_SESSION['e_sezon_pola']);
            } elseif(isset($_SESSION['e_sezon_baza'])) {
                //Błąd bazy danych przy tworzeniu sezonu
                echo '<div id="error">' . $_SESSION['e_sezon_baza'] . '</div><br/>';
                unset($_SESSION['e_sezon_baza']);
            }
        ?>

        <h3>ROK ROZPOCZĘCIA: (np. 2016) </h3>
        <input type="number" name="sezon" maxlength="4" cols="9" id="sezon" min="2016" max="2030"> <br />
        <h3>LICZBA DRUŻYN W CAŁYM SEZONIE:</h3>
        <input type="number" name="liczba_druzyn" id="liczba_druzyn" min="2" max="30"> <br />
        <input type="submit" value="DALEJ">
    </form>
</div>
