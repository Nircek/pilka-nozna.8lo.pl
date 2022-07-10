<?php
register_style("admin_log");

$ini = load_config_file(ROOT_PATH . "/config.ini");
if (!$ini) {
    $ini = load_config_file(ROOT_PATH . "/config.sample.ini");
}
if (!$ini) {
    report_error("config", "No valid config found.");
    return false;
}

$ADMIN_PASS = $ini['panel_pass'];
$ADMIN_LOGIN = $ini['panel_user'];

// Sprawdzenie czy formularz został wysłany (czu użytkownik kliknął 'zaloguj')
if (is_logged(false)) {
    goto xkcd292;
}

if (isset($_POST['login'])) {
    $login = $_POST['login'];
    $password = $_POST['password'];

    if (empty($login) or empty($password)) {
        report_error("Wpisz oba pola!", null);
    } else {
        if ($password == $ADMIN_PASS and $login == $ADMIN_LOGIN) {
            $_SESSION['zalogowany'] = true;
            xkcd292:
            header("Location: " . PANEL_URL);
            exit();
        } else {
            report_error("Niepoprawny login lub hasło!", null);
        }
    }
}

function page_render()
{
    ?>
    <div id="content">
        <h1> LOGOWANIE ADMINISTRATORA </h1>
        <div id="formularz">
            <form method="post" action="#">
                <p class="big"> Login: </p>
                <input type="text" id="login" name="login">
                <p class="big"> Hasło: </p>
                <input type="password" id="password" name="password"><br />
                <input type="submit" value="ZALOGUJ">
            </form>
        </div>
    </div>
<?php
}
