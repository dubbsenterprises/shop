<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/companies_functions.php');

if ($_GET['SubDomain']) {
    $SubDomain = $_GET['SubDomain'];
    $Company_DAL          = new Companies_DAL();
    $SubDomainCount_fromSubDomain = $Company_DAL->Companies_Check_existingSubDomain($_SESSION['settings']['company_id'],$SubDomain);

    if (count($SubDomainCount_fromSubDomain) >= 1){ $switch = 0; }
    else { $switch = 1; }
    $Response = array(  'SubDomainCount_fromSubDomain' => count(SubDomainCount_fromSubDomain),
                        'existingSubDomainResponse' => $switch
                     );
echo json_encode($Response);
}
?>
