<?php

session_start();

include('./../funkcje/funkcje_admin.php');
is_logged();

include("./db-connect.php");//MZ: wcześniejsza wersja `include("./skrypty/db-connect.php");`... jesteśmy w folderze skrypty, więc nie ma takiego pliku

$sezon = $_POST['zdjecie_sezon'];
$data = date('Y-m-d');
function uuid() //MZ: dodałem tą funkcję do tworzenia losowych nazw zdjęć
{
    // SRC: https://solvit.io/50064cf
    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}
function make_thumb($src, $dest, $desired_width) {
    // SRC: https://stackoverflow.com/a/44323040/6732111
    /* read the source image */
    $source_image = imagecreatefromjpeg($src);
    $width = imagesx($source_image);
    $height = imagesy($source_image);
    /* find the "desired height" of this thumbnail, relative to the desired width  */
    $desired_height = floor($height * ($desired_width / $width));
    /* create a new, "virtual" image */
    $virtual_image = imagecreatetruecolor($desired_width, $desired_height);
    /* copy source image at a resized size */
    imagecopyresampled($virtual_image, $source_image, 0, 0, 0, 0, $desired_width, $desired_height, $width, $height);
    /* create the physical thumbnail image to its destination */
    imagejpeg($virtual_image, $dest);
}

//Sprawdzenie czy plik został wybrany
if(!empty($_FILES['files']['name'][0])) {
    $name_array = $_FILES['files']['name'];
    $tmp_name_array = $_FILES['files']['tmp_name'];
    $type_array = $_FILES['files']['type'];

    for($i = 0; $i < count($tmp_name_array); $i++) {
        $ext = explode(".", $name_array[$i]);
        $ostatnia_przerwa = count($ext) - 1; //Gdyby było więcej kropek niż 1
        $ext = $ext[$ostatnia_przerwa];

        if($ext == "jpg" OR $ext == "JPG") {
            //Tworzymy unikatową nazwę dla pliku
            do {
                $random = uuid(); //MZ: wcześniej `uniqid() . rand(100, 9999)`, które było bezsensowne
                $name_array[$i] = $random . ".jpg";
            } while(file_exists("../zdjecia/".$name_array[$i])); //MZ: brak powtarzania UUID

            if(move_uploaded_file($tmp_name_array[$i], "../zdjecia/".$name_array[$i])) {
                $file_destination = "zdjecia/" . $name_array[$i];
                make_thumb("../".$file_destination, "../zdjecia/thumb." . $name_array[$i], 200);
                try {
                    //$sql = ; //MZ: nie ma żadnego odwołania do $sql później... usuwam
                    $stmt = $pdo->prepare("INSERT INTO `zdjecia` (`id`, `sezon`, `sciezka`, `data`) VALUES (NULL, '$sezon', '$file_destination', '$data')");
                    $stmt->execute();
                } catch(PDOException $e) {
                    $_SESSION['e_zdjecia_baza'] = "Wystąpił problem z bazą danych: " . $e;
                    header('Location: ../admin.php');
                    exit();
                }

            } else {
                $_SESSION['e_zdjecia_serwer'] = "Wystąpił problem z przesłaniem pliku na serwer!";
                header('Location: ../admin.php');
                exit();
            }
        } else {
            $_SESSION['e_zdjecia_rozszerzenie'] = "$name_array[$i] - Rozszerzenie nie jest obsługiwane!";
            header('Location: ../admin.php');
            exit();
        }
    }
} else {
    $_SESSION['e_zdjecia_pliki'] = "Wybierz pliki!";
    header('Location: ../admin.php');
    exit();
}

$_SESSION['e_zdjecia_sukces'] = "Zdjęcia zostały dodane pomyślnie!";
header('Location: ../admin.php');
exit();
