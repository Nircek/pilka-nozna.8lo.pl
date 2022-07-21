<?php
is_logged();

// SPRAWDZANIE WYSŁANIE FORMULARZA KROKU 1
if (isset($_POST['sezon'])) {
    // Sprawdzanie czy wszystkie pola są wypełnione
    if (empty($_POST['sezon']) or empty($_POST['liczba_druzyn'])) {
        report_error("Wszystkie pola są wymagane!", null);
        $_SESSION['krok'] = 1;
    } else {
        $sezon = cast_int($_POST['sezon']);
        $liczba_druzyn = cast_int($_POST['liczba_druzyn']);
        if (is_null($sezon) or is_null($liczba_druzyn)) {
            report_error("sezon or liczba violation", null);
            exit();
        }
        $_SESSION['liczba_druzyn'] = $liczba_druzyn;
        $_SESSION['sezon'] = $sezon;

        // Ustawiam KROK na 2 i przechodzę do tegoż kroku
        $_SESSION['krok'] = 2;
        header('Location: .');
        exit();
    }
}

function page_krok_render()
{
    ?>
    <div class="error">Funkcja nie została zaimplementowana.</div>
    <?php SERVER_ERROR(501); ?>
    <!------------------ TWORZENIE SEZONU KROK 1 ------------------>
    <div id="panel">
        <form action="#" method="post" autocomplete="off">
            <p class="big">ROK ROZPOCZĘCIA: (np. 2016) </p>
            <input type="number" name="sezon" maxlength="4" cols="9" id="sezon" min="2016" max="2030"> <br />
            <p class="big">LICZBA DRUŻYN W CAŁYM SEZONIE:</p>
            <input type="number" name="liczba_druzyn" id="liczba_druzyn" min="2" max="30"> <br />
            <input type="submit" value="DALEJ">
        </form>
    </div>
<?php
}
