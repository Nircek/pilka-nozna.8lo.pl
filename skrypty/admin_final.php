<?php
	session_start();
	is_logged();

	include('./db-connect.php');
	include('./../funkcje/funkcje.php');

	//Pobieranie obecnego sezonu
	$sezon = obecny_sezon($pdo);
	$sezon_tabela = $sezon."_tabela";
	$sezon_final = $sezon."_final";

	//Sprawdzanie czy już przypadkiem nie istnieje...
	if(sprawdzanie_tabela($pdo, $sezon_final)) {
		$_SESSION['e_final_istnieje'] = "Finał obecnego sezonu już istnieje!";
		header('Location: ../admin.php');
		exit();
	} else {
		// ========================= TWORZENIE TABELI FINAŁOWEJ ========================
		try {
			$sql = "CREATE TABLE `$sezon_final` (
						`id` int NOT NULL AUTO_INCREMENT,
						`druzyna_1` text,
						`druzyna_2` text,
						`wynik_1` int,
						`wynik_2` int,
						`termin` date null,
						`poziom` int null,
						 PRIMARY KEY(id)
						)";
			$stmt = $pdo->prepare($sql);
			$stmt->execute();
		} catch(PDOException $e) {
			$_SESSION['e_final_tabela'] = "Błąd tabeli $sezon_final: $e";
			header('Location: ../admin.php');
			exit();
		}

		// ========================= POBIERANIE DRUŻYN ========================
		try {
			$result_1 = $pdo->query("SELECT nazwa FROM $sezon_tabela WHERE grupa=1 ORDER BY pkt DESC, (zdobyte - stracone) DESC, nazwa ASC LIMIT 2 ");
			$result_2 = $pdo->query("SELECT nazwa FROM $sezon_tabela WHERE grupa=2 ORDER BY pkt DESC, (zdobyte - stracone) DESC, nazwa ASC LIMIT 2 ");
		} catch (PDOException $e) {
			$_SESSION['e_final_tabela'] = "Błąd tabeli $sezon_final: $e";
			header('Location: ../admin.php');
			exit();
		}

		while ($row = $result_1->fetch())
			$druzyny_1[] = array('nazwa' => $row['nazwa']);

		while ($row = $result_2->fetch())
			$druzyny_2[] = array('nazwa' => $row['nazwa']);

		$i = 0;
		foreach ($druzyny_1 as $druzyny_1)
			$grupa_1[$i++] = $druzyny_1['nazwa'];

		$i = 0;
		foreach ($druzyny_2 as $druzyny_2)
			$grupa_2[$i++] = $druzyny_2['nazwa'];

		// ================================== USTALANIE MECZY ====================
		$mecz_1[0] = $grupa_1[0];
		$mecz_1[1] = $grupa_2[1];
		$mecz_2[0] = $grupa_2[0];
		$mecz_2[1] = $grupa_1[1];

		// ================================= WSTAWIANIE DO BAZY ==================
		try {
			$sql = "INSERT INTO `$sezon_final` (`id`, `druzyna_1`, `druzyna_2`, `poziom`) VALUES ('NULL', '$mecz_1[0]', '$mecz_1[1]', '2'),
																																			('NULL', '$mecz_2[0]', '$mecz_2[1]', '2'),
																																			('NULL', NULL, NULL, '3'),
																																			('NULL', NULL, NULL, '1')";
			$pdo->exec($sql);
		} catch (PDOException $e) {
			$_SESSION['e_final_tabela'] = "Błąd tabeli $sezon_final: $e";
			header('Location: ../admin.php');
			exit();
		}

		$_SESSION['sukces_final'] = "Utworzono drzewko finałowe!";
		header('Location: ../admin.php');
		exit();
	}
?>
