<?php

		$baza_danych = "<credentials censored>";
		$host = "<credentials censored>";
		$login = "<credentials censored>";
		$haslo = "<credentials censored>";

		// KONFIGURACJA POŁĄCZENIA //
			try
			{
				$pdo = new PDO('mysql:host=localhost;dbname=pilka_nozna', 'root', '');
				$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$pdo->exec('SET NAMES "utf8"');
			}
			catch (PDOException $e)
			{
				$output = 'Nie można nawiązać połączenia z bazą danych ' . $e->getMessage();
			}
			
?>