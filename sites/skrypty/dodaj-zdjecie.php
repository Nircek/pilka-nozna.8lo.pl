<?php
is_logged();
header('Location: ' . PANEL_URL);

$sezon = cast_int($_POST['zdjecie_sezon']);
if (is_null($sezon)) {
    report_error("sezon violation", NULL);
    exit();
}

function random_uuid()
{
    // SRC: https://solvit.io/50064cf
    return sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff)
    );
}
function make_thumb($src, $dest, $desired_width)
{
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

// Sprawdzenie czy plik został wybrany
if (!empty($_FILES['files']['name'][0])) {
    $name_array = $_FILES['files']['name'];
    $tmp_name_array = $_FILES['files']['tmp_name'];
    $type_array = $_FILES['files']['type'];

    for ($i = 0; $i < count($tmp_name_array); $i++) {
        $ext = explode(".", $name_array[$i]);
        $ostatnia_przerwa = count($ext) - 1; // Gdyby było więcej kropek niż 1
        $ext = $ext[$ostatnia_przerwa];

        if ($ext == "jpg" or $ext == "JPG") {
            // Tworzymy unikatową nazwę dla pliku
            do {
                $random = random_uuid();
                $name_array[$i] = "${random}.jpg";
            } while (file_exists(ROOT_PATH . "/public/zdjecia/" . $name_array[$i]));

            $file_absolute = ROOT_PATH . "/public/zdjecia/" . $name_array[$i];
            if (move_uploaded_file($tmp_name_array[$i], $file_absolute)) {
                $file_destination = "zdjecia/" . $name_array[$i];
                make_thumb($file_absolute, ROOT_PATH . "/public/zdjecia/thumb." . $name_array[$i], 200);
                PDOS::Instance()->prepare("INSERT INTO `zdjecia` (`sezon`, `sciezka`, `data`) VALUES (?, ?, CURDATE())")->execute([$sezon, $file_destination]);
            } else {
                report_error("Wystąpił problem z przesłaniem pliku na serwer!", NULL);
                exit();
            }
        } else {
            report_error($name_array[$i] . " - Rozszerzenie nie jest obsługiwane!", NULL);
            exit();
        }
    }
} else {
    report_error("Wybierz pliki!", NULL);
    exit();
}

exit();
