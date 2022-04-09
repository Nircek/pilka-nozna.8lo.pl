<?php
include(ROOT_PATH . "/funkcje/funkcje_admin.php");
is_logged();

// Sprawdzenie czy ktoś wysłał formularz
if (isset($_POST['info_tytul'])) {
    $tytul = $_POST['info_tytul'];
    $tresc = $_POST['info_tresc'];

    if ((empty($tytul)) or (empty($tresc))) {
        $_SESSION['e_info_pola'] = "Oba pola muszą być wypełnione";
        header('Location: ../admin.php');
        exit();
    } else {
        try {
            $sql = "INSERT INTO `informacje` (`id`, `tytul`, `tresc`, `data`) VALUES (NULL, '$tytul', '$tresc', CURDATE())";
            $stmt = PDOS::Instance()->prepare($sql);
            $stmt->execute();
        } catch (PDOException $e) {
            reportError("db", $e->getMessage());
        }

        // Jesli sukces to się wyświetli, a jeśli nie to skrypt już wcześniej wywali błąd i przerwie działanie
        $_SESSION['e_info_sukces'] = "Informacja została dodana pomyślnie!";
        header('Location: ../admin.php');
        exit();
    }
} else {
    header('Location: ../');
    exit();
}
