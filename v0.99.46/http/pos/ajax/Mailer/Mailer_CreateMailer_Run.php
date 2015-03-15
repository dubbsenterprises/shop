<?php
include_once('../../../../includes/general_functions.php');
include_once('../../../../includes/mailer_functions.php');
include_once('../../../../includes/customers_functions.php');
$General_DAL        = new InsertUpdateDelete_DAL();
$Mailer_DAL         = new Mailer_DAL();
$Customers_DAL      = new Customers_DAL();

if ( $_GET['createMailer_Run'] == "1") {
    $company_id             = $_GET['company_id'];
    $assigned_login_id      = $_GET['assigned_login_id'];
    $created_by_login_id    = $_GET['created_by_login_id'];
    $mailer_run_template_id = $_GET['mailer_run_template_id'];
    $sql                = "insert into mailer_run   (company_id,mailer_templates_id,assigned_login_id,created_by_login_id,added)
                           values                   ( $company_id,$mailer_run_template_id,$assigned_login_id,$created_by_login_id,now() )" ;
    $mailer_run_id      = $General_DAL->insert_query($sql);
    $_SESSION['mailer_run']['mailer_run_id'] = $mailer_run_id;
    $Active_customers   = $Customers_DAL->get_AllCustomersPerCompanyId($company_id,2,1);
    ### add entry for each customer.
    foreach ($Active_customers as $customer_info) {
            if ($customer_info->email_promotions == 1) {
                $mailer_Run_items_sql = "insert into mailer_run_items (mailer_run_id,customer_id,company_id,added) values ($mailer_run_id,$customer_info->id,$company_id,now() ) ;";
                $result    = $General_DAL->insert_query($mailer_Run_items_sql);
            }
    }
    unset($_SESSION['mailer_run']['created_by_login_id']);
    mailer();
    }
?>
