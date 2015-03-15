<?php
session_start();
require_once('../includes/general_functions.php');
$general_dal = new GENERAL_DAL();
list($host,$domain) = setup_path_general();
$http_host = $_SERVER['HTTP_HOST'];

list($defaultPOS,$posurl,$template_function,$include_file) = Company_Setup_Company($general_dal,$host,$domain,$http_host);

if      ($defaultPOS == 1 )     { header("Location: $posurl"); }
elseif  ($defaultPOS <> NULL)   { include_once('../http/'.$include_file); $template_function(); }
else {
    print "$http_host is not returning any active domains.  Is this a new domain? <br>
        ( Company_Setup_Company(\$general_dal,\$host,\$domain,\$http_host) )";
}
?>