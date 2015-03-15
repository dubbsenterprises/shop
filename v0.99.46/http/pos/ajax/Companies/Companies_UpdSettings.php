<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/companies_functions.php');
session_start();
    $company_id = $_GET['company_id'];
    $setting    = $_GET['setting'];
    $value      = $_GET['value'];

    $sql        = "update companies set $setting=".quoteSmart($value)." where id = $company_id ";
    #echo $sql;
    $dal        = new InsertUpdateDelete_DAL();
    $update_id  = $dal->insert_query($sql);
    companies();
?>