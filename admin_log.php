<?php
	session_start();

	$ADMIN_PASS = '<credentials censored>';
	$ADMIN_LOGIN = '<credentials censored>';

	//Sprawdzenie czy formularz został wysłany (czu użytkownik kliknął 'zaloguj')
	if(isset($_POST['login'])) {
		include("./skrypty/db-connect.php");

		$login = $_POST['login'];
		$password = $_POST['password'];

		if(empty($login) OR empty($password)) {
			$_SESSION['e_log_pola'] = "Wypisz oba pola!";
		} else {
	    if($password == $ADMIN_PASS and $login == $ADMIN_LOGIN) {
	        $_SESSION['zalogowany'] = true;
	        header('Location: admin.php');
	        exit();
	    } else {
	        $_SESSION['e_log_dane'] = "Niepoprawny login lub hasło!";
	    }
		}
	}

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

	<!----------------- STYLE CSS DOTYCZĄCE TYLKO TEJ PODSTRONY STRONY -------------------->
	<link rel="stylesheet" type="text/css" href="style/admin.css">
	<style>
	#formularz{
		margin: auto;
		width: 300px;
		font-size: 25px;
	}

	</style>
</head>
<body>

	<div id="container">
		<?php include('./szablon/menu.php'); ?>

		<div id="content-border">
			<div id="content">
				<h1> LOGOWANIE ADMINISTRATORA </h1>
				<?php
						if(isset($_SESSION['e_log_pola'])){
							echo "<div id='error'>" . $_SESSION['e_log_pola'] . "</div>";
							unset($_SESSION['e_log_pola']);
						} elseif(isset($_SESSION['e_log_baza'])){
							echo "<div id='error'>" . $_SESSION['e_log_baza'] . "</div>";
							unset($_SESSION['e_log_baza']);
						} elseif(isset($_SESSION['e_log_dane'])){
							echo "<div id='error'>" . $_SESSION['e_log_dane'] . "</div>";
							unset($_SESSION['e_log_dane']);
						}
					?>
				<div id="formularz">
					<form method="post" action="#">
						<h3>Login:</h3>
						<input type="text" id="login" name="login"><br/>
						<h3>Hasło:</h3>
						<input type="password" id="password" name="password"><br/>
						<input type="submit" value="ZALOGUJ">
					</form>
				</div>
			</div>
		</div>

		<?php include('./szablon/footer.php'); ?>

	</div>
</body>
</html>
