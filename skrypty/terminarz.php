<?php

	session_start();
	$path = $_SERVER['DOCUMENT_ROOT'];
	
	//Sprawdzanie zalogowania żeby wyświetlić "pasek admina"
	if(isset($_SESSION['zalogowany']) == false)
	{
		header('Location: admin_log.php');
		exit;
	}
	
	include($path.'/db-connect.php');
	
	$sezon = $_POST['sezon'];
	$sezon_final = $sezon."_final";
	$sezon_terminarz = $sezon."_terminarz";
	
	if(isset($_POST['final_ilosc']) == True)
	{
		for($y=1; $y<=4; $y++)
		{
			$id = $y;
			$termin = $_POST['f_'.$y];
			$sql = "UPDATE `$sezon_final` SET `termin` = '$termin' WHERE `id` = '$id'";
			try
			{
				$pdo->exec($sql);
			}
			catch(PDOException $e)
			{
				$_SESSION['e_harmonogram_baza'] = "Błąd bazy danych: $e";
				header('Location: ../admin_harmonogram.php');
				exit();
			}
		}
	}
	elseif(isset($_POST['grupa_ilosc']) == True)
	{
		$ilosc = $_POST['grupa_ilosc'];
		for($y=1; $y<=$ilosc; $y++)
		{
			$id = $y;
			$termin = $_POST[$y];
			$sql = "UPDATE `$sezon_terminarz` SET `termin` = '$termin' WHERE `id` = '$id'";
			try
			{
				$pdo->exec($sql);
			}
			catch(PDOException $e)
			{
				$_SESSION['e_harmonogram_baza'] = "Błąd bazy danych: $e";
				header('Location: ../admin_harmonogram.php');
				exit();
			}
		}
	}
	
	header('Location: ../admin_harmonogram.php');
	exit();
?>