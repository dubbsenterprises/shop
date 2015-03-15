<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/jobs_functions.php');

if ( $_GET['editJobs']) {
    $job_id     = $_GET['job_id'];
    $action     = $_GET['action'];
    $sql        = "update jobs_master set status = $action where id = $job_id";
    $dal        = new InsertUpdateDelete_DAL();
    $update_id  = $dal->insert_query($sql);
    //unset($_SESSION['search_data']['Customers_AllCustomers']['customer_search_inactive_customers']);
    jobs();
}
?>