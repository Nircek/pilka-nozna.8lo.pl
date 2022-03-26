<?php

session_start();
session_unset();
header('Location: ../strona-glowna');
exit();
