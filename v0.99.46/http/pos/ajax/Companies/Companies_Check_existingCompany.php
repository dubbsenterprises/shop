<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/companies_functions.php');

if ($_GET['companyName']) {
    $companyName = $_GET['companyName'];
    $Company_DAL          = new Companies_DAL();
    $companyNameCount_fromCompanyName = $Company_DAL->Companies_Check_existingCompany($_SESSION['settings']['company_id'],$companyName);

    if (count($companyNameCount_fromCompanyName) >= 1){ $switch = 0; }
    else { $switch = 1; }
    $Response = array(  'companyNameCount_fromCompanyName' => count($companyNameCount_fromCompanyName),
                        'existingCompanyResponse' => $switch
                     );
echo json_encode($Response);
}
?>
