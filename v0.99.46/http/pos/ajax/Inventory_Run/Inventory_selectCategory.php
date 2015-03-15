<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/inventory_management_functions.php');

if ($_GET['category_id']) {
    unset($_SESSION['inventory_run']['active_barcode']);
          $_SESSION['inventory_run']['active_category_id'] = $_GET['category_id'];
    Inventory_showCategory_for_Additions();
}
?>
