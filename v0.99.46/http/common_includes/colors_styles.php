<?php
if(!isset($_SESSION)){ session_start(); }
function setup_path_common(){
if (substr_count($_SERVER['SERVER_NAME'],".") == 1){
    $domref = "www." . $_SERVER['SERVER_NAME'] ; }
else {
    $domref =          $_SERVER['SERVER_NAME'] ; }
list($host,$domain,$ext) = split("\.",$domref);
$domain .= "." . $ext;
$_SESSION['settings']['domain'] = $domain;
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
return array ($host,$domain);
}
list($host,$domain) = setup_path_common();
include_once("includes/general_functions.php");

$general_dal = new GENERAL_DAL();
$IMAGE_DAL   = new IMAGE_DATA_DAL();

if   ( isset($_GET['style']) && $_GET['style'] == "Include" ) {$display = "Include";}
else { $display = "Style";}

if   ( isset($_SESSION['settings']['company_id']) ) { $company_id = $_SESSION['settings']['company_id'];}

$main_bc_color1         = $general_dal->get_Company_Preference($company_id,'main_bc_color1');
$main_color1_text       = $general_dal->get_Company_Preference($company_id,'main_color1_text');
$main_bc_color1_light   = $general_dal->get_Company_Preference($company_id,'main_bc_color1_light');
$main_color1_light_text = $general_dal->get_Company_Preference($company_id,'main_color1_light_text');

$main_bc_color2         = $general_dal->get_Company_Preference($company_id,'main_bc_color2');
$main_color2_text       = $general_dal->get_Company_Preference($company_id,'main_color2_text');
$main_bc_color2_light   = $general_dal->get_Company_Preference($company_id,'main_bc_color2_light');
$main_color2_light_text = $general_dal->get_Company_Preference($company_id,'main_color2_light_text');

$main_bc_color3         = $general_dal->get_Company_Preference($company_id,'main_bc_color3');
$main_color3_text       = $general_dal->get_Company_Preference($company_id,'main_color3_text');
$main_bc_color3_light   = $general_dal->get_Company_Preference($company_id,'main_bc_color3_light');
$main_color3_light_text = $general_dal->get_Company_Preference($company_id,'main_color3_light_text');

$main_page_background   = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID($company_id,'main_page_background');

if      ($display == "Include") { header("Content-type: text/css"); }
else if ($display == "Style")   { print "<style>";  }
?>

.main_bc_color1 {
    background:<?=$main_bc_color1[0]->value?>;
}
.main_color1_text {
    color:<?=$main_color1_text[0]->value?>;
}

.main_bc_color1_light {
    background:<?=$main_bc_color1_light[0]->value?>;
}

.main_color1_light_text {
    color:<?=$main_color1_light_text[0]->value?>;
}


.main_bc_color2 {
    background:<?=$main_bc_color2[0]->value?>;
}
.main_color2_text {
    color:<?=$main_color2_text[0]->value?>;
}
.main_bc_color2_light {
    background:<?=$main_bc_color2_light[0]->value?>;
}
.main_color2_light_text {
    color:<?=$main_color2_light_text[0]->value?>;
}


.main_bc_color3 {
    background:<?=$main_bc_color3[0]->value?>;
}
.main_color3_text {
    color:<?=$main_color3_text[0]->value?>;
}
.main_bc_color3_light {
    background:<?=$main_bc_color3_light[0]->value?>;
}
.main_color3_light_text {
    color:<?=$main_color3_light_text[0]->value?>;
}

<? if (is_object($main_page_background) ) { ?>
    .main_page_background {
        background: url("/pos/showimage.php?id=<?=$main_page_background[0]->image_id?>&image_db_id=<?=$main_page_background[0]->image_db_id?>") no-repeat scroll 0 0 #000000;
        height: 813px;
        margin: auto;
        width: 1021px;
    }
<?}?>

<?if ($display == "Style") {
     print "</style>";
}?>
