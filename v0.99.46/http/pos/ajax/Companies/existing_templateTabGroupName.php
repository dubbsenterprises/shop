<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/companies_functions.php');

if ($_GET['templateTabGroupName']) {
    $templateTabGroupName = $_GET['templateTabGroupName'];
    $Companies_DAL          = new Companies_DAL();
    $templateTabGroupNameData = $Companies_DAL->Companies_existing_templateTabGroupName($templateTabGroupName);
    $switch = 1;
    if (count($templateTabGroupNameData) > 0){ $switch = 0; }
    
    $Response = array(  'templateTabGroupNameExistResponse' => $switch,
                        'templateTabGroupName' => $templateTabGroupName,
                        'status' => "XXX"
                     );
echo json_encode($Response);
}
?>
