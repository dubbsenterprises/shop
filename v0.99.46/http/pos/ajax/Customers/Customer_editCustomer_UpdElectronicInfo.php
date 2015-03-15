<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/customers_functions.php');
$InsertUpdateDelete_Dal = new InsertUpdateDelete_DAL();

if ($_POST['field'] == 'email') {
    $customer_id    = urldecode($_POST['customer_id']);
    $field          = urldecode($_POST['field']);
    $value          = urldecode($_POST['email']);
} elseif ($_POST['field'] == 'phone_num') {
    $customer_id    = urldecode($_POST['customer_id']);
    $field          = urldecode($_POST['field']);
    $value          = urldecode($_POST['phone_num']);
} elseif ($_POST['field'] == 'email_promotions') {
    $customer_id    = urldecode($_POST['customer_id']);
    $field          = urldecode($_POST['field']);
    $value          = urldecode($_POST['email_promotions']);
}

    $sql = "update customers set $field = ".quoteSmart($value)." where id = $customer_id";
    $InsertUpdateDelete_Dal->query($sql);

    $response_array['returnCode']   = 1;
    $response_array['sql']          = $sql;
    ob_start();
    customers();
    $response_array['html']      = ob_get_clean();
    echo json_encode($response_array);
?>