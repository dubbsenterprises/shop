<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/companies_functions.php');
$InsertUpdateDelete_Dal = new InsertUpdateDelete_DAL();
$companies_dal          = new Companies_DAL();

if ($_POST['action'] == 'AddNewTemplateTab_by_GroupID') {
    $company_id             = urldecode($_POST['company_id']);
    $TemplateTabGroupID     = urldecode($_POST['TemplateTabGroupID']);
    $name                   = urldecode($_POST['name']);
    $dataType               = urldecode($_POST['dataType']);
    $DefaultValue           = urldecode($_POST['DefaultValue']);

    $sql = "insert into templateTabs
        (name,default_value, dataGroup, dataType, status, added)
        values (".quoteSmart($name).",
        ".quoteSmart($DefaultValue).",
        ".quoteSmart($TemplateTabGroupID).",
        ".quoteSmart($dataType).",
        1,
        now()
        )";
        $new_company_id = $InsertUpdateDelete_Dal->insert_query($sql);
    $response_array['returnCode']           = 1;
    $response_array['TemplateTabGroupID']   = $TemplateTabGroupID;
    $response_array['name']                 = $name;
    $response_array['dataType']             = $dataType;
    $response_array['DefaultValue']         = $DefaultValue;
    $response_array['sql']                  = $sql;
    ob_start();
    edit_TemplateTabGroupData($companies_dal,$company_id,$TemplateTabGroupID);
    $response_array['html']      = ob_get_clean();
    echo json_encode($response_array);
}
?>