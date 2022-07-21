<?php

global $config_ini;

if (is_logged(false)) {
    header("Location: " . PANEL_URL);
    exit();
}

function page_perform()
{
    global $config_ini;

    $login = filter_input(INPUT_POST, 'login');
    $password = filter_input(INPUT_POST, 'password');

    if (empty($login) or empty($password)) {
        report_error("Wpisz oba pola!", null);
    } else {
        if ($password === $config_ini['panel_pass'] and $login === $config_ini['panel_user']) {
            $_SESSION['zalogowany'] = true;
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
