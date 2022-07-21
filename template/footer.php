<?php
date_default_timezone_set("Europe/Warsaw");
$rok = date('Y');
?>
<div id="footer">
    <div class="left-drawer">
        <ol id="half-links" class="stylenone link">
            <li><a href="<?= PREFIX ?>/informacje"><div class="center-vert"> INFORMACJE </div></a></li>
            <?php foreach (PDOS::Instance()->cmd("get_static_sites()")->fetchAll(PDO::FETCH_ASSOC) as $page): ?>
                <li><a href="<?= PREFIX ?>/static/<?= $page['name'] ?>"><div class="center-vert"> <?= $page['title'] ?> </div></a></li>
            <?php endforeach ?>
        </ol>
    </div>
    <div class="center-drawer">
        <div title="2017-2019 Call3m; 2019-2022 Marcin Zepp (Nircek)" class="center-vert">
            &copy <?= $rok; ?> społeczność PIKa
        </div>
    </div>
    <div class="right-drawer">
        <ol id="whole-links" class="stylenone link">
            <li><a href="<?= PREFIX ?>/sezony/obecny"><div class="center-vert"> OBECNY SEZON </div></a></li>
            <li><a href="<?= PREFIX ?>/sezony"><div class="center-vert"> WSZYSTKIE SEZONY </div></a></li>
        </ol>
    </div>
    <div style="clear: both;"></div>
</div>
