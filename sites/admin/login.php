<?php
register_style("admin_log");

$ADMIN_PASS = '<credentials censored>';
$ADMIN_LOGIN = '<credentials censored>';

// Sprawdzenie czy formularz został wysłany (czu użytkownik kliknął 'zaloguj')
if (is_logged(false)) goto xkcd292;

if (isset($_POST['login'])) {
    $login = $_POST['login'];
    $password = $_POST['password'];

    if (empty($login) or empty($password)) {
        report_error("Wpisz oba pola!", NULL);
    } else {
        if ($password == $ADMIN_PASS and $login == $ADMIN_LOGIN) {
            $_SESSION['zalogowany'] = true;
            xkcd292:
            header("Location: " . PANEL_URL);
            exit();
        } else {
            report_error("Niepoprawny login lub hasło!", NULL);
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
                <h3> Login: </h3>
                <input type="text" id="login" name="login"><br />
                <h3> Hasło: </h3>
                <input type="password" id="password" name="password"><br />
                <input type="submit" value="ZALOGUJ">
            </form>
        </div>
    </div>
<?php }
