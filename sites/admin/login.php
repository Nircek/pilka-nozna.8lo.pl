<?php

$ADMIN_PASS = '<credentials censored>';
$ADMIN_LOGIN = '<credentials censored>';

// Sprawdzenie czy formularz został wysłany (czu użytkownik kliknął 'zaloguj')
if (isset($_POST['login'])) {
    $login = $_POST['login'];
    $password = $_POST['password'];

    if (empty($login) or empty($password)) {
        $_SESSION['e_log_pola'] = "Wypisz oba pola!";
    } else {
        if ($password == $ADMIN_PASS and $login == $ADMIN_LOGIN) {
            $_SESSION['zalogowany'] = true;
            header("Location: $PREFIX/admin");
            exit();
        } else {
            $_SESSION['e_log_dane'] = "Niepoprawny login lub hasło!";
        }
    }
}
?>
<?php generate_header("admin,admin_log"); ?>
<div id="content">
    <h1> LOGOWANIE ADMINISTRATORA </h1>
    <?php
    if (isset($_SESSION['e_log_pola'])) {
        echo "<div id='error'> " . $_SESSION['e_log_pola'] . " </div>";
        unset($_SESSION['e_log_pola']);
    } elseif (isset($_SESSION['e_log_baza'])) {
        echo "<div id='error'> " . $_SESSION['e_log_baza'] . " </div>";
        unset($_SESSION['e_log_baza']);
    } elseif (isset($_SESSION['e_log_dane'])) {
        echo "<div id='error'> " . $_SESSION['e_log_dane'] . " </div>";
        unset($_SESSION['e_log_dane']);
    }
    ?>
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

<?php generate_footer(); ?>
