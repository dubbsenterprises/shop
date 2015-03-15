<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/inventory_management_functions.php');
$serviceORitem_session = $_GET['serviceORitem_session'];
if ( isset($_SESSION[$serviceORitem_session]) ) { unset($_SESSION[$serviceORitem_session]); }

if          ( $serviceORitem_session == 'Items_CreateNewItem') {
    ItemManagement_AddItemStanza();
} else if   ( $serviceORitem_session == 'Items_CreateNewService' ) {
    ItemManagement_AddServiceStanza();
}
?>