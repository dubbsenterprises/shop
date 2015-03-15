<?
session_start();
if (isset($_SESSION['settings']['site'])) {
    $_SESSION['settings']['lastsite'] = $_SESSION['settings']['site'];
}
function setup_path_massage(){
if (substr_count($_SERVER['SERVER_NAME'],".") == 1){
    $domref = "www." . $_SERVER['SERVER_NAME'] ;}
else {
    $domref =          $_SERVER['SERVER_NAME'] ;}
list($subdomain,$domain,$ext) = split("\.",$domref);
$domain .= "." . $ext;
$_SESSION['settings']['domain']     = $domain;
$_SESSION['settings']['subdomain']  = $subdomain;
#################
if (isset($_SERVER['SUBDOMAIN_DOCUMENT_ROOT'])){
    $orig_path_info = realpath($_SERVER['SUBDOMAIN_DOCUMENT_ROOT']);
    $pos = strpos($orig_path_info, '/shop/');
}
else {
    $orig_path_info = realpath($_SERVER['DOCUMENT_ROOT']);
    $pos = strpos($orig_path_info, '/shop/'); 
}
$shop_path  = substr($orig_path_info, 0, $pos)."/shop/";
$Zend_path  = $shop_path . 'Zend/library/';
$version    = 'v' . substr($orig_path_info, $pos + 7, strpos($orig_path_info, '/', $pos + 7) - ($pos + 7));

set_include_path($shop_path.$version.'/:'.$Zend_path);
return array ($subdomain,$domain);
}

list($subdomain,$domain) = setup_path_massage();
include_once('../../includes/shop.php');
include_once('../../includes/dbConfig.php');
include_once('../../includes/general_functions.php');
include_once('../../includes/shop_processVariables.php');
dbconnect();
processVariables();
$woptions = "width=" . (!isset($_SESSION['preferences']['receipt_width']) || $_SESSION['preferences']['receipt_width'] > 380 ? 400 : $_SESSION['preferences']['receipt_width'] + 50) . ", height=400, screenX=100, screenY=100, scrollbars=yes, resizeable=yes";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html style="width:100%; height:100%;  ">
    <head>
        <title><?=$subdomain?>.<?=$domain?></title>
        <!-- Russ!! -->
        <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1"/>
        <link rel="stylesheet"  type="text/css"         href="/common_includes/colors_styles.php?style=Include" media="screen"/>
        <link rel="stylesheet"  type="text/css"         href="includes/pos.css"/>
        <link rel="stylesheet"  type="text/css"         href="includes/printformats.css" media="print"/>
        <link rel="stylesheet"  type="text/css"         href="includes/jQueryCSS/smoothness/jquery-ui-1.8.9.custom.css"/>
        <script                 type="text/javascript"  src= "includes/jQueryJS/jquery-1.8.1.min.js"></script>
        <script                 type="text/javascript"  src= "includes/jQueryJS/jquery-ui-1.8.9.custom.min.js"></script>
        <script                 type="text/javascript"  src= "/common_includes/common.js"></script>
        <script                 type="text/javascript"  src= "includes/pos.js"></script>
    </head>
    <body style="min-width: 900px; max-width: 1400px; height:90%; max-height:1000px" class='t_align_center p0' onload='<? if ($_SESSION['show_receipt'] == 1) { ?>window.open("receipt.php", "_blank", "<?=$woptions?>"); <? unset($_SESSION['show_receipt']); } ?>
        <? if ($_SESSION['show_return_receipt']   == 1) { ?>window.open("returnreceipt.php", "_blank", "<?=$woptions?>"); <? unset($_SESSION['show_return_receipt']); } ?>
        <? if ($_POST['show_gift_certificates']   == 1) { ?>window.open("giftcertificates.php", "_blank", "<?=$woptions?>"); <? } ?>
        <? if ($_SESSION['show_exchange_receipt'] == 1) { ?>window.open("exchangereceipt.php", "_blank", "<?=$woptions?>"); <? unset($_SESSION['show_exchange_receipt']); } ?> init(<?=currentMilliseconds();?>);'>
        <? if (isset($_GET["load_site"])) { $load_site=$_GET["load_site"]; } ?>

        <form name='page_form' method='post'>
            <input type='hidden' name='post_values' id='post_values' value=''/>
        </form>
        <? if ($GLOBALS['onlineshop'] == 0 && empty($_SESSION['settings']['user'])) {
            unset($_SESSION['settings']['user']);
            unset($_SESSION['settings']['manage']);
            mainLogin();
            $nopage = 1;
        } else {?>
            <div id="pos_header" class='f_left center bcwhite wp100 h50px' style="min-width: 900px;">
                <? pos_header(); ?>
            </div>
            <div class="f_left center wp100 hp100 pt50">
                <div class='d_InlineBlock f_left center  wp15 hp100'>
                    <? left_menu();?>
                </div>
                <div class='d_InlineBlock f_left center wp85 hp100'>
                    <? showpage($load_site); ?>
                </div>
            </div>
        <?}?>
    </body>
</html>