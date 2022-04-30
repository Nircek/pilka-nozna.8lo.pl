<?php
is_logged();
$autor = 0;
header('Location: ' . PANEL_URL);

$tytul = $_POST['info_tytul'];
$tresc = $_POST['info_tresc'];
if (empty($tytul) or empty($tresc)) {
    report_error("Oba pola muszą być wypełnione!", NULL);
    exit();
}
PDOS::Instance()->prepare( // add_info(title, author, content)
    "INSERT INTO `ng_article` (`season_id`, `title`, `author_id`, `publish_on_news_page`, `is_subpage`, `content`, `created_at`) SELECT
        `season_id`, ?, ?, 1, 0, ?, CURDATE()
    FROM `ng_season` ORDER BY `created_at` DESC LIMIT 1;"
)->execute([$tytul, $autor, $tresc]);
exit();
