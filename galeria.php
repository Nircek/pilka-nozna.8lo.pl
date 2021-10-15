<?php
	session_start();
?>
<!DOCTYPE html>
<html lang="pl-PL">
<head>
	<?php include('./szablon/meta.php'); ?>
	<title> PIK Piłka Nożna </title>

	<!----------------- STYLE CSS DOTYCZĄCE TYLKO TEJ PODSTRONY STRONY -------------------->
	<style>
	#powrot{
		text-align: left;
		font-size: 18px;
		margin-left: 10px;
	}
	#powrot a{
		color: #ffffff;
		text-decoration: none;
		-webkit-transition: color 0.07s linear 0s;
		transition: color 0.07s linear 0s;
	}
	#powrot a:hover{
		color: rgba(0, 0, 0, 0.6)
	}
	.sezon{
		float: left;
		height: 90px;
		line-height: 300%;
		width: 300px;
		background-color: rgba(0, 0, 0, 0.3);
		font-size: 30px;
		font-weight: bold;
		text-align: center;
		margin: 0 10px;
		margin-top: 25px;
	}
	.sezon a{
		color: rgba(255, 255, 255, 1);
		display: block;
		text-decoration: none;
		width: 300px;
		height: 90px;
		-webkit-transition: background-color 0.15s linear 0s;
		transition: background-color 0.15s linear 0s;
	}
	.sezon a:hover
	{
		background-color: rgba(190, 190, 190, 1);
		color: rgba(0, 0, 0, 1);
	}
	.zdjecie{
		float: left;
		margin: 10px;
		cursor: pointer;
	}
	#podglad{
		width: 940px;
		margin: auto;
		margin-top: 30px;
	}
	#glowne_zdjecie{
		float: left;
		position: relative;
		max-width:940px;
		min-width:300px;
		margin: auto;
	}
	#lewo, #prawo {
		user-select: none;
		cursor: pointer;
		position: relative;
		float: left;
		text-align: center;
		height: 130px;
		width: 50px;
		font-size: 50px;
		line-height: 230%;
		background-color: rgba(0, 0, 0, 0.9);
		float: left;
		margin-top: 170px;
		z-index: 5;
	}
	#lewo{
		margin-right: -50px;
	}
	#prawo{
		margin-left: -50px;
	}
	</style>
	<script type="text/javascript">
		function laduj(numer_zdjecia){
			// Podmiana zdjęcia w podglądzie
			var zdjecie = document.getElementById(numer_zdjecia);
			var sciezka = zdjecie.getAttribute("srcfull");
			var glowne_zdjecie = document.getElementById('glowne_zdjecie');
			glowne_zdjecie.value = numer_zdjecia;
			glowne_zdjecie.src = sciezka;
		}

		function lewo(){
			var podglad = document.getElementById('glowne_zdjecie');
			var stare_zdjecie = podglad.value;
			stare_zdjecie = Number(stare_zdjecie);
			var nowe_zdjecie = stare_zdjecie - 1;
			var nowa_sciezka = document.getElementById(nowe_zdjecie);
			nowa_sciezka = nowa_sciezka.getAttribute('srcfull');
			podglad.src = nowa_sciezka;
			podglad.value = nowe_zdjecie;
		}

		function prawo(liczba_zdjec){
			var podglad = document.getElementById('glowne_zdjecie');
			var stare_zdjecie = podglad.value;
			stare_zdjecie = Number(stare_zdjecie);
			var nowe_zdjecie = stare_zdjecie + 1;
			var nowa_sciezka = document.getElementById(nowe_zdjecie);
			nowa_sciezka = nowa_sciezka.getAttribute('srcfull');
			podglad.src = nowa_sciezka;
			podglad.value = nowe_zdjecie;

		}
	</script>
</head>
<body  onload="laduj(0)"> <!-- Gdy jeszcze nie ma załadowanego zdjęcia to ładuje pierwsze czyli 0 -->
	<div id="container">
		<?php include('./szablon/menu.php'); ?>

		<div id="content-border">
			<div id="content">
				<?php
					if(isset($_GET['s']))
						echo '<div id="powrot"><a href="galeria"> &#8592; POWRÓT </a></div>';
				?>
				<h1> GALERIA </h1>
				<div style="clear: both;"></div>
				<?php

					//sprawdzanie czy użytkownik wybrał, któryś sezon
					if(isset($_GET['s'])){
						$sezon = $_GET['s'];
						//Sezon zapisywany w bazie to tylko jego liczba początkowa
						//2016/2017 to tylko 2016, dlatego $_GET musi rozdzielić te dwie liczby
						$sezon_arr = explode("/", $sezon);
						$sezon = $sezon_arr[0];

						//Nawiązywanie połączenia z bazą
						include("./skrypty/db-connect.php");

						//Pobieranie z bazy sciezke zdjęć z danego sezonu
						try {
							//Wysyłanie zapytania
							//$sql = "SELECT * FROM zdjecia WHERE sezon = '$sezon' ORDER BY data ";
							//$result = $pdo->query($sql);

              $sql=$pdo->prepare("SELECT * FROM zdjecia WHERE sezon=? ORDER BY data");
              $sql->bindValue(1, $sezon);


              $sql->execute();
							$liczba_zdjec = $sql->rowCount();
						} catch (PDOException $e) {
							echo 'Błąd bazy danych: '. $e .'</div>';
						}
						//Przypisanie każdej sciezce klucza $i++ gdzie $i to kolejna liczba całkowita
						for($i=0; $row = $sql->fetch(PDO::FETCH_ASSOC); $i++)
							$zdjecie[] = array("$i" => $row['sciezka']);

						//Jeśli są:
						//Wyświetla na samej górze jedno duże zdjęcie 'podglad'
						echo "<div id='podglad'>
									<div id='lewo' onclick='lewo()'></div>
									<img id='glowne_zdjecie' src='".$zdjecie[0][0]."' value=''/>
									<div id='prawo' onclick='prawo()'></div>
									<div style='clear: both'></div>
								</div>";
						//Wypisywanie wszystkich zdjęć wraz z odpowiadającymi im ścieżkami
						$i=0;
						foreach($zdjecie as $zdjecie) {
							//W 'id' i skrypcie 'laduj()' znajduje się taka sama liczba przez co JS może ją stąd pobrać
							//Jak pobierze liczbę w ID to od razu zna liczbę sciezki przez co może ją dopasować i podmienić w zdjęciu na 'podgladzie'
							$pathinf = pathinfo($zdjecie[$i]);
							echo "<div class='zdjecie' >
										<img width='172' id='" . $i ."' height='98' src='" . $pathinf['dirname'].'/thumb.'.$pathinf['basename'] ."' srcfull='" . $zdjecie[$i] ."' onclick='laduj($i)'/>
									</div>";
							$i++;
						}
						echo "<div style='clear: both;'></div>";

					}	else {
						//Jeśli nie wybrano jeszcze sezonu to wyświetla się menu, z pobranymi z bazy danych wszystkimi sezonami

						include("./skrypty/db-connect.php");
						//Pobieranie z bazy wszystkich sezonów (2014/2015 itp...)
						try {
							$sql = "SELECT DISTINCT	sezon FROM zdjecia ORDER BY sezon DESC";
							$result = $pdo->query($sql);
						}	catch (PDOException $e) {
							echo '<div id="error">Błąd bazy danych: '. $e .'</div>';
						}

						$sezon = array();
						while ($row = $result->fetch())
							$sezon[] = array('sezon' => $row['sezon']);

						//Składanie "kafelka" z odnośnikiem do galerii danego sezonu
						foreach($sezon as $sezon) {
							echo "<div class='sezon'>
										<a href='?s=".$sezon['sezon']."/".($sezon['sezon'] +1)."'>".
										 $sezon['sezon']."/".($sezon['sezon'] +1)
									  . "</a>
									</div>";
						}
						echo '<div style="clear: both;"></div>';
					}

				?>
			</div>
		</div>

		<?php include('./szablon/footer.php'); ?>

	</div>
</body>
</html>
