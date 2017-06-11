<?php
	
	session_start();
	
	$path = $_SERVER['DOCUMENT_ROOT'];
	
	//Sprawdzenie czy ktoś wysłał formularz
	if(isset($_POST['info_tytul']))
	{

		$tytul = $_POST['info_tytul'];
		$tresc = $_POST['info_tresc'];
		$data = date('Y-m-d');
		
		//Sprawdzanie czy wpisano tytuł i treść 
		if((empty($tytul) == true) OR (empty($tresc) == true))
		{
			//Wywalanie błędu jeśli nie są oba wypełnione
			$_SESSION['e_info_pola'] = "Oba pola muszą być wypełnione";
			header('Location: ../admin.php');
			exit();
		}
		else
		{
			include($path . "/skrypty/db-connect.php");
			//Wkładanie do bazy odpowiednich rekordów
			try
			{
				$sql = "INSERT INTO `informacje` (`id`, `tytul`, `tresc`, `data`) VALUES (NULL, '$tytul', '$tresc', '$data')";
				$stmt = $pdo->prepare($sql);
				$stmt->execute();
			}
			catch(PDOException $e)
			{
				$_SESSION['e_info_baza'] = "Wystąpił problem z bazą danych: " . $e;
				header('Location: ../admin.php');
				exit();
			}
			
			//Jesli sukces to się wyświetli, a jeśli nie to skrypt już wcześniej wywali błąd i przerwie działanie
			$_SESSION['e_info_sukces'] = "Informacja została dodana pomyślnie!";
			header('Location: ../admin.php');
			exit();
		}
	}
	else
	{
		header('Location: '.$path.'/index.php');
		exit();
	}


?>