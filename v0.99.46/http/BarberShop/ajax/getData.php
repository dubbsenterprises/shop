<?php
session_start();
include_once('../../'.$_SESSION['settings']['templateType'].'/'.$_SESSION['settings']['templateType'].'_functions.php');
include_once("../../../includes/general_functions.php");
$IMAGE_DAL = new IMAGE_DATA_DAL();

if ( $_GET['part']          == "mainmenu"){
    mainmenu();    
} elseif ( $_GET['part']    == "services"){
    services($IMAGE_DAL);
} elseif ( $_GET['part']    == "about_us"){
    about_us($IMAGE_DAL);
} elseif ( $_GET['part']    == "location"){
    location($IMAGE_DAL);
} elseif ( $_GET['part']    == "packages"){
    packages($IMAGE_DAL);
} elseif ( $_GET['part']    == "register"){?>
    <div class="wp50 d_InlineBlock center m5 main_bc_color1 main_color1_text">
    <?make_appointment_step3_RIGHT_NEWUSER('register_only');?>
    </div>
<?}elseif (  $_GET['part']   == "appointments"){
    $_SESSION['appointment']['step'] = 1;
    $_SESSION['appointment']['step1']= 1;
    unset($_SESSION['appointment_book']);
    if (isset($_SESSION['appointment']['step2'])) { $_SESSION['appointment']['step2'] = 2; }
    if (isset($_SESSION['appointment']['step3'])) { $_SESSION['appointment']['step3'] = 2; }
    if (isset($_SESSION['appointment']['step4'])) { $_SESSION['appointment']['step4'] = 2; }
    appointments();
}else {
    mainmenu();
    }
?>
