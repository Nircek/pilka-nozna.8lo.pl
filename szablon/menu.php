<?php
	if(isset($_SESSION['zalogowany'])) {
		?>
		<div id='zalogowany'
					style='font-weight: bold; padding: 5px 0; margin: auto; text-align: center; background-color: #22c12d; width: 1000px; font-size: 25px;'>
			ADMIN ZALOGOWANY  | <a href=skrypty/logout.php> WYLOGUJ </a> | <a href='admin.php'> PANEL ADMINA </a></div>
		<?php
	}
	//Nawiązywanie połączenia z bazą
	include("skrypty/db-connect.php");
	try{
		$sql = "SELECT * FROM sezony ORDER BY sezon DESC LIMIT 1";
		$result = $pdo->query($sql);
		$liczba = $result->rowCount();
	}catch(PDOException $e){
		echo "<div id='error'>". $e ."</div>";
	}
	if($liczba === 1)
	{
		while ($row = $result->fetch()){
			$obecny_sezon[] = array('sezon' => $row['sezon']);
		}
		foreach ($obecny_sezon as $obecny_sezon) {
			$obecny_sezon = $obecny_sezon['sezon'];
			$obecny_sezon = $obecny_sezon ."/".($obecny_sezon+1);
		}
	}

?>
<div id="menu">
	<div id="logo">
		<a href="strona-glowna"><img src="img/logo.png" height="170" style="margin-top: 5px;"></a>
	</div>
	<div id="title">
		<div id="title-content">
			VIII LO "PIK"
			PIŁKA NOŻNA
		</div>
	</div>
	<div id="options">
		<div id="top-options">
			<div id="facebook">
				<a target="_blank" href="https://www.facebook.com/Pi%C5%82ka-no%C5%BCna-VIII-LO-w-Katowicach-182879961837032/">
					<i class="icon-facebook"></i>
				</a>
			</div>
			<div id="pik">
				<a target="_blank" href="http://8lo.pl/">
					<i class="icon-graduation-cap"></i>
				</a>
			</div>
			<div id="galeria">
				<a href="galeria">
					<i class="icon-camera"></i>
				</a>
			</div>
			<div style="clear: both"></div>
		</div>
		<div id="bottom-options">
			<div id="obecny-sezon">

				<a href=<?php if($liczba === 1){ echo "'sezony.php?s=$obecny_sezon'";} ?>>
						OBECNY SEZON
				</a>
			</div>
			<div id="wszystkie-sezony">
				<a href="sezony.php">
						WSZYSTKIE SEZONY
				</a>
			</div>
			<div style="clear: both;"></div>
		</div>
	</div>
	<div style="clear: both;"></div>
</div>
