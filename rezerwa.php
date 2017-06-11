<?php
	session_start();
?>
<!DOCTYPE html>
<html lang="pl-PL">
<head>

	<?php /* Załadowanie powtarzających się tagów dla każdej podstrony */ include('szablon/meta.php'); ?>
	
	<!------------------ STYLE CSS DOTYCZĄCE TYLKO TEJ PODSTRONY STRONY -------------------->
	<link rel="stylesheet" type="text/css" href="style/sezony.css">
</head>
<body>
	
	<div id="container">
	
		<?php /* Załadowanie Menu z szablonu */ include('szablon/menu.php'); ?>
		
		<div id="content-border">
			
				<?php
					//Nawiązywanie połączenia z bazą
					include("skrypty/db-connect.php");

					if(!isset($_GET['s']))
					{

		// -------------------------------- JEŚLI JESZCZE SEZON NIE WYBRANY ----------------- //
						echo "<div id='content' style='background-color: rgba(20, 21, 24, 0.9);
													   width: 960px;
													   padding: 20px;'>";
							echo "<h1> WYBIERZ SEZON </h1>";

							try {
								//Wysyłanie zapytania
								$sql = "SELECT sezon FROM sezony ORDER BY id DESC";
								$result = $pdo->query($sql);
							} catch(PDOException $e) {
								echo "<div id='error'>". $e ."</div>";
							}
							while ($row = $result->fetch()){
								$sezon[] = array('sezon' => $row['sezon']);
							}
							//Składanie "kafelka" z odnośnikiem do galerii danego sezonu
							foreach($sezon as $sezon) {
								echo "<div class='sezon'>
											<a href='?s=".$sezon['sezon']."/".($sezon['sezon'] +1)."'>".
											 $sezon['sezon']."/".($sezon['sezon'] +1)
										  . "</a>
										</div>";
							}
							echo "<div class='sezon'>
									<a href='http://www.pilka-nozna.8lo.pl/' target='_blank'>
										ARCHIWUM
									</a>
								  </div>
								  <div style='clear: both;'></div>";

						echo "</div>";
					}
					else
					{
						$sezon_pelny = $_GET['s'];
						//Sezon zapisywany w bazie to tylko jego liczba początkowa
						//2016/2017 to tylko 2016, dlatego $_GET musi rozdzielić te dwie liczby
						$sezon_arr = explode("/", $sezon_pelny);
						$sezon = $sezon_arr[0];
						$sezon_tabela = $sezon . "_tabela";
						$sezon_terminarz = $sezon . "_terminarz";

						echo "<div id='content'>";
					?>
<!------------------------------------------ LEWA KOLUMNA -------------------------------------->
						<div id="left-content">
							<div id="powrot"><a href="sezony.php"> &#8592 POWRÓT </a></div>
						</div>

<!---------------------------------------- ŚRODKOWA KOLUMNA ------------------------------------>
						<div id="center-content">
						<?php echo "<h1>SEZON $sezon_pelny </h1>"; ?>
							<h3> GRUPA PIERWSZA </h3>

							<?php
 	// --------------------- POBIERANIE TERMINARZU I WYNIKÓW GRUPY PIERWSZEJ---------------------						
							try
							{
								$sql = "SELECT * FROM $sezon_terminarz WHERE grupa=1 ORDER BY termin ASC";
								$result_terminarz = $pdo->query($sql);
							}
							catch(PDOException $e)
							{
								echo "<div id='error'>". $e ."</div>";
							}
							while ($row = $result_terminarz->fetch())
							{
								$grupa_1_wyniki[] = array('druzyna_1' => $row['1_text'],
												   'druzyna_2' => $row['2_text'],
												   'gole_1' => $row['1_wynik'],
												   'gole_2' => $row['2_wynik'],
												   'termin' => $row['termin']);
							}
							foreach ($grupa_1_wyniki as $grupa_1_wyniki) 
							{
								if($grupa_1_wyniki['gole_1'] == NULL
									 OR $grupa_1_wyniki['gole_2'] == NULL)
								{
									$grupa_1_wyniki['gole_1'] = "-";
									$grupa_1_wyniki['gole_2'] = "-";
								}
								if($grupa_1_wyniki['termin'] == "0000-00-00")
								{
									$grupa_1_wyniki['termin'] = "nieustalono";
								}
								echo "<table id='terminarz' cellspacing='0'>
										  <tr id='tr_termin'> 
											<td colspan='3'>". $grupa_1_wyniki['termin'] ."</td>
										  </tr>
										  <tr id='tr_wynik'>
											<td>". $grupa_1_wyniki['druzyna_1'] ."</td>
											<td id='td_wynik'>". 
												$grupa_1_wyniki['gole_1'] .":". $grupa_1_wyniki['gole_2']."
											</td>
											<td>". $grupa_1_wyniki['druzyna_2'] ."</td>
										 </tr>
									 </table>";
							}

							echo "<h3 style='margin-top: 40px;'> GRUPA DRUGA </h3>";
	// --------------------- POBIERANIE TERMINARZU I WYNIKÓW GRUPY PIERWSZEJ---------------------						
							try
							{
								$sql = "SELECT * FROM $sezon_terminarz WHERE grupa=2 ORDER BY termin ASC";
								$result_terminarz = $pdo->query($sql);
							}
							catch(PDOException $e)
							{
								echo "<div id='error'>". $e ."</div>";
							}
							while ($row = $result_terminarz->fetch())
							{
								$grupa_2_wyniki[] = array('druzyna_1' => $row['1_text'],
												   'druzyna_2' => $row['2_text'],
												   'gole_1' => $row['1_wynik'],
												   'gole_2' => $row['2_wynik'],
												   'termin' => $row['termin']);
							}
							foreach ($grupa_2_wyniki as $grupa_2_wyniki) 
							{
								if($grupa_2_wyniki['gole_1'] == NULL
									 OR $grupa_2_wyniki['gole_2'] == NULL)
								{
									$grupa_2_wyniki['gole_1'] = "-";
									$grupa_2_wyniki['gole_2'] = "-";
								}
								if($grupa_2_wyniki['termin'] == "0000-00-00")
								{
									$grupa_2_wyniki['termin'] = "nieustalono";
								}
								echo "<table id='terminarz' cellspacing='0'>
										  <tr id='tr_termin'> 
											<td colspan='3'>". $grupa_2_wyniki['termin'] ."</td>
										  </tr>
										  <tr id='tr_wynik'>
											<td>". $grupa_2_wyniki['druzyna_1'] ."</td>
											<td id='td_wynik'>". 
												$grupa_2_wyniki['gole_1'] .":". $grupa_2_wyniki['gole_2']."
											</td>
											<td>". $grupa_2_wyniki['druzyna_2'] ."</td>
										 </tr>
									 </table>";
							}
						?>
						</div>

<!----------------------------------------- PRAWA KOLUMNA -------------------------------------->
						<div id="right-content">
							<h1> TABELA </h1>
							<h3> GRUPA PIERWSZA </h3>
							<table id="tabela" cellspacing="0">
								<tr>
									<th> LP </th>
									<th> ZESPÓŁ </th>
									<th> PKT </th>
									<th> Z </th>
									<th> R </th>
									<th> P </th>
									<th> Strzel. </th>
									<th> Strac. </th>
									<th> +/- </th>
								</tr>
							<?php

	// ------------------------------- POBIERANIE TABELI SEZONU GRUPY PIERWSZEJ ----------------
							try
							{
								$sql = "SELECT * FROM $sezon_tabela WHERE grupa=1 ORDER BY pkt DESC";
								$result_tabela = $pdo->query($sql);
							}
							catch(PDOException $e)
							{
								echo "<div id='error'>". $e ."</div>";
							}
							while ($row = $result_tabela->fetch())
							{
								$grupa_1_tabela[] = array('nazwa' => $row['nazwa'],
												   'pkt' => $row['pkt'],
												   'z' => $row['zwyciestwa'],
												   'r' => $row['remisy'],
												   'p' => $row['przegrane'],
												   'zdobyte' => $row['zdobyte'],
												   'stracone' => $row['stracone']);
							}
							$i=1;
							foreach ($grupa_1_tabela as $grupa_1_tabela) 
							{
								$bilans = $grupa_1_tabela['zdobyte'] - $grupa_1_tabela['stracone'];
								if($bilans > 0){
									$bilans = "+". $bilans;
								}	
								echo "<tr>
										<td> $i. </td>
										<td>". $grupa_1_tabela['nazwa'] ."</td>
										<td>". $grupa_1_tabela['pkt'] ."</td>
										<td>". $grupa_1_tabela['z'] ."</td>
										<td>". $grupa_1_tabela['r'] ."</td>
										<td>". $grupa_1_tabela['p'] ."</td>
										<td>". $grupa_1_tabela['zdobyte'] ."</td>
										<td>". $grupa_1_tabela['stracone'] ."</td>
										<td>". $bilans ."</td>
									 </tr>";
								$i++;
							}
							?>
							</table>
							<h3>GRUPA DRUGA</h3>
							<table id="tabela" cellspacing="0">
								<tr>
									<th> LP </th>
									<th> ZESPÓŁ </th>
									<th> PKT </th>
									<th> Z </th>
									<th> R </th>
									<th> P </th>
									<th> Strzel. </th>
									<th> Strac. </th>
									<th> +/- </th>
								</tr>
							<?php

	// ------------------------------- POBIERANIE TABELI SEZONU GRUPY DRUGIEJ ----------------
							try
							{
								$sql = "SELECT * FROM $sezon_tabela WHERE grupa=2 ORDER BY pkt DESC";
								$result_tabela = $pdo->query($sql);
							}
							catch(PDOException $e)
							{
								echo "<div id='error'>". $e ."</div>";
							}
							while ($row = $result_tabela->fetch())
							{
								$grupa_2_tabela[] = array('nazwa' => $row['nazwa'],
												   'pkt' => $row['pkt'],
												   'z' => $row['zwyciestwa'],
												   'r' => $row['remisy'],
												   'p' => $row['przegrane'],
												   'zdobyte' => $row['zdobyte'],
												   'stracone' => $row['stracone']);
							}
							$i=1;
							foreach ($grupa_2_tabela as $grupa_2_tabela) 
							{
								$bilans = $grupa_2_tabela['zdobyte'] - $grupa_2_tabela['stracone'];
								if($bilans > 0){
									$bilans = "+". $bilans;
								}	
								echo "<tr>
										<td> $i. </td>
										<td>". $grupa_2_tabela['nazwa'] ."</td>
										<td>". $grupa_2_tabela['pkt'] ."</td>
										<td>". $grupa_2_tabela['z'] ."</td>
										<td>". $grupa_2_tabela['r'] ."</td>
										<td>". $grupa_2_tabela['p'] ."</td>
										<td>". $grupa_2_tabela['zdobyte'] ."</td>
										<td>". $grupa_2_tabela['stracone'] ."</td>
										<td>". $bilans ."</td>
									 </tr>";
								$i++;
							}
							?>
							</table>
						</div>
						<div style="clear: both;"></div>
					</div>
					<?php
					// --------------- POBIERANIE TABELI SEZONU GRUPY PIERWSZEJ ------------------
						try
						{
							$sql = "SELECT * FROM $sezon_tabela WHERE grupa=1 ORDER BY pkt DESC";
							$result_tabela = $pdo->query($sql);
						}
						catch(PDOException $e)
						{
							echo "<div id='error'>". $e ."</div>";
						}

					// --------------- POBIERANIE TABELI SEZONU GRUPY PIERWSZEJ ------------------
						try
						{

							$sql = "SELECT * FROM $sezon_tabela ORDER BY pkt DESC";
							$result_terminarz = $pdo->query($sql);
						}
						catch(PDOException $e)
						{
							echo "<div id='error'>". $e ."</div>";
						}
					}
				?>
		</div>
		
		<?php /* Załadowanie Footer'a z szablonu */ include('szablon/footer.php'); ?>
		
	</div>
</body>
</html>