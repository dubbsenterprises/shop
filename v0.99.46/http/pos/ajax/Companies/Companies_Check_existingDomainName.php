<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/companies_functions.php');

if ($_GET['DomainName']) {
    $DomainName = $_GET['DomainName'];
    $Company_DAL          = new Companies_DAL();
    $DomainNameCount_fromDomainName = $Company_DAL->Companies_Check_existingDomainName($_SESSION['settings']['company_id'],$DomainName);

    if (count($DomainNameCount_fromDomainName) >= 1){ $switch = 0; }
    else { $switch = 1; }
    $Response = array(  'DomainNameCount_fromDomainName' => count($DomainNameCount_fromDomainName),
                        'existingDomainNameResponse' => $switch
                     );
echo json_encode($Response);
}
?>
