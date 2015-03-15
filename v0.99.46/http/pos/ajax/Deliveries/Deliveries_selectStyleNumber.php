<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/inventory_management_functions.php');

if ($_GET['style_number']) {
    $_SESSION['delivery']['active_style_number'] = $_GET['style_number'];
    Deliveries_showStyleNumber_for_Additions();
}
?>
