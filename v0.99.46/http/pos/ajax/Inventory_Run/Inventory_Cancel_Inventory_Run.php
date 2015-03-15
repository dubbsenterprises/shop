<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/inventory_management_functions.php');
 
if ( $_GET['cancelInventory_Run'] == "1") {
    unset($_SESSION['inventory_run']);
    inventory();
}
?>
