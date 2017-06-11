<?php
	$path = $_SERVER['DOCUMENT_ROOT'];
//======================================================================================================
	//Sprawdza czy cokolwiek wpisano i robi update odpowiednich rekordów
	function dodawanie_wyniku($sezon_terminarz, $id, $wynik_1, $wynik_2)
	{
		$path = $_SERVER['DOCUMENT_ROOT'];
		include($path.'/skrypty/db-connect.php');
		
		if((empty($wynik_1) == true AND is_numeric($wynik_1) == false) OR (empty($wynik_2) == true AND is_numeric($wynik_1) == false))
		{
			$wynik_1 = NULL;
			$wynik_2 = NULL;
			
			$sql = "UPDATE $sezon_terminarz SET 1_wynik =NULL, 2_wynik=NULL WHERE id='$id' ";
		}
		else
		{
			$sql = "UPDATE $sezon_terminarz SET 1_wynik ='".$wynik_1."', 2_wynik='".$wynik_2."' WHERE id='$id' ";
		}
		
		//Wk³adanie wszystkich wyników do bazy danych
		try
		{
			$pdo->exec($sql);
		}
		catch(PDOException $e)
		{
			$_SESSION['e_wyniki_baza'] = "B³¹d bazy danych: $e <br/> $sql";
			header('Location: ../nowe_wyniki.php');
			exit();
		}
	}
	
//=====================================================================================================
	function resetowanie_tabeli($sezon_tabela)
	{
		$path = $_SERVER['DOCUMENT_ROOT'];
		include($path.'/skrypty/db-connect.php');
		
		//RESETOWANIE TABELI Z PUNKTAMI
		//Trzeba to zrobiæ ze wzglêdu na to i¿ pkt dodaj¹ siê do ju¿ zapisanych i by³by problem gdyby chcia³o siê jakiœ mecz anulowaæ
		//Dlatego wszystko zawsze zlicza siê od pocz¹tku
		try
		{
			$sql = "UPDATE `$sezon_tabela`
					SET `pkt` = 0,
						`zwyciestwa` = 0,
						`przegrane` = 0,
						`remisy` = 0,
						`zdobyte` = 0,
						`stracone` = 0";
			$pdo->exec($sql);
		}
		catch(PDOException $e)
		{
			$_SESSION['e_wyniki_baza'] = "B³¹d bazy danych: $e";
			header('Location: admin_wyniki.php');
			exit();
		}
	}
//=====================================================================================================
	function tabela($sezon_tabela, $grupa, $d1, $d2, $gole_1, $gole_2)
	{
		$path = $_SERVER['DOCUMENT_ROOT'];
		include($path.'/skrypty/db-connect.php');
		
		if($gole_1 != NULL and $gole_2 != NULL)
		{	
			
			//Dodawanie odpowiednich danych odpowiednim zespo³om
			if($gole_1 == $gole_2)
			{
				try
				{
					$sql = "UPDATE `$sezon_tabela`
							SET `remisy` = `remisy` + 1, `pkt` = `pkt` + 1, `zdobyte` = `zdobyte` + '$gole_1', `stracone` = `stracone` + '$gole_2'
							WHERE `numer` = '$d1' AND `grupa` = '$grupa'";
					$pdo->exec($sql);
				}
				catch(PDOException $e)
				{
					$_SESSION['e_wyniki_baza'] = "B³¹d bazy danych: $e <br/> $sql";
					header('Location: ../nowe_wyniki.php');
					exit();
				}
				
				try
				{
					$sql = "UPDATE `$sezon_tabela`
							SET `remisy` = `remisy` + 1, `pkt` = `pkt` + 1, `zdobyte` = `zdobyte` + '$gole_2', `stracone` = `stracone` + '$gole_1'
							WHERE `numer` = '$d2' AND `grupa` = '$grupa'";
					$pdo->exec($sql);
				}
				catch(PDOException $e)
				{
					$_SESSION['e_wyniki_baza'] = "B³¹d bazy danych: $e <br/> $sql";
					header('Location: ../nowe_wyniki.php');
					exit();
				}
			}
			elseif($gole_1 > $gole_2)
			{
				try
				{
					$sql = "UPDATE `$sezon_tabela`
							SET `zwyciestwa` = `zwyciestwa` + 1, `pkt` = `pkt` + 3, `zdobyte` = `zdobyte` + '$gole_1', `stracone` = `stracone` + '$gole_2'
							WHERE `numer` = '$d1' AND `grupa` = '$grupa'";
					$pdo->exec($sql);
				}
				catch(PDOException $e)
				{
					$_SESSION['e_wyniki_baza'] = "B³¹d bazy danych: $e <br/> $sql";
					header('Location: ../nowe_wyniki.php');
					exit();
				}
				
				try
				{
					$sql = "UPDATE `$sezon_tabela`
							SET `przegrane` = `przegrane` + 1, `zdobyte` = `zdobyte` + '$gole_2', `stracone` = `stracone` + '$gole_1'
							WHERE `numer` = '$d2' AND `grupa` = '$grupa'";
					$pdo->exec($sql);
				}
				catch(PDOException $e)
				{
					$_SESSION['e_wyniki_baza'] = "B³¹d bazy danych: $e <br/> $sql";
					header('Location: ../nowe_wyniki.php');
					exit();
				}
			}
			elseif($gole_1 < $gole_2)
			{
				try
				{
					$sql = "UPDATE `$sezon_tabela`
							SET `przegrane` = `przegrane` + 1, `zdobyte` = `zdobyte` + '$gole_1', `stracone` = `stracone` + '$gole_2'
							WHERE `numer` = '$d1' AND `grupa` = '$grupa'";
					$pdo->exec($sql);
				}
				catch(PDOException $e)
				{
					$_SESSION['e_wyniki_baza'] = "B³¹d bazy danych: $e <br/> $sql";
					header('Location: ../nowe_wyniki.php');
					exit();
				}
				
				try
				{
					$sql = "UPDATE `$sezon_tabela`
							SET `zwyciestwa` = `zwyciestwa` + 1, `pkt` = `pkt` + 3, `zdobyte` = `zdobyte` + '$gole_2', `stracone` = `stracone` + '$gole_1'
							WHERE `numer` = '$d2' AND `grupa` = '$grupa'";
					$pdo->exec($sql);
				}
				catch(PDOException $e)
				{
					$_SESSION['e_wyniki_baza'] = "B³¹d bazy danych: $e <br/> $sql";
					header('Location: ../nowe_wyniki.php');
					exit();
				}
			}
		}
	}
	

?>