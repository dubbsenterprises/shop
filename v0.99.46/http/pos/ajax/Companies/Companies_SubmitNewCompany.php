<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/companies_functions.php');
$InsertUpdateDelete_Dal = new InsertUpdateDelete_DAL();

if ($_POST['action'] == 'AddCompany') {
    $name           = urldecode($_POST['name']);
    $domainname     = urldecode($_POST['domain']);
    $subdomain      = urldecode($_POST['subdomain']);
    $templateType   = urldecode($_POST['templateType']);
    $defaultPOS     = $_POST['defaultPOS'];

    $sql = "insert into companies
        (name,domain,subdomain,templateType,templateNumber,status,defaultPOS,added)
        values (".quoteSmart($name).",
        ".quoteSmart($domainname).",
        ".quoteSmart($subdomain).",
        ".$templateType.",
        1,
        1,
        0,
        now()
        )";
        $new_company_id = $InsertUpdateDelete_Dal->insert_query($sql);
        $update_id      = $InsertUpdateDelete_Dal->insert_query("insert into logins (company_id,status,username,password,level,added) values ($new_company_id,1,'admin','e4a285cb4f3ac4004f091f38c1b9b95f',2,now())");


    unset($_SESSION['edit_companies']['CompanyAdd']);

    $response_array['returnCode']   = 1;
    $response_array['sql']          = $sql;
    ob_start();
    companies();
    $response_array['html']      = ob_get_clean();

    echo json_encode($response_array);
}
?>