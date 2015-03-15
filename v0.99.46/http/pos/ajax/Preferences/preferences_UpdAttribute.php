<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/preferences_functions.php');

    $company_id   = $_GET['company_id'];
    $column       = $_GET['column'];
    $value        = $_GET['value'];
    $existsDAL    = new GENERAL_DAL();
    $existsResults = $existsDAL->check_preferenceExist($company_id,$column);
    if ($existsResults[0]->count > 0 ) {
        $sql        = "update preferences set value=".quoteSmart($value).", updated=now()";
        $sql       .= " where company_id = $company_id and name='$column'";
    }
    else {
        $sql        = "insert into preferences (company_id,name,value,added)";
        $sql       .= " values ($company_id,'$column',".quoteSmart($value).",now())";
    }
    #echo $sql;
    $dal        = new InsertUpdateDelete_DAL();
    $update_id  = $dal->insert_query($sql);
    preferences();
?>