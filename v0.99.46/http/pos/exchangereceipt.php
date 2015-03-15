<?
session_start();
include_once('../../includes/shop.php');

$w = $_SESSION['preferences']['receipt_width'];
$cc = 20;

if ($_GET['w'] > 0) {
    $w = $_GET['w'];
}
if ($_GET['cc'] > 0) {
    $cc = $_GET['cc'];
}

$exchange = $_SESSION['settings']['site'] != 'exchanges' ? $_SESSION['exchange2'] : $_SESSION['exchange'];
$sale = $_SESSION['settings']['site'] != 'exchanges' ? $_SESSION['sale2'] : $_SESSION['sale'];
$return = $_SESSION['settings']['site'] != 'exchanges' ? $_SESSION['return2'] : $_SESSION['return'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1"/> 
        <title>EXCHANGE RECEIPT</title>
        <script src="includes/clear-default-text.js" type="text/javascript"></script>
        <link rel="stylesheet" type="text/css" href="includes/pos.css"/>
        <link rel="stylesheet" type="text/css" href="includes/printpos.css" media="print"/>
    </head>
    <body>
        <div class='center bcwhite pb30' style='width: <?= $w ?>px;'>
            <div class='bold mt5 pt5 s08'><?= $_SESSION['preferences']['receipt_title'] ?></div>
            <div class='s06 mb5'><?= nl2br($_SESSION['preferences']['receipt_header']) ?></div>
            <div class='s07 bold mb5'>EXCHANGE RECEIPT</div>
            <div class='s06 mb10'><?= $exchange['receiptdate'] ?></div>
            <div class='s06 mb10'>Clerk: <?= $exchange['clerk'] ?></div>
            <div class='pl5 pr5'>
                <table class='wp100 mb10'>
                    <tr><td class='s06 b1sb left pr5'>ITEM</td><td class='s06 b1sb right pr5'>Q#</td><td class='s06 b1sb right'>PRICE</td></tr>
                    <?
                    if (is_array($sale['basket']['items'])) {
                        foreach (array_keys($sale['basket']['items']) as $key) {
                            $item = $sale['basket']['items'][$key];
                            $vals = calc($item['price'], $item['discount'], $item['additional_discount'], $item['tax'], $item['quantity']);
                            $comma = "";
                            if ($item['quantity'] > 0) {
                                ?>
                                <tr><td class='s06 left pt3 pr5'><?= $_SESSION['preferences']['brand_name_shown_on_receipts'] == 1 ? $item['brand'] . ' ' : '' ?><?= $item['name'] ?></td><td class='s06 right pt3 pr5'><?= $item['quantity'] ?></td><td class='s06 right pt3'><?= money($item['price'] * $item['quantity']) ?></td></tr>
                                <tr><td class='s06 left pt0' colspan='3'>price: <?= money($item['price']) ?></td></tr>
                                <tr><td class='s06 left pt0' colspan='3'><? if ($item['attribute1'] != '') { ?><?= $item['attributename1'] ?>:<?= $item['attribute1'] ?><? $comma = ", ";
                                } ?><? if ($item['attribute2'] != '') { ?><?= $comma ?><?= $item['attributename2'] ?>:<?= $item['attribute2'] ?><? } ?></td></tr>
                                <tr><td class='s05 left pt0' colspan='3'><?= $item['barcode'] ?> (<?= $item['number'] ?>)</td></tr>
                                <?
                                if ($item['discount'] > 0) {
                                    ?>
                                    <tr><td class='s06 left pt0 pr5' colspan='2'>discount (<?= number($item['discount']) ?>%)</td><td class='s06 right pt0'>-<?= money($vals['odiscount']) ?></td></tr>
                                    <?
                                }

                                if ($item['additional_discount'] > 0) {
                                    ?>
                                    <tr><td class='s06 left pt0 pr5' colspan='2'>extra discount (<?= number($item['additional_discount']) ?>%)</td><td class='s06 right pt0'>-<?= money($vals['xdiscount']) ?></td></tr>
                                    <?
                                }
                            }
                        }
                    }

                    if (is_array($sale['basket']['gift_certificates'])) {
                        foreach (array_keys($sale['basket']['gift_certificates']) as $value) {
                            if (count($sale['basket']['gift_certificates'][$value]) > 0) {
                                ?>
                                <tr><td class='s06 left pt3 pr5'>gift certificate</td><td class='s06 right pt3 pr5'><?= count($sale['basket']['gift_certificates'][$value]) ?></td><td class='s06 right pt3'><?= money($value * count($sale['basket']['gift_certificates'][$value])) ?></td></tr>
                                <tr><td class='s06 left pt0' colspan='3'>value: <?= money($value) ?></td></tr>
                                <?
                            }
                        }
                    }

                    if (is_array($return['items'])) {
                        foreach (array_keys($return['items']) as $key) {
                            $item = $return['items'][$key];
                            $comma = "";
                            $vals = calc($item['price'], $item['discount'], $item['additional_discount'], $item['tax'], $item['quantity']);
                            ?>
                            <tr><td class='s06 left pt3 pr5'><?= $_SESSION['preferences']['brand_name_shown_on_receipts'] == 1 ? $item['brandname'] . ' ' : '' ?><?= $item['name'] ?></td><td class='s06 right pt3 pr5'><?= $item['quantity'] ?></td><td class='s06 right pt3'><?= money(-$vals['price']) ?></td></tr>
                            <tr><td class='s06 left pt0' colspan='3'><? if ($item['attribute1'] != '') { ?><?= $item['attributename1'] ?>:<?= $item['attribute1'] ?><? $comma = ", ";
                } ?><? if ($item['attribute2'] != '') { ?><?= $comma ?><?= $item['attributename2'] ?>:<?= $item['attribute2'] ?><? } ?></td></tr>
                            <tr><td class='s05 left pt0' colspan='3'><?= $item['barcode'] ?> (<?= $item['number'] ?>)</td></tr>
                            <?
                            if ($vals['odiscount'] > 0) {
                                ?>
                                <tr><td class='s06 left pt0 pr5' colspan='2'>discount (<?= number($item['discount']) ?>%)</td><td class='s06 right pt0'><?= money($vals['odiscount']) ?></td></tr>
            <?
        }

        if ($vals['xdiscount'] > 0) {
            ?>
                                <tr><td class='s06 left pt0 pr5' colspan='2'>extra discount (<?= number($item['additional_discount']) ?>%)</td><td class='s06 right pt0'><?= money($vals['xdiscount']) ?></td></tr>
                                <?
                            }
                        }
                    }
                    ?>
                    <tr><td colspan='2'></td><td class='b1sb pb5'></td></tr>
                    <tr><td colspan='2' class='s06 pr5 pt3 right'>SUBTOTAL</td><td class='s06 right pt3'><?= money($exchange['totals']['price'] - $exchange['totals']['discount']) ?></td></tr>
                    <tr><td colspan='2' class='s06 pr5 pt0 right'>TAX (<?= $_SESSION['preferences']['tax'] ?>%)</td><td class='s06 right pt0'><?= money($exchange['totals']['tax']) ?></td></tr>
                    <tr><td colspan='2' class='s06 pr5 pt2 right bold'>TOTAL</td><td class='s06 right pt2 bold'><?= money($exchange['totals']['price'] - $exchange['totals']['discount'] + $exchange['totals']['tax']) ?></td></tr>
                    <?
                    if (is_array($exchange['voucher_payments'])) {
                        $vouchers_total = 0;

                        foreach (array_keys($exchange['voucher_payments']) as $id) {
                            $vouchers_total += $exchange['voucher_payments'][$id]['value'];
                        }

                        if ($vouchers_total > 0) {
                            ?>
                            <tr><td colspan='2' class='s06 pr5 pt3 right'>PAID BY VOUCHERS</td><td class='s06 right pt3'><?= money($vouchers_total) ?></td></tr>
                            <?
                        }
                    }

                    if (is_array($exchange['card_payments'])) {
                        foreach (array_keys($exchange['card_payments']) as $id) {
                            ?>
                            <tr><td colspan='2' class='s06 pr5 pt3 right'>PAID BY <?= strtoupper($exchange['card_payments'][$id]['name']) ?></td><td class='s06 right pt3'><?= money($exchange['card_payments'][$id]['amount']) ?></td></tr>
                            <?
                        }
                    }

                    if ($exchange['cash_payment'] > 0) {
                        ?>
                        <tr><td colspan='2' class='s06 pr5 pt3 right'>PAID CASH</td><td class='s06 right pt3'><?= money($exchange['cash_payment']) ?></td></tr>
                        <tr><td colspan='2'></td><td class='b1sb pb5'></td></tr>
                        <tr><td colspan='2' class='s06 pr5 pt3 right'>CHANGE</td><td class='s06 right pt3'><?= money($exchange['change']['cash']) ?></td></tr>
                        <?
                    }

                    if ($exchange['change']['voucher'] > 0) {
                        ?>
                        <tr><td colspan='2' class='s06 pr5 pt3 right'>OVER PAYED</td><td class='s06 right pt3'><?= money($exchange['change']['voucher']) ?></td></tr>
                        <?
                    }
                    ?>
                </table>
            </div>
            <div class='s06 mb10'><?= nl2br($_SESSION['preferences']['receipt_footer']) ?></div>
            <img src='barcode.php?barcode=<?= $exchange['receipt_id'] ?>'/>
            <div class='s06'><?= $exchange['receipt_id'] ?></div>
        </div>
        <div class='mt10 mb20 left noprint'><input type='button' value='PRINT' onclick='window.print();'/></div>
    </body>
</html>
