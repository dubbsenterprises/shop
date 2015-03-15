<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/inventory_management_functions.php');


if ($_GET['Barcode']) {
    $barcode = $_GET['Barcode'];
    $Inventory_DAL          = new INVENTORY_DAL();
    $Barcode_FromItemID = $Inventory_DAL->ItemManagement_ItemLookup_by_Barcode($_SESSION['settings']['company_id'],$barcode);

    if (count($Barcode_FromItemID) > 0){ $switch = 0; }
    else { $switch = 1; }
    $Response = array(  'BarcodeExistResponse' => $switch,
                        'status' => "XXX"
                     );
echo json_encode($Response);
}
?>
