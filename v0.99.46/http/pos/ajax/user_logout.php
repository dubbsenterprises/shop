<?php
#include_once    ('../../../includes/functions.php');
require_once('../../../includes/general_functions.php');
    $weburl = 'http://' . $_SERVER['HTTP_HOST'] . "/pos";

    session_unset();
    $_SESSION['keep_debug'] = 1;
    header("Location: $weburl");
    exit();
?>
