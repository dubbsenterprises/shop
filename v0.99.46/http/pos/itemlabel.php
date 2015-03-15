<?
session_start();
include_once('../../includes/shop.php');
include_once('../../includes/inventory_management_functions.php');
$w = $_SESSION['preferences']['label_width'];
if ($_GET['w'] >= 100) { $w = $_GET['w'];
        $count = isset($_GET['count']) && $_GET['count'] > 0 ? $_GET['count'] : 1;
if ($_GET['view_type'] == "grid"){ $view_type = "grid"      ; $view_type_opp  = "vertical";}
else                             { $view_type = "vertical"  ; $view_type_opp  = "grid";}

}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1"/> 
    <title>PRODUCT LABEL</title>
    <script src="includes/clear-default-text.js" type="text/javascript"></script>
    <script src="includes/pos.js" type="text/javascript"></script>
    <link rel="stylesheet" type="text/css" href="includes/pos.css"/>
    <link rel="stylesheet" type="text/css" href="includes/printformats.css" media="print"/>
  </head>
  <body>
    <div class='mt10 mb20 left noprint'>
        <input type='button' value='PRINT' onclick='window.print();'/>
        <? if ( ( $count > 1 || (isset($_GET['all']) && $_GET['all'] == 1)) ) {?>
        <input type='button' value='<?=ucfirst($view_type_opp)?>' onclick='label(<?=$_GET['id']?>,<?=$w?>,<?=$count?>,1,"<?=$view_type_opp?>");'/>
        <? } ?>
    </div>
    <?
    $Inventory_DAL  = new INVENTORY_DAL();
    if ( (isset($_GET['all']) && $_GET['all'] == 1) ) {
        $itemsRows   = $Inventory_DAL->deliveries_GetDeliveryDetailsItems($_GET['id']);

            $first = 1 ;
            $rowCount=1;
        foreach($itemsRows as $itemsRow) {
            $itemInfo   = $Inventory_DAL->labels_GetLabelData($itemsRow->id);
            $count = $itemsRow->quantity;
            $cur = 0;
                while ($cur++ < $count) {?>
                    <? if ($view_type == "grid") {?>
                    <!--  FOr Grid View -->
                    <? if ($first == 1){ $first = 0; ?><table border="0"><?}?>
                    <? if ($rowCount == 1) { ?><tr><td ><? } else { ?><td><? }?>
                    <?}?>

                        <div class='center bcwhite<?=$cur > 1 ? ' pbb' : ''?>' style='width: <?=$w?>px;'>
                          <div class='bold mb2 mt5'><?=$_SESSION['preferences']['company_name']?></div>
                          <div class='s08 mb3 no-overflow'><?=$itemInfo[0]->style_number?>&nbsp;<?=$itemInfo[0]->barcode?></div>
                          <div class='s07 mb5 no-overflow'><? if ($itemInfo[0]->attribute1 != "") { ?>
                              <?=strtolower($itemInfo[0]->catt1)?>:<?=$itemInfo[0]->attribute1?>&nbsp;&nbsp;
                              <? } ?>
                              price:<?=money($itemInfo[0]->price *(100 - $itemInfo[0]->discount) / 100)?>
                          </div>
                          <div class='m0'><img class='m0' src='barcode.php?barcode=<?=$itemInfo[0]->barcode?>' border=0/></div>
                        </div>
                    <? if ($view_type == "grid") {?>
                    <!--  FOr Grid View -->
                    <? if ($rowCount == 3) { ?></td></tr><tr><td colspan="3"><div class='center h12px'>&nbsp;</div></td></tr><? } else { ?></td><? }?>
                    <? if ($rowCount == 1 || $rowCount == 2) { ?><td width="60">&nbsp;</td><? }?>
                    <? if ($rowCount == 3) { $rowCount=1; } else { $rowCount++; } ?>
                    <?}?>
                <?}
         }
    //FOr Grid View
    if ($view_type == "grid") { ?></table><? }
    
    }



    else {
            $itemInfo   = $Inventory_DAL->labels_GetLabelData($_GET['id']);

            $count = isset($_GET['count']) && $_GET['count'] > 0 ? $_GET['count'] : 1;
            $cur = 0;
            while ($cur++ < $count) {?>
                <div class='center bcwhite<?=$cur > 1 ? ' pbb' : ''?>' style='width: <?=$w?>px;'>
                  <div class='bold mb2 mt5'><?=$_SESSION['preferences']['company_name']?></div>
                  <div class='s06 no-overflow'><?=$itemInfo[0]->style_number?>&nbsp;<?=$itemInfo[0]->barcode?></div>
                  <div class='s07 mb5 no-overflow'><? if ($itemInfo[0]->attribute1 != "") { ?>
                      <?=strtolower($itemInfo[0]->catt1)?>:<?=$itemInfo[0]->attribute1?>&nbsp;&nbsp;
                      <? } ?>
                      price:<?=money($itemInfo[0]->price *(100 - $itemInfo[0]->discount) / 100)?>
                  </div>
                  <div class='m0'><img class='m0' src='barcode.php?barcode=<?=$itemInfo[0]->barcode?>' border=0/></div>
                </div>
            <?}
    }?>
  </body>
</html>

