<div id="footer">
		<div id="left-footer">
			<ol>
				<li><a href="informacje"> INFORMACJE </a></li>
				<li><a href="o-nas"> O NAS </a></li>
				<li><a href="kontakt"> KONTAKT </a></li>
				<li><a href="regulamin"> REGULAMIN </a></li>
			</ol>
		</div>
		<div id="center-footer">
			<div id="top-center-footer"></div>
			<div id="bottom-center-footer">
				&copy <?php $rok = date('Y'); echo $rok;?>
			</div>
		</div>
		<div id="right-footer">
			<ol>
				<li>
					<a href=<?php /* tą zmienną deklaruje menu.php*/ echo "'sezony.php?s=$obecny_sezon'";?>>
						OBECNY SEZON
					</a>
				</li>
				<li>
					<a href="sezony.php">
						WSZYSTKIE SEZONY
					</a>
				</li>
			</ol>
		</div>
		<div style="clear: both;"></div>
</div>
