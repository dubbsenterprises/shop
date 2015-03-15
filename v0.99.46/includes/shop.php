<?
include_once('permissions.php');
require_once('shop_general_functions.php');
require_once('dbConfig.php');
require_once('shop_db_functions.php');
require_once('shop_pages_functions.php');
require_once('shop_list_functions.php');
require_once('shop_sale_functions.php');

$server_name = $_SERVER['SERVER_NAME'];

$domain      = preg_split("/\./", $_SERVER['SERVER_NAME']);
$domain      = $domain[1] . "." . $domain[2];
$path_split = preg_split("/\//", $_SERVER['SCRIPT_FILENAME']);
$company = $path_split[7];

if (isset($_SERVER['SUBDOMAIN_DOCUMENT_ROOT'])){
    $orig_path_info = realpath($_SERVER['SUBDOMAIN_DOCUMENT_ROOT']);
    $pos = strpos($orig_path_info, '/shop/');
}
else {
    $orig_path_info = realpath($_SERVER['DOCUMENT_ROOT']);
    $pos = strpos($orig_path_info, '/shop/');
}

if ($pos > 0) {
    $_SESSION['settings']['version'] = substr($orig_path_info, $pos + 7, strpos($orig_path_info, '/', $pos + 7) - ($pos + 7));
    $weburl = 'http://' . $_SERVER['HTTP_HOST'] . "/pos";
    $onlineshop = 0;

    $dpos = strrpos(substr($_SERVER['HTTP_HOST'], 0, strrpos($_SERVER['HTTP_HOST'], '.')), '.');
    $domain = $dpos !== false ? substr($_SERVER['HTTP_HOST'], $dpos + 1) : $_SERVER['HTTP_HOST'];
    $_SESSION['settings']['pagetype'] = "shop";
} else {
        echo "problem with line 25ish, $pos, ". $_SERVER['ORIG_PATH_INFO'].",".$_SERVER['SUBDOMAIN_DOCUMENT_ROOT']."\n";
	while (list($var,$value) = each ($_SERVER)) {
		echo "$var => $value <br />";
	}
        exit;
}
$userlevels = array(0 => "employee", 1 => "manager", 2 => "administrator");
?>