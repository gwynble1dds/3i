<?php
require_once 'db_config.php';


if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}


header('Location: login.php');
exit();
?>