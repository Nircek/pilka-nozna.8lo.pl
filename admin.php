<?php
	session_start();
	include('./funkcje/funkcje_admin.php');
	is_logged();
?>
<!DOCTYPE html>
<html lang="pl-PL">
<head>

	<meta charset="utf-8">
	<title>PIK Piłka Nożna</title>
	<link rel="stylesheet" type="text/css" href="style/szablon.css">
	<link rel="stylesheet" type="text/css" href="fontello/css/peak.css">
	<link href="https://fonts.googleapis.com/css?family=Monda:400,700&amp;subset=latin-ext" rel="stylesheet">
	<link rel="icon" type="image/png" href="img/logo.png">
	<meta name="robots" content="noindex" />

	<!------------------ STYLE CSS DOTYCZĄCE TYLKO TEJ PODSTRONY STRONY -------------------->
	<link rel="stylesheet" type="text/css" href="style/admin.css">
</head>
<body>

	<div id="container">
	<?php include('./szablon/menu.php'); ?>

		<div id="content-border">
			<div id="content">
				<h1> PANEL ADMINISTRATORA </h1>
				<div id="panel">

					<!-------------------------- DODAWANIE INFO ------------------------>
					<div id="informacje">
						<h2>DODAJ INFORMACJĘ</h2>

						<?php
							// Errors
							if(isset($_SESSION['e_info_pola'])) {
								echo "<div id='error'>" . $_SESSION['e_info_pola'] . "</div><br/>";
								unset($_SESSION['e_info_pola']);
							} elseif(isset($_SESSION['e_info_baza'])) {
								echo "<div id='error'>" . $_SESSION['e_info_baza'] . "</div><br/>";
								unset($_SESSION['e_info_baza']);
							} elseif(isset($_SESSION['e_info_sukces'])){
								echo "<div id='sukces'>" . $_SESSION['e_info_sukces'] . "</div><br/>";
								unset($_SESSION['e_info_sukces']);
							}
						?>

						<form method="post" action="skrypty/dodaj-post.php" id="informacje-form" >
							<h3>TYTUŁ</h3>
							<textarea cols="30" rows="2" id="info_tytul" maxlength="50" name="info_tytul"></textarea><br/>
							<h3>TREŚĆ</h3>
							<textarea cols="40" rows="10" id="info_tresc" name="info_tresc"></textarea><br/>
							<input type="submit" value="PUBLIKUJ">
						</form>
					</div>

					<!-------------------------- WPISYWANIE WYNIKÓW ------------------------>
					<div id="aktualizacja-sezonu">
						<h2>AKTUALIZUJ SEZON</h2>
						<h3>WPISZ WYNIKI</h3>
						<?php
							if(isset($_SESSION['e_wyniki_baza'])) {
									echo '<div id="error">' .$_SESSION['e_wyniki_baza'] . '</div><br/>';
									unset($_SESSION['e_wyniki_baza']);
							} elseif(isset($_SESSION['e_wyniki_baza'])) {
									echo '<div id="error">' .$_SESSION['e_wyniki_baza'] . '</div><br/>';
									unset($_SESSION['e_wyniki_baza']);
							}
						?>
						<div id="dalej-button">
							<a href="admin_wyniki.php">+</a>
						</div>
						<h3>AKTUALIZUJ HARMONOGRAM</h3>
						<?php
							if(isset($_SESSION['e_harmonogram_baza'])) {
									echo '<div id="error">' .$_SESSION['e_harmonogram_baza'] . '</div><br/>';
									unset($_SESSION['e_harmonogram_baza']);
							}
						?>
						<div id="dalej-button">
							<a href="admin_harmonogram.php">+</a>
						</div>
					</div>
					<div style="clear: both;"></div>

					<!-------------------------- DODAWANIE ZDJĘĆ ------------------------>
					<div id="zdjecia">
						<h2> DODAJ ZDJĘCIA </h2>
						<form method="post" enctype="multipart/form-data" action="skrypty/dodaj-zdjecie.php">
							<h3>WYBIERZ SEZON:</h3>
							<input type="number" min='2000'
							<?php
								 $sezon = explode("/", $obecny_sezon)[0];
								 echo "max='".($sezon)."' value='".($sezon)."'"; // <lost this version of file; falling back to 2019-12-07+17:06:26>
							?>
								 id="zdjecia_sezon" name="zdjecie_sezon">

							<?php
								//Sprawdzenie czy skrypt dodaj-zdjecie.php wskazał jakieś błędy
								if(isset($_SESSION['e_zdjecia_pliki'])) {
									//Błąd typu: "Nie wybrano pliku"
									echo '<div id="error">' . $_SESSION['e_zdjecia_pliki'] . '</div><br/>';
									unset($_SESSION['e_zdjecia_pliki']);
								} elseif(isset($_SESSION['e_zdjecia_serwer'])) {
									//Błędy związane z przenoszeniem pliku i wpisem do bazy danych (raczej się nie zdarzają)
									echo '<div id="error">' . $_SESSION['e_zdjecia_serwer'] . '</div><br/>';
									unset($_SESSION['e_zdjecia_serwer']);
								} elseif(isset($_SESSION['e_zdjecia_baza'])) {
									//Błędy związane z wpisywaniem rekordu do bazy
									echo '<div id="error">' . $_SESSION['e_zdjecia_baza'] . '</div><br/>';
									unset($_SESSION['e_zdjecia_baza']);
								} elseif(isset($_SESSION['e_zdjecia_rozszerzenie'])) {
									//Błąd rozszerzenia (dozwolone to png i jpg)
									echo '<div id="error">' . $_SESSION['e_zdjecia_rozszerzenie'] . '</div><br/>';
									unset($_SESSION['e_zdjecia_rozszerzenie']);
								} elseif(isset($_SESSION['e_zdjecia_sukces'])) {
									//Wszystko się udało :D
									echo '<div id="sukces">' . $_SESSION['e_zdjecia_sukces'] . '</div><br/>';
									unset($_SESSION['e_zdjecia_sukces']);
								}
							?>

							<h3>WYBIERZ ZDJĘCIA (.jpg)</h3>
							<input type="file" name="files[]" id="zdjecia_wybor" accept=".jpg,.jpeg,.png" multiple><br/>
							<input type="submit" value="DODAJ">
						</form>
					</div>

					<!-------------------------- TWORZENIE SEZONU ------------------------>
					<div id="tworzenie-sezonu">
						<h2>STWÓRZ SEZON</h2>
						<?php
							//Sprawdzenie czy się powiodło
								if(isset($_SESSION['sezon_sukces'])) {
									echo '<div id="sukces">' . $_SESSION['sezon_sukces'] . '</div><br/>';
									unset($_SESSION['sezon_sukces']);
								}
						?>

						<div id="dalej-button">
							<a href="admin_sezon.php">+</a>
						</div>
						<br/>
						<h2> STWÓRZ RUNDĘ FINAŁOWĄ </h2>
						<?php
							//Sprawdzenie czy się powiodło
								if(isset($_SESSION['e_final_istnieje'])) {
									echo '<div id="error">' . $_SESSION['e_final_istnieje'] . '</div><br/>';
									unset($_SESSION['e_final_istnieje']);
								} elseif(isset($_SESSION['e_final_tabela'])) {
									echo '<div id="error">' . $_SESSION['e_final_tabela'] . '</div><br/>';
									unset($_SESSION['e_final_tabela']);
								} elseif(isset($_SESSION['sukces_final'])) {
									echo '<div id="sukces">' . $_SESSION['sukces_final'] . '</div><br/>';
									unset($_SESSION['sukces_final']);
								}
						?>
						<div id="dalej-button">
							<a href="skrypty/admin_final.php">+</a>
						</div>
					</div>

					<div style="clear: both;"></div>
				</div>
			</div>
		</div>

		<?php include('./szablon/footer.php'); ?>

	</div>
</body>
</html>
