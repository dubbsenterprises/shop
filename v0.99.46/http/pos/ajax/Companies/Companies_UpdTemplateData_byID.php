<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/companies_functions.php');
$InsertUpdateDelete_Dal = new InsertUpdateDelete_DAL();
$companies_dal          = new Companies_DAL();

if ($_POST['action'] == 'updateTabData') {
    $TemplateDataID      = urldecode($_POST['TemplateDataID']);  #The ID number of the data entry.
    $TemplateTabData     = urldecode($_POST['TemplateTabData']); # The data itself
    $TemplateTabGroupID  = urldecode($_POST['TemplateTabGroupID']);
    $TemplateTabId       = urldecode($_POST['TemplateTabId']);   # The ID number of the template Tab.  0 is default company stuff. 
    $Company_ID          = urldecode($_POST['Company_ID']);


if ($TemplateDataID != 0 ) {
    $sql = "UPDATE templateTabsData set value=".quoteSmart($TemplateTabData).",updated=now() where id = $TemplateDataID";
    $InsertUpdateDelete_Dal->query($sql);
} else {
    $sql = "INSERT into templateTabsData
        (templateTabID,company_id,value,added)
        values (
                ".$TemplateTabId.",
                ".$Company_ID.",
                ".quoteSmart($TemplateTabData).",
                now()
                )";
    $new_templateTabsData_id = $InsertUpdateDelete_Dal->insert_query($sql);
}

    $response_array['returnCode']   = 1;
    $response_array['sql']          = $sql;

    ob_start();
    edit_TemplateTabGroupData($companies_dal,$Company_ID,$TemplateTabGroupID);
    $response_array['html']      = ob_get_clean();
    echo json_encode($response_array);
}
?>