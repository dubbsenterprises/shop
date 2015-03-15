<?
session_start();
include_once('../../includes/shop.php');
include_once('../../includes/reports_functions.php');
include_once('../../includes/general_functions.php');
$reportDAL      = new DAL();
$general_dal    = new GENERAL_DAL();
$w = $_SESSION['preferences']['receipt_width'];
$cc = 20;
if ($_GET['w'] > 0) { $w = $_GET['w']; }
if ($_GET['cc'] > 0) { $cc = $_GET['cc']; }
$sale = $_SESSION['settings']['site'] != 'sales' ? $_SESSION['sale2'] : $_SESSION['sale'];

$PreferenceData             = $general_dal->get_CompanyPreference_by_Company_ID($_SESSION['settings']['company_id'],'timezone');
date_default_timezone_set($PreferenceData[0]->value);
$intNow                     = mktime();
$to=$from=date("Y-m-d",$intNow);
ob_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1"/> 
    <title>SALES RECEIPT</title>
<?  $page_code = $mail_code = ob_get_contents();
    ob_clean();
?>
    <style type="text/css" media="print">.noprint { display: none; visibility: hidden; }</style>
<?
    $page_code .= ob_get_contents();
    ob_clean();
?>
    <link rel="stylesheet" type="text/css" href="includes/pos.css"/>
    <link rel="stylesheet" type="text/css" href="includes/printpos.css" media="print"/>
    <link rel="stylesheet" type="text/css" href="includes/jQueryCSS/smoothness/jquery-ui-1.8.9.custom.css"/>
  </head>
  <body style='background-color: #EEEEEE;'>
    <div style='text-align: center; background-color: white; padding-bottom: 30px; width: <?=$w?>px; width:100%'>
      <div style='font-weight: bold; margin-top: 5px; padding-top: 5px; font-size: 0.8em; line-height: 110%; font-family: Arial;'><?=$_SESSION['preferences']['receipt_title']?></div>
      <div style='font-size: 0.6em; line-height: 110%; font-family: Arial; margin-bottom: 5px;'><?=nl2br($_SESSION['preferences']['receipt_header'])?></div>
      <div style='font-size: 0.7em; line-height: 110%; font-family: Arial; font-weight: bold;''>DAILY LEDGER:</div>
      <div style='font-size: 0.6em; line-height: 110%; font-family: Arial; margin-bottom: 5px;'><?=$from?> - <?=$to?></div>

       <?daily_lender_sales_summary($reportDAL,$to,$from);?>
      <br>
       <?daily_lender_payment_summary($reportDAL,$to,$from);?>
       <br>
       <?daily_lender_card_payment_summary($reportDAL, $to, $from);?>
       <br>
       <?daily_lender_voucher_payment_summary($reportDAL, $to, $from);?>
  </body>
</html>
<?
$mail_code .= ob_get_contents();
$page_code .= ob_get_contents();
ob_end_clean();
print $page_code;
?>