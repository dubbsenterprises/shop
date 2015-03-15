<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/inventory_management_functions.php');

if ($_GET['Barcode']) {
    unset($_SESSION['inventory_run']['active_style_number']);
          $_SESSION['inventory_run']['active_barcode'] = $_GET['Barcode'];
    Inventory_showStyleNumber_for_Additions();
}
?>
