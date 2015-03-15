<?
	session_start();
	include_once('../../includes/shop.php');
	if (isset($_POST['gcformat'])) {
		$_SESSION['settings']['gcformat'] = $_POST['gcformat'];
	}
	$w = $_SESSION['preferences']['receipt_width'];
	$cc = 20;
	if ($_GET['w'] > 0) { $w = $_GET['w']; }
	if ($_GET['cc'] > 0) { $cc = $_GET['cc']; }
	$b = $_GET['rv'] == 1 ? 'basket2' : 'basket';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
      <head>
        <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1"/>
        <title>GIFT CERTIFICATES</title>
        <script src="includes/clear-default-text.js" type="text/javascript"></script>
        <link rel="stylesheet" type="text/css" href="includes/pos.css"/>
        <link rel="stylesheet" type="text/css" href="includes/printpos.css" media="print"/>
      </head>
  <body style='background-color: #EEEEEE;'>
<?
    $cur = 0;
    foreach (array_keys($_SESSION['sale'][$b]['gift_certificates']) as $value) {
        foreach ($_SESSION['sale'][$b]['gift_certificates'][$value] as $barcode) {
        $cur++;
            if ($_SESSION['settings']['gcformat'] == 'lf') {?>
                <div class='center bcwhite<?=$cur > 1 ? ' pbb' : ''?>' style='width: <?=$w?>px;'>
                  <div class='bold mb2 mt5'><?=$_SESSION['preferences']['company_name']?></div>
                  <div class='s08 mb3'><?=$barcode?></div>
                  <div class='s07 mb5'>gift certificate value: <?=money($value)?></div>
                  <div class='m0'><img class='m0' src='barcode.php?barcode=<?=$barcode?>' border=0/></div>
                </div>
            <?} else {?>
                <div class='center bcwhite pb30<?=$cur > 1 ? ' pbb' : ''?>' style='width: <?=$w?>px;'>
                  <div class='bold mt5 pt5 s08'><?=$_SESSION['preferences']['receipt_title']?></div>
                  <div class='s06 mb5'><?=nl2br($_SESSION['preferences']['receipt_header'])?></div>
                  <div class='s06 mb15'>GIFT CERTIFICATE</div>
                  <div class='s06 mb10'>VALUE: <?=money($value)?></div>
                  <img src='barcode.php?barcode=<?=$barcode?>'/>
                  <div class='s06'><?=$barcode?></div>
                </div>
            <?}
        }
    }
?>
    <form class='mt10 mb20 left noprint' method='post'>
      <input class='s08 bold button' type='button' value='PRINT' onclick='window.print();'/>
      <select class='ml20' onchange='document.getElementById("gcformat").value = this.value; this.form.submit();'>
        <option value='rf'<?=$_SESSION['settings']['gcformat'] != 'lf' ? ' selected' : ''?>>receipt format</option>
	<option value='lf'<?=$_SESSION['settings']['gcformat'] == 'lf' ? ' selected' : ''?>>label format</option>
      </select>
      <input type='hidden' id='gcformat' name='gcformat' value=''/>
    </form>
  </body>
</html>
