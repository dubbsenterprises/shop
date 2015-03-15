<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/inventory_management_functions.php');

if ($_GET['style_number']) {
    unset($_SESSION['inventory_run']['active_barcode']);
          $_SESSION['inventory_run']['active_style_number'] = $_GET['style_number'];
    Inventory_showStyleNumber_for_Additions();
}
?>
