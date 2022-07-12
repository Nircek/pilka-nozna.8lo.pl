<?php

$site_title = HIT_UNPACK();
global $site;
$site = empty($site_title) ? array() : PDOS::Instance()->cmd("get_static_site(title)", [$site_title])->fetchAll(PDO::FETCH_ASSOC);
if (count($site) < 1) {
    NOT_FOUND();
} else {
    function page_init()
    {
        global $site;
        $site = $site[0];
        register_title($site['title']);
        return $site['content'];
    }

    function page_render($obj)
    {
        ?>
        <div id="content">
            <?= $obj ?>
        </div>
    <?php
    }
}
