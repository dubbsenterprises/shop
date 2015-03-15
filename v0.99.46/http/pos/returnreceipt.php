<?

	session_start();
	include_once('../../includes/shop.php');

	$w = $_SESSION['preferences']['receipt_width'];
	$cc = 20;

	if ($_GET['w'] > 0) { $w = $_GET['w']; }
	if ($_GET['cc'] > 0) { $cc = $_GET['cc']; }

	$return = $_SESSION['settings']['site'] != 'returns' ? $_SESSION['return2'] : $_SESSION['return'];

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1"/> 
    <title>RETURN RECEIPT</title>
    <script src="includes/clear-default-text.js" type="text/javascript"></script>
    <link rel="stylesheet" type="text/css" href="includes/pos.css"/>
    <link rel="stylesheet" type="text/css" href="includes/printpos.css" media="print"/>
  </head>
  <body>
    <div class='center bcwhite pb30' style='width: <?=$w?>px;'>
      <div class='bold mt5 pt5 s08'><?=$_SESSION['preferences']['return_receipt_title']?></div>
      <div class='s06 mb5'><?=nl2br($_SESSION['preferences']['return_receipt_header'])?></div>
      <div class='s07 bold mb5'>RETURN RECEIPT</div>
      <div class='s06 mb10'><?=isset($return['receipt_date']) ? $return['receipt_date'] : date("m/d/y h:ia")?></div>
      <div class='s06 mb10'>Clerk: <?=$return['clerk']?></div>
      <div class='pl5 pr5'>
        <table class='wp100 mb10'>
	  <tr><td class='s06 b1sb left pr5'>ITEM</td><td class='s06 b1sb right pr5'>QTY</td><td class='s06 b1sb right'>PRICE</td></tr>
<?

	if (is_array($return['items'])) {
		$totalprice = $totaltax = $totaldiscount = 0;
		foreach (array_keys($return['items']) as $key) {
			$item = $return['items'][$key];
			$comma = "";
			$vals = calc($item['price'], $item['discount'], $item['additional_discount'], $item['tax']);
			$totalprice += $item['quantity'] * $vals['price'];
			$totaltax += $item['quantity'] * $vals['tax'];
			$totaldiscount += $item['quantity'] * $vals['discount'];

?>
          <tr><td class='s06 left pt3 pr5'><?=$_SESSION['preferences']['brand_name_shown_on_receipts'] == 1 ? $item['brandname'] . ' ' : ''?><?=$item['name']?></td><td class='s06 right pt3 pr5'><?=$item['quantity']?></td><td class='s06 right pt3'><?=money($item['price'] * $item['quantity'])?></td></tr>
          <tr><td class='s06 left pt0' colspan='3'><? if ($item['attribute1'] != '') { ?><?=$item['attributename1']?>:<?=$item['attribute1']?><? $comma = ", "; } ?><? if ($item['attribute2'] != '') { ?><?=$comma?><?=$item['attributename2']?>:<?=$item['attribute2']?><? } ?></td></tr>
          <tr><td class='s05 left pt0' colspan='3'><?=$item['barcode']?> (<?=$item['number']?>)</td></tr>
<?

			if ($vals['odiscount'] > 0) {

?>
          <tr><td class='s06 left pt0 pr5' colspan='2'>discount (<?=number($item['discount'])?>%)</td><td class='s06 right pt0'>-<?=money($item['quantity'] * $vals['odiscount'])?></td></tr>
<?

			}

			if ($vals['xdiscount'] > 0) {

?>
          <tr><td class='s06 left pt0 pr5' colspan='2'>extra discount (<?=number($item['additional_discount'])?>%)</td><td class='s06 right pt0'>-<?=money($item['quantity'] * $vals['xdiscount'])?></td></tr>
<?

			}
		}
	}

?>
	  <tr><td colspan='2'></td><td class='b1sb pb5'></td></tr>
	  <tr><td colspan='2' class='s06 pr5 pt3 right'>SUBTOTAL</td><td class='s06 right pt3'><?=money($totalprice - $totaldiscount)?></td></tr>
	  <tr><td colspan='2' class='s06 pr5 pt0 right'>TAX</td><td class='s06 right pt0'><?=money($totaltax)?></td></tr>
	  <tr><td colspan='2' class='s06 pr5 pt2 right bold'>TOTAL</td><td class='s06 right pt2 bold'><?=money($totalprice - $totaldiscount + $totaltax)?></td></tr>
	</table>
      </div>
      <div class='s06 mb10'><?=nl2br($_SESSION['preferences']['return_receipt_footer'])?></div>
      <img src='barcode.php?barcode=<?=$return['receipt_id']?>'/>
      <div class='s06'><?=$return['receipt_id']?></div>
    </div>
    <div class='mt10 mb20 left noprint'><input type='button' value='PRINT' onclick='window.print();'/></div>
  </body>
</html>
