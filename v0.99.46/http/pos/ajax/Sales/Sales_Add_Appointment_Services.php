<?php 
include('../../../../includes/general_functions.php');
include('../../../../includes/inventory_management_functions.php');
include('../../../../includes/appointment_functions.php');
$inventory_dal      = new INVENTORY_DAL();
$appointment_dal    = new Appointments_DAL();

if ($_POST['action'] == 'Sales_Add_Appointment_Services') {
    $appointment_id                     = urldecode($_POST['appointment_id']);
    if(isset($_SESSION['sale']))        { unset($_SESSION['sale']); }
    $sale                               = &$_SESSION['sale'];

    $Appointment_Info                   = $appointment_dal->Appointments_displayAppointmentInfo($appointment_id);
    $_SESSION['sale']['customer_id']    = $Appointment_Info[0]->customer_id;

    $Appointment_Services_Info          = $appointment_dal->Appointments_displayAppointmentServices_by_appointment_ID($appointment_id);
    foreach ($Appointment_Services_Info as $Service_info) {
        $item_id            = $Service_info->service_id;
        $Item_info          = $inventory_dal->Inventory_ItemInfoByItemID($item_id);

        $item               = &$sale['basket']['items'][$item_id];
        if( isset($item['quantity']) ) {$item['quantity']++;} else {$item['quantity'] = 1; }
        $item['real_discount']      = $item['discount']     = $Item_info[0]->discount;
        $item['real_price']         = $item['price']        = $Item_info[0]->price;
        $item['rtax']               = 0;
        $item['tax']                = 0;
        $item['brand']              = $Item_info[0]->brand;
        $item['name']               = $Item_info[0]->name;
        $item['attribute1']         = $Item_info[0]->attribute1;
        $item['attributename1']     = $Item_info[0]->attribute1;
        $item['attribute2']         = $Item_info[0]->attribute2;
        $item['attributename2']     = $Item_info[0]->attribute2;
        $item['barcode']            = $Item_info[0]->barcode;
        $item['number']             = $Item_info[0]->number;
        $sale['basket']['lastitemid'] = $item['id'] = $item_id;
    }

    $response_array['returnCode']               = 1;
    $response_array['itemsArray']               = $_SESSION['sale']['basket']['items'];
    $response_array['appointment_id']           = $appointment_id;
    echo json_encode($response_array);
}
?>