<?php
session_start();
include_once('../../../includes/general_functions.php');
include_once('../../../includes/inventory_management_functions.php');
include_once('../../../includes/appointment_functions.php');
include_once('../../../includes/customers_functions.php');
$IMAGE_DAL = new IMAGE_DATA_DAL();
if ( $_GET['part']          == "mainmenu"){
    include_once('../../'.$_SESSION['settings']['templateType'].'/'.$_SESSION['settings']['templateType'].'_functions.php');
    mainmenu();    
} elseif ( $_GET['part']    == "services"){
    include_once('../../'.$_SESSION['settings']['templateType'].'/'.$_SESSION['settings']['templateType'].'_functions.php');
    services($IMAGE_DAL);
} elseif ( $_GET['part']    == "about_us"){
    include_once('../../'.$_SESSION['settings']['templateType'].'/'.$_SESSION['settings']['templateType'].'_functions.php');
    about_us($IMAGE_DAL);
} elseif ( $_GET['part']    == "location"){
    include_once('../../'.$_SESSION['settings']['templateType'].'/'.$_SESSION['settings']['templateType'].'_functions.php');
    location($IMAGE_DAL);
} elseif ( $_GET['part']    == "packages"){
    include_once('../../'.$_SESSION['settings']['templateType'].'/'.$_SESSION['settings']['templateType'].'_functions.php');
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
} elseif (  $_GET['part']   == "iframe_appointments"){
    unset($_SESSION['appointment_book']);
    $_SESSION['appointment']['step'] = 1;
    $_SESSION['appointment']['step1']= 1;
    if (isset($_SESSION['appointment']['step2'])) { $_SESSION['appointment']['step2'] = 2; }
    if (isset($_SESSION['appointment']['step3'])) { $_SESSION['appointment']['step3'] = 2; }
    if (isset($_SESSION['appointment']['step4'])) { $_SESSION['appointment']['step4'] = 2; }
    $general_dal = new GENERAL_DAL();
    list($host,$domain) = setup_path_general();
    $http_host = $_SERVER['HTTP_HOST'];
    list($defaultPOS,$posurl,$template_function,$include_file) = Company_Setup_Company($general_dal,$host,$domain,$http_host);
    include_once('../../'.$include_file);
    default_header();?>
    <div id="body_div" class="wp100 hp100 d_InlineBlock center main_bc_color1 main_color1_text" style="min-width:600px; min-height:300px;">
        <?appointments();?>
    </div>
    <? } else {
        mainmenu();
    }
?>
