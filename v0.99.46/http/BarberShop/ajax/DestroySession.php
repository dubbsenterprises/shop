<?php
session_start();
include('../../'.$_SESSION['settings']['templateType'].'/'.$_SESSION['settings']['templateType'].'_functions.php');

if ($_POST['session_name']) {
    $session_name = $_POST['session_name'];
        unset($_SESSION[$session_name]);

    ob_start();
    login_horizontal();
    $response_array['html']         = ob_get_clean();
    $response_array['returnCode']   = 1;
    echo json_encode($response_array);
}
?>