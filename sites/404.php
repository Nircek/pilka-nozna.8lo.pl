<?php
require_once(ROOT_PATH . "/utils/register.php");
register_title("Strona nie została znaleziona");

function page_render()
{
?>
    <div id="content">
        <img src="<?= PREFIX ?>/img/error-404.jpg" width='1000' style='margin-left: -20px;'>
    </div>
<?php }
