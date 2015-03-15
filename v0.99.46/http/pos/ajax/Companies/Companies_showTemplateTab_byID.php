<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/companies_functions.php');
$companies_dal  = new Companies_DAL();
$templateId     = urldecode($_POST['templateId']);
$company_id     = urldecode($_POST['company_id']);

ob_start();
Companies_showTemplateTab_byID($companies_dal,$company_id,$templateId);
$response_array['html']      = ob_get_clean();

echo json_encode($response_array);
?>