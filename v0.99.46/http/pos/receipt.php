<?
session_start();
include_once('../../includes/shop.php');
$w = $_SESSION['preferences']['receipt_width'];
$cc = 20;
if ($_GET['w'] > 0) { $w = $_GET['w']; }
if ($_GET['cc'] > 0) { $cc = $_GET['cc']; }
$sale = $_SESSION['settings']['site'] != 'sales' ? $_SESSION['sale2'] : $_SESSION['sale'];
ob_start();

if      (!(isset($_GET['receipt_type'])) ) { $receipt_type = 'sales_receipt'; }
else    { $receipt_type = $_GET['receipt_type']; }
?>
<html>
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1"/> 
    <title>SALES RECEIPT</title>
    <?
    $page_code = $mail_code = ob_get_contents();
    ob_clean();
    ?>
    <style type="text/css" media="print">.noprint { display: none; visibility: hidden; }</style>
    <?
    $page_code .= ob_get_contents();
    ob_clean();
    ?>
    </head>
    <body style='background-color: #EEEEEE;'>
    <div style='text-align: center; background-color: white; padding-bottom: 30px; width: <?=$w?>px;'>
        <!-- Receipt Header top DIV-->        
        <div style='padding-left: 5px; padding-right: 5px;'>
            <div style='font-weight: bold; margin-top: 5px; padding-top: 5px; font-size: 0.8em; line-height: 110%; font-family: Arial;'>
                <?=$_SESSION['preferences']['receipt_title']?>
            </div>
            <div style='font-size: 0.6em; line-height: 110%; font-family: Arial; margin-bottom: 5px;'>
                <?=nl2br($_SESSION['preferences']['receipt_header'])?>
            </div>
            <?if ($receipt_type == 'gift_receipt') {?>
                <div style='font-weight: bold; font-size: 0.6em; line-height: 110%; font-family: Arial; margin-bottom: 5px;'>
                    GIFT RECEIPT
                </div>
            <?}else {?>
                <div style='font-weight: bold; font-size: 0.6em; line-height: 110%; font-family: Arial; margin-bottom: 5px;'>
                    SALES RECEIPT
                </div>
            <?}?>
            <div style='font-size: 0.6em; line-height: 110%; font-family: Arial; margin-bottom: 10px;'>
                <?=$sale['receipt_date']?>
            </div>
            <div style='font-size: 0.6em; line-height: 110%; font-family: Arial; margin-bottom: 10px;'>
                Clerk: <?=$sale['clerk']?>
            </div>
        </div>
        
        <!-- Receipt Sale Items DIV-->
        <div style='padding-left: 5px; padding-right: 5px;'>
            <?if ($receipt_type == 'sales_receipt') {?>
            <table style='width: 100%; margin-bottom: 10px;'>
                <tr>
                    <td style='font-size: 0.6em; line-height: 110%; font-family: Arial; border-bottom: 1px solid #8888DD; text-align: left; padding-right: 5px;'>ITEM</td>
                    <td style='font-size: 0.6em; line-height: 110%; font-family: Arial; border-bottom: 1px solid #8888DD; text-align: right; padding-right: 5px;'>QTY</td>
                    <td style='font-size: 0.6em; line-height: 110%; font-family: Arial; border-bottom: 1px solid #8888DD; text-align: right;'>PRICE</td>
                </tr>
                <?
                #Print the items of the sale
                if (is_array($sale['basket']['items'])) {
                    foreach (array_keys($sale['basket']['items']) as $key) {
                        $item = &$sale['basket']['items'][$key];
                        $vals = calc($item['price'], $item['discount'], $item['additional_discount'], $item['tax'], $item['quantity']);
                        $comma = "";
                        if ($item['quantity'] > 0) {?>
                            <tr>
                                <td style='font-size: 0.6em; line-height: 110%; font-family: Arial; text-align: left; padding-top: 3px; padding-right: 5px;'>
                                    <?=$_SESSION['preferences']['brand_name_shown_on_receipts'] == 1 ? $item['brand'] . ' ' : ''?><?=$item['name']?>
                                </td>
                                <td style='font-size: 0.6em; line-height: 110%; font-family: Arial; text-align: right; padding-top: 3px; padding-right: 5px;'>
                                    <?=$item['quantity']?>
                                </td>
                                <td style='font-size: 0.6em; line-height: 110%; font-family: Arial; text-align: right; padding-top: 3px;'>
                                    <?=money($item['price'] * $item['quantity'])?>
                                </td>
                            </tr>
                            <tr>
                                <td style='font-size: 0.6em; line-height: 110%; font-family: Arial; text-align: left; padding-top: 0px' colspan='3'>
                                    price: <?=money($item['price'])?>
                                </td>
                            </tr>
                            <tr>
                                <td style='font-size: 0.6em; line-height: 110%; font-family: Arial; text-align: left; padding-top: 0px' colspan='3'>
                                    <? if ($item['attribute1'] != '') { ?><?=$item['attributename1']?>:<?=$item['attribute1']?><? $comma = ", "; } ?><? if ($item['attribute2'] != '') { ?><?=$comma?><?=$item['attributename2']?>:<?=$item['attribute2']?><? } ?>
                                </td>
                            </tr>
                            <tr>
                              <td style='font-size: 0.5em; line-height: 110%; font-family: Arial; text-align: left; padding-top: 0px' colspan='3'>
                                  <?=$item['barcode']?> (<?=$item['number']?>)
                              </td>
                          </tr>
                            <?if ($item['discount'] > 0) {?>
                                <tr>
                                    <td style='font-size: 0.6em; line-height: 110%; font-family: Arial; text-align: left; padding-top: 0px; padding-right: 5px;' colspan='2'>
                                        discount (<?=number($item['discount'])?>%)
                                    </td>
                                    <td style='font-size: 0.6em; line-height: 110%; font-family: Arial; text-align: right; padding-top: 0px'>
                                        -<?=money($vals['odiscount'])?>
                                    </td>
                                </tr>
                            <?}
                              if ($item['additional_discount'] > 0) {?>
                                <tr>
                                    <td style='font-size: 0.6em; line-height: 110%; font-family: Arial; text-align: left; padding-top: 0px; padding-right: 5px;' colspan='2'>
                                        extra discount (<?=number($item['additional_discount'])?>%)
                                    </td>
                                    <td style='font-size: 0.6em; line-height: 110%; font-family: Arial; text-align: right; padding-top: 0px'>
                                        -<?=money($vals['xdiscount'])?>
                                    </td>
                                </tr>
                            <?}?>
                            <tr>
                                <td colspan='3'>
                                    <hr>
                                </td>
                            </tr>
                        <?}
                      }
                }
                if (is_array($sale['basket']['gift_certificates'])) {
                    foreach (array_keys($sale['basket']['gift_certificates']) as $value) {
                        if (count($sale['basket']['gift_certificates'][$value]) > 0) {?>
                            <tr>
                                <td style='font-size: 0.6em; line-height: 110%; font-family: Arial; text-align: left; padding-top: 3px; padding-right: 5px;'>
                                    gift certificate</td><td style='font-size: 0.6em; line-height: 110%; font-family: Arial; text-align: right; padding-top: 3px; padding-right: 5px;'><?=count($sale['basket']['gift_certificates'][$value])?></td><td style='font-size: 0.6em; line-height: 110%; font-family: Arial; text-align: right; padding-top: 3px;'><?=money($value * count($sale['basket']['gift_certificates'][$value]))?>
                                </td>
                            </tr>
                            <tr>
                                <td style='font-size: 0.6em; line-height: 110%; font-family: Arial; text-align: left; padding-top: 0px' colspan='3'>
                                    value: <?=money($value)?>
                                </td>
                            </tr>                  
                        <?}
                    }
                }?>
                <tr>
                    <td colspan='2' style='font-size: 0.6em; line-height: 110%; font-family: Arial; padding-right: 5px; padding-top: 3px; text-align: right;'>SUBTOTAL</td>
                    <td style='font-size: 0.6em; line-height: 110%; font-family: Arial; text-align: right; padding-top: 3px;'><?=money($sale['totals']['price'] - $sale['totals']['discount'])?></td>
                </tr>
                <?if ($sale['totals']['tax'] > 0) {?>
                <tr>
                    <td colspan='2' style='font-size: 0.6em; line-height: 110%; font-family: Arial; padding-right: 5px; padding-top: 0px; text-align: right;'>TOTAL TAX</td>
                    <td style='font-size: 0.6em; line-height: 110%; font-family: Arial; text-align: right; padding-top: 0px'><?=money($sale['totals']['tax'])?></td>
                </tr>
                <?}?>
                <tr>
                    <td colspan='2' style='font-size: 0.6em; line-height: 110%; font-family: Arial; padding-right: 5px; padding-top: 2px; text-align: right; font-weight: bold;'>
                        TOTAL
                    </td>
                    <td style='font-size: 0.6em; line-height: 110%; font-family: Arial; text-align: right; padding-top: 2px; font-weight: bold;'>
                        <?=money($sale['totals']['price'] - $sale['totals']['discount'] + $sale['totals']['tax'])?>
                    </td>
                </tr>
                <?if (is_array($sale['voucher_payments'])) {
                $vouchers_total = 0;
                foreach (array_keys($sale['voucher_payments']) as $id) {
                    $vouchers_total += $sale['voucher_payments'][$id]['value'];
                }
                if ($vouchers_total > 0) {?>
                <tr>
                    <td colspan='2' style='font-size: 0.6em; line-height: 110%; font-family: Arial; padding-right: 5px; padding-top: 3px; text-align: right;'>
                        PAID BY VOUCHERS
                    </td>
                    <td style='font-size: 0.6em; line-height: 110%; font-family: Arial; text-align: right; padding-top: 3px;'>
                    <?=money($vouchers_total)?>
                    </td>
                </tr>
                <?}
                }
                if (is_array($sale['card_payments'])) {
                foreach (array_keys($sale['card_payments']) as $id) {?>
                <tr>
                    <td colspan='2' style='font-size: 0.6em; line-height: 110%; font-family: Arial; padding-right: 5px; padding-top: 3px; text-align: right;'>
                        PAID BY <?=strtoupper($sale['card_payments'][$id]['name'])?>
                    </td>
                    <td style='font-size: 0.6em; line-height: 110%; font-family: Arial; text-align: right; padding-top: 3px;'>
                        <?=money($sale['card_payments'][$id]['amount'])?>
                    </td>
                </tr>
                <?}
                }
                if ($sale['cash_payment'] > 0) {?>
                <tr>
                    <td colspan='2' style='font-size: 0.6em; line-height: 110%; font-family: Arial; padding-right: 5px; padding-top: 3px; text-align: right;'>PAID CASH</td>
                    <td style='font-size: 0.6em; line-height: 110%; font-family: Arial; text-align: right; padding-top: 3px;'><?=money($sale['cash_payment'])?></td>
                </tr>
                <tr>
                    <td colspan='3'>                       
                    </td>
                    <td style='border-bottom: 1px solid #8888DD; padding-bottom: 5px;'>
                    </td>
                </tr>
                <tr>
                    <td colspan='2' style='font-size: 0.6em; line-height: 110%; font-family: Arial; padding-right: 5px; padding-top: 3px; text-align: right;'>
                        CHANGE
                    </td>
                    <td style='font-size: 0.6em; line-height: 110%; font-family: Arial; text-align: right; padding-top: 3px;'>
                        <?=money($sale['change']['cash'])?>
                    </td>
                </tr>
                <?}
                if ($sale['change']['voucher'] > 0) {?>
                <tr
                    <td colspan='2' style='font-size: 0.6em; line-height: 110%; font-family: Arial; padding-right: 5px; padding-top: 3px; text-align: right;'>
                        OVERPAYED
                    </td>
                    <td style='font-size: 0.6em; line-height: 110%; font-family: Arial; text-align: right; padding-top: 3px;'>
                        <?=money($sale['change']['voucher'])?>
                    </td>
                </tr>   
                <?}?>
            </table>
            <?}?>
        </div>
        
        <!-- Receipt Footer/Bottom DIV-->
        <div style='font-size: 0.6em; line-height: 110%; font-family: Arial; margin-bottom: 10px;'>
            <?=nl2br($_SESSION['preferences']['receipt_footer'])?>
        </div>
        <?
        $mail_code .= ob_get_contents();
        $page_code .= ob_get_contents();
        ob_clean();
        ?>
            <img src='barcode.php?barcode=<?=$sale['receipt_id']?>'/>
        <?
        $page_code .= ob_get_contents();
        ob_clean();
        ?>
        <div style='font-size: 0.6em; line-height: 110%; font-family: Arial;'>
            <?=$sale['receipt_id']?>
        </div>
    </div>
 
        
    <?
    $mail_code .= ob_get_contents();
    $page_code .= ob_get_contents();
    ob_clean();
    ?>
    <form method='post' name='mail_receipt_form' style='margin-top: 10px; text-align: left;' class='noprint'>
        <input class='button' type='button' value='PRINT' onclick='window.print();'/>
        <input class='button margin-left: 5px;' type='button' value='SEND MAIL' onclick='$mail = prompt("To what email address the receipt should be sent to?"); document.getElementById("mail_address").value = $mail; document.mail_receipt_form.submit();'/>
        <input type='hidden' name='mail_address' id='mail_address' value=''/>
    </form>
    <?
    $page_code .= ob_get_contents();
    ob_clean();
    ?>
    </body>
</html>

    
<?
$mail_code .= ob_get_contents();
if (isset($_POST['mail_address'])) {
    mail($_POST['mail_address'], 'sale receipt', $mail_code);
}
$page_code .= ob_get_contents();
ob_end_clean();
print $page_code;
?>