<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/mailer_functions.php');
$InsertUpdateDelete_Dal         = new InsertUpdateDelete_DAL();
$Mailer_dal                     = new Mailer_DAL();
$response_array['returnCode']   = 0;

if (isset($_POST['action']) && $_POST['action'] == 'SendMail_By_mailer_run_item_id') {
    $mailer_run_items_id        = urldecode($_POST['mailer_run_items_id']);
    $mailer_run_item_info       = $Mailer_dal->mailer_Customer_ID_from_mailer_run_item_id($mailer_run_items_id);
    $mailer_template_name       = $mailer_run_item_info[0]->template_name;
    if ($mailer_run_item_info[0]->status == 0 ) {
        $sql = "update mailer_run_items
                set
                status = 1,
                completed_date = now()
                where id = $mailer_run_items_id ;";
        $InsertUpdateDelete_Dal->query($sql);
        ##  Send the Email.
        $mailer_template_name($mailer_run_item_info[0]->customer_id,0);
        
        $response_array['returnCode']       = 1;
        $response_array['customer_id']      = $mailer_run_item_info[0]->customer_id;
        $response_array['template_name']    = $mailer_run_item_info[0]->template_name;
        $response_array['sql']              = $sql;
        sleep(1);
    }
}
    echo json_encode($response_array);
?>
