<?php
require_once(ROOT_PATH . "/utils/register.php");
register_title("Strona nie została znaleziona");

function page_render()
{
    ?>
    <div id="content" class="fullish">
        <img src="<?= PREFIX ?>/img/error-404.jpg">
    </div>
<?php
}
