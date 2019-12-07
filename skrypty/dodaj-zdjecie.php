<?php

session_start();

include('./../funkcje/funkcje_admin.php');
is_logged();

include("./db-connect.php");//MZ: wcześniejsza wersja `include("./skrypty/db-connect.php");`... jesteśmy w folderze skrypty, więc nie ma takiego pliku

$sezon = $_POST['zdjecie_sezon'];
$data = date('Y-m-d');

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
			$random = uniqid() . rand(100, 9999);
			$name_array[$i] = $random . ".jpg";

			if(move_uploaded_file($tmp_name_array[$i], "../zdjecia/".$name_array[$i])) {
				$file_destination = "zdjecia/" . $name_array[$i];
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
?>
