<?php
function ItemManagement_Add_Edit_Item(){
$Edit_or_Add = $_SESSION['edit_item']['Edit_or_Add'];
?>
    <div class="ReportsTopRow main_bc_color2 main_color2_text">
        <? if ($Edit_or_Add == "Add") {?>
            <a href="#" title="Item Management" onclick="mainDiv('ItemManagement'); return false;">Item Management</a>
        <?} elseif ($Edit_or_Add == "Edit"){?>
            <a href="javascript: none();" onclick="mainDiv('item_search')">Items</a>        <?} ?>
        &nbsp;
    </div>
    <div style="max-height: 1000px;" class="f_left wp100 hp94">
        <div class="leftSpace main_bc_color2 main_color2_text">
            &nbsp;
        </div>
        <div class="middleSpace wp96">
            <? if ($Edit_or_Add == "Add") {?>
            <div class="d_InlineBlock hp10 wp100">
                <div class="f_left left wp50 hp100 vtop">
                    <img alt="" height="45" src="/common_includes/includes/images/items_icon.png">
                    <? if ($Edit_or_Add == "Add") {?>
                        Add New Items in this Module
                    <?} elseif ($Edit_or_Add == "Edit"){?>
                        Edit item attributes and view basic information
                    <?} else {?>
                        <?=$Edit_or_Add?>
                    <?} ?>
                </div>
                <div id="Inventory_Items_SubmitEdit_or_Add_result" class="f_right bold wp45 hp100">&nbsp;</div>
            </div>
            <?} ?>
                <div class="d_InlineBlock hp100 wp100" id="InventoryMgmtBodyCenter">
                <?
                    if     ($_SESSION['edit_item']['Edit_or_Add'] == "Add"){
                        ItemManagement_AddItemStanza($_SESSION['edit_item']['Edit_or_Add']);
                    }
                    elseif ($_SESSION['edit_item']['Edit_or_Add'] == "Edit" ) {
                        ItemManagement_EditItemStanza($_SESSION['edit_item']['Edit_or_Add']);
                    }
                    else {
                        echo "nope";
                    }
                ?>
                </div>
         </div>
        <div class="rightSpace main_bc_color2 main_color2_text">
            &nbsp;
        </div>
    </div>
    <div class="ReportsBottomRow main_bc_color2 main_color2_text">
        &nbsp;
    </div>
<?
}
    function top_row_item_images($item_id){
       $image_id_data = array ();
       $inventory_dal = new INVENTORY_DAL();
       $image_id_data = $inventory_dal->ItemManagement_get_default_Item_ImageID($item_id,0);
    ?>
        <div class="wp100 hp15">
            <div class="f_left wp25 hp100 no-overflow" id="makeMeScrollable">&nbsp;
            <? if (isset($image_id_data)) { ?>
            <img alt="" src="showimage.php?id=<?=$image_id_data[0]->image_id?>&image_db_id=<?=$image_id_data[0]->image_db_id?>&w=150&h=80">
            <? } else { ?>
            <img alt="" src="showimage.php?id=0&image_db_id=0&w=150&h=80">
            <? } ?>
            </div>
            <div class="f_left wp50 hp100 s19" id="Inventory_Items_SubmitEdit_or_Add_result">&nbsp;</div>
            <div class="f_right wp25 hp100 "   id="Inventory_Items_PrintLabel">&nbsp;<input type="button" class="button w70" value="LABEL" onclick="label(<?=$item_id?>, 180, 1);"></div>
        </div>
    <?
    } 
    function top_row_item($Edit_or_Add){
        if ($Edit_or_Add == "Edit") {
            $activeTabBackground = "bctrt";
            if (isset($_SESSION['edit_item']['ActiveTab']) && $_SESSION['edit_item']['ActiveTab'] == "editItemImages"){
                $editItemImagesBackground = 'bctrt';
            }
            else { $editItemImagesBackground = ''; }

            if (isset($_SESSION['edit_item']['ActiveTab']) && $_SESSION['edit_item']['ActiveTab'] == "editItemSales"){
                $editItemSalesBackground = 'bctrt';
            }
            else { $editItemSalesBackground = ''; }

            if (isset($_SESSION['edit_item']['ActiveTab']) && $_SESSION['edit_item']['ActiveTab'] == "editItemReturns"){
                $editItemReturnsBackground = 'bctrt';
            }
            else { $editItemReturnsBackground = ''; }

            if (isset($_SESSION['edit_item']['ActiveTab']) && $_SESSION['edit_item']['ActiveTab'] == "editItemDeliveries" ) {
                $editItemDeliveriesBackground = 'bctrt';
            }
            else { $editItemDeliveriesBackground = ''; }

            if (isset($_SESSION['edit_item']['ActiveTab']) && $_SESSION['edit_item']['ActiveTab'] == "editItemAttribute" || !isset($_SESSION['edit_item']['ActiveTab']) ){
                $editItemAttributeBackground = 'bctrt';
            }
            else { $editItemAttributeBackground = ''; }
        ?>
            <div class="wp100 hp05">
            <div onclick="Inventory_Items_Edit_ActiveTab('editItemAttribute');" class="f_left s08 wp20 hp100 <?=$editItemAttributeBackground?>" >Attributes</div>
            <div onclick="Inventory_Items_Edit_ActiveTab('editItemImages');"    class="f_left s08 wp20 hp100 <?=$editItemImagesBackground?>" >Images</div>
            <div onclick="Inventory_Items_Edit_ActiveTab('editItemSales');"     class="f_left s08 wp20 hp100 <?=$editItemSalesBackground?>" >Sales</div>
            <div onclick="Inventory_Items_Edit_ActiveTab('editItemReturns');"   class="f_left s08 wp20 hp100 <?=$editItemReturnsBackground?>" >Returns</div>
            <div onclick="Inventory_Items_Edit_ActiveTab('editItemDeliveries')" class="f_left s08 wp20 hp100 <?=$editItemDeliveriesBackground?>" >Deliveries</div>
        </div>
        <? } elseif ($Edit_or_Add == "Add") {?>
            <div class="d_InlineBlock wp100 hp05 HEADER main_bc_color1 main_color1_text mt5">
                <div class="f_left s13 bold wp50 hp100">Create New Item</div>
                <div class="f_right wp50 hp100" >
                    <div class="f_right red s09 wp100 bold" onclick="Inventory_Items_Edit_or_Add_clearValues('Items_CreateNewItem')">Reset "Add Item" Values ?</div>
                    &nbsp;
                </div>
            </div>
        <?
        }
    }
        function ItemManagement_AddItemStanza ($Edit_or_Add){
    top_row_item($_SESSION['edit_item']['Edit_or_Add']);
    ?>
        <div class="d_InlineBlock h350px box5">
            <?add_edit_item_left_side($_SESSION['edit_item']['Edit_or_Add'])?>
            <div class="f_left wp02">&nbsp</div>
            <?add_edit_item_right_side($_SESSION['edit_item']['Edit_or_Add'])?>
        </div>
    <?
    }

        function ItemManagement_EditItemStanza(){
    top_row_item_images($_SESSION['edit_item']['item_id']);
    top_row_item($_SESSION['edit_item']['Edit_or_Add']);

        if     (isset($_SESSION['edit_item']['ActiveTab']) && $_SESSION['edit_item']['ActiveTab'] == "editItemImages"){
            ItemManagement_EditItemStanza_ItemImages($_SESSION['edit_item']['Edit_or_Add']);
        }
        elseif (isset($_SESSION['edit_item']['ActiveTab']) && $_SESSION['edit_item']['ActiveTab'] == "editItemSales"){
            ItemManagement_EditStanza_Sales('Item');
        }
        elseif (isset($_SESSION['edit_item']['ActiveTab']) && $_SESSION['edit_item']['ActiveTab'] == "editItemReturns"){
            
        }
        elseif (isset($_SESSION['edit_item']['ActiveTab']) && $_SESSION['edit_item']['ActiveTab'] == "editItemDeliveries" ) {
            ItemManagement_EditStanza_Deliveries('Item');
        }
        elseif (isset($_SESSION['edit_item']['ActiveTab']) && $_SESSION['edit_item']['ActiveTab'] == "editItemAttribute" || !isset($_SESSION['edit_item']['ActiveTab']) ){
            ItemManagement_EditItemStanza_ItemAttributes($_SESSION['edit_item']['Edit_or_Add']);
        }
    }
            function ItemManagement_EditItemStanza_ItemAttributes($Edit_or_Add){
            ?>
            <div class="wp100 hp80">
                <div class="d_InlineBlock hp05 wp100">
                    <div class="f_left  hp100 wp75 bctrt left textIndent15 s1">
                        Item Attributes
                    </div>
                        <? if ($Edit_or_Add == 'Add') {?>
                            <div class="f_right hp100 red wp25 bctrt center" onclick="Inventory_Items_Edit_or_Add_clearValues('Items_CreateNewItem')">Reset "Add Item" Values ?</div>
                        <? } elseif ($Edit_or_Add == 'Edit') { ?>
                            <div class="f_right hp100 wp25 bctrt" >&nbsp;</div>
                        <? } ?>
                </div>
                <div class="d_InlineBlock hp95 wp100 ">
                        <?add_edit_item_left_side($_SESSION['edit_item']['Edit_or_Add'],$_SESSION['edit_item']['item_id'])?>
                        <div class="f_left wp02">&nbsp</div>
                        <?add_edit_item_right_side($_SESSION['edit_item']['Edit_or_Add'],$_SESSION['edit_item']['item_id'])?>
                </div>
            </div>
    <?
    }
            function ItemManagement_EditItemStanza_ItemImages(){
            ?>
                <div class="d_InlineBlock wp100 hp80">
                <?
                    $general_dal = new GENERAL_DAL();
                    upload_file_stanza('item',$general_dal,$_SESSION['edit_item']['item_id']);
                ?>
                </div>
            <?
        }
    function add_edit_item_left_side ($Edit_or_Add,$item_id=0){
        $inventory_dal = new INVENTORY_DAL();
        # if the item ID is not Zero "0" then we can get data for the item,  then its an Edit also.
        if ($item_id) { $itemInfo = $inventory_dal->Inventory_ItemInfoByItemID($item_id);
        } else {        $itemInfo = 'null'; }
        ?>
        <div class="f_left bclightgrey wp49 hp100">
            <?
            if ( $_SESSION['settings'][$_SESSION['settings']['login_id']]['level'] >= 2 ) {$css = 'wp90 text'; } else { $css = 'wp90 text d_None'; }
            Edit_or_Add_item_attribute_choice($inventory_dal,'Items_CreateNewItem',         'dropdown','wp85','wp90 text',             $Edit_or_Add,'category_id',      'Item Category',           '',                  $itemInfo,'keep',   'Inventory_Items_Edit_or_Add_CategoryChange',1);
            Edit_or_Add_item_attribute_choice($inventory_dal,'Items_CreateNewItem',         'text',    'wp85','wp90 text',             $Edit_or_Add,'name',             'Item Name',               '',                  $itemInfo,'keep',   'no_onchange',                               1);
            Edit_or_Add_item_attribute_choice($inventory_dal,'Items_CreateNewItem',         'text',    'wp25',$css,                    $Edit_or_Add,'buy_price',        'Buy Price',               '',                  $itemInfo,'keep',   'no_onchange',                               1);
            Edit_or_Add_item_attribute_choice($inventory_dal,'Items_CreateNewItem',         'text',    'wp25','wp90 text',             $Edit_or_Add,'price',            'Sell Price',              '',                  $itemInfo,'keep',   'no_onchange',                               1);
            if ($Edit_or_Add == 'Edit'){   
            Edit_or_Add_item_attribute_choice($inventory_dal,'Items_CreateNewItem',         'text',    'wp25','wp90 text',             $Edit_or_Add,'quantity',         'Quantity',                '',                  $itemInfo,'keep',   'no_onchange',                               0);
            }
            Edit_or_Add_item_attribute_choice($inventory_dal,'Items_CreateNewItem',         'text',    'wp50','wp90 text',             $Edit_or_Add,'barcode',          'Barcode',                 'Auto Generated',    $itemInfo,'nokeep', 'no_onchange',                               0);
            Edit_or_Add_item_attribute_choice($inventory_dal,'Items_CreateNewItem',         'text',    'wp35','wp90 text',             $Edit_or_Add,'number',           'Style Number',            '',                  $itemInfo,'keep',   'no_onchange',                               1);
            Edit_or_Add_item_attribute_choice($inventory_dal,'Items_CreateNewItem',         'textarea','wp85','wp90 h100px text',      $Edit_or_Add,'style',            'Item Description',        '',                  $itemInfo,'keep',   'no_onchange',                               1);
            ?>
        </div>
            <?
            }
    function add_edit_item_right_side($Edit_or_Add,$item_id=0){
        $inventory_dal = new INVENTORY_DAL();
        # if the item ID is not Zero "0" then we can get data for the item,  then its an Edit also.
        if ($item_id) { $itemInfo = $inventory_dal->Inventory_ItemInfoByItemID($item_id);
        } else {        $itemInfo = 'null'; }
        ?>
        <div class="f_left bclightgrey wp49 hp100">
            <?
            Edit_or_Add_item_attribute_choice($inventory_dal,'Items_CreateNewItem',         'dropdown','wp85','wp90 text',             $Edit_or_Add,'supplier_id',     'Supplier',                 '',                  $itemInfo,'keep',   'no_onchange',                              1);
            Edit_or_Add_item_attribute_choice($inventory_dal,'Items_CreateNewItem',         'dropdown','wp85','wp90 text',             $Edit_or_Add,'brand_id',        'Brand',                    '',                  $itemInfo,'keep',   'no_onchange',                              1);
            Edit_or_Add_item_attribute_choice($inventory_dal,'Items_CreateNewItem',         'dropdown','wp85','wp90 text',             $Edit_or_Add,'department_id',   'Department',               '',                  $itemInfo,'keep',   'no_onchange',                              1);
            Edit_or_Add_item_attribute_choice($inventory_dal,'Items_CreateNewItem',         'dropdown','wp85','wp90 text',             $Edit_or_Add,'tax_group_id',    'Tax Group',                '',                  $itemInfo,'keep',   'no_onchange',                              1);
            Edit_or_Add_item_attribute_choice($inventory_dal,'Items_CreateNewItem',         'text',    'wp85','wp90 text',             $Edit_or_Add,'attribute1',      'Attribute1',               '',                  $itemInfo,'keep',   'no_onchange',                              1);
            Edit_or_Add_item_attribute_choice($inventory_dal,'Items_CreateNewItem',         'text',    'wp85','wp90 text',             $Edit_or_Add,'attribute2',      'Attribute2',               '',                  $itemInfo,'keep',   'no_onchange',                              1);
            Edit_or_Add_item_attribute_choice($inventory_dal,'Items_CreateNewItem',         'text',    'wp20','wp90 text',             $Edit_or_Add,'discount',        'discount',                 '',                  $itemInfo,'keep',   'no_onchange',                              1);
            Edit_or_Add_item_attribute_choice($inventory_dal,'Items_CreateNewItem',         'text',    'wp20','wp90 text',             $Edit_or_Add,'location',        'location',                 '',                  $itemInfo,'keep',   'no_onchange',                              1);
            Edit_or_Add_item_attribute_choice($inventory_dal,'Items_CreateNewItem',         'text',    'wp10','wp90 text',             $Edit_or_Add,'reorder_limit1',  'Reorder Min 1',            '',                  $itemInfo,'keep',   'no_onchange',                              1);
            Edit_or_Add_item_attribute_choice($inventory_dal,'Items_CreateNewItem',         'text',    'wp10','wp90 text',             $Edit_or_Add,'reorder_limit2',  'Reorder Min 2',            '',                  $itemInfo,'keep',   'no_onchange',                              1);
            Edit_or_Add_item_attribute_choice($inventory_dal,'Items_CreateNewItem',         'bolean',  'wp40','wp90 text',             $Edit_or_Add,'online_active',   'Online Available?',        '',                  $itemInfo,'keep',   'no_onchange',                              1);
            if($Edit_or_Add == "Edit") {
               Edit_or_Add_item_attribute_choice($inventory_dal,'Items_CreateNewService','bolean',  'wp40','wp90 text',             $Edit_or_Add,'archived',        'Archived',                 '',         $itemInfo,'na',     'no_onchange',1);
                if ($_SESSION['settings'][$_SESSION['settings']['login_id']]['level'] >= 2) {
                        ?><input type="submit" value="Update Item" class="button wp50 mt5" onclick="Inventory_Items_Edit_or_Add(<?=$_SESSION['edit_item']['item_id']?>,'item','edit')"><?
                }
            }  elseif($Edit_or_Add == "Add") { ?>
                <div class="center pt20" onclick="Inventory_Items_Edit_or_Add(0,'item','add')">
                    <input type="submit" value="Create New Item" class="button">
                </div>
            <? } ?>
       </div>
       <?
       }


function ItemManagement_Add_Edit_Service(){
$Edit_or_Add = $_SESSION['edit_service']['Edit_or_Add'];
?>
    <div class="ReportsTopRow main_bc_color2 main_color2_text">
        <? if ($Edit_or_Add == "Add") {?>
            <a href="#" title="Item Management" onclick="mainDiv('ItemManagement'); return false;">Item Management</a>
        <?} elseif ($Edit_or_Add == "Edit"){?>
            <a href="javascript: none();" onclick="mainDiv('item_search')">Items</a>        <?} ?>
        &nbsp;
    </div>
    <div style="max-height: 1000px;" class="f_left wp100 hp94">
        <div class="leftSpace main_bc_color2 main_color2_text">
            &nbsp;
        </div>
        <div class="middleSpace wp96">
            <? if ($Edit_or_Add == "Add") {?>
            <div class="d_InlineBlock hp10 wp100">
                <div class="f_left left wp50 hp100 vtop">
                    <img alt="" height="45" src="/common_includes/includes/images/items_icon.png">
                    <? if ($Edit_or_Add == "Add") {?>
                        Add New Service in this Module
                    <?} elseif ($Edit_or_Add == "Edit"){?>
                        Edit service attributes and view basic information
                    <?}?>
                </div>
                <div id="Inventory_Items_SubmitEdit_or_Add_result" class="f_right bold wp50 hp100">&nbsp;</div>
            </div>
            <? } ?>
            <div class="d_InlineBlock hp100 wp100" id="InventoryMgmtBodyCenter">
                <?
                    if     ($_SESSION['edit_service']['Edit_or_Add'] == "Add"){
                        ItemManagement_AddServiceStanza($_SESSION['edit_service']['Edit_or_Add']);
                    }
                    elseif ($_SESSION['edit_service']['Edit_or_Add'] == "Edit" ) {
                        ItemManagement_EditServiceStanza($_SESSION['edit_service']['Edit_or_Add']);
                    }
                    else {
                        echo "nope";
                    }
                ?>
                </div>
         </div>
        <div class="rightSpace main_bc_color2 main_color2_text">
            &nbsp;
        </div>
    </div>
    <div class="ReportsBottomRow main_bc_color2 main_color2_text">
        &nbsp;
    </div>
<?
}
    function top_row_service_images($item_id){
   $inventory_dal = new INVENTORY_DAL();
   $image_id_data = $inventory_dal->ItemManagement_get_default_Item_ImageID($item_id);
?>
        <div class="d_InlineBlock wp100 hp15">
            <div class="f_left wp25 hp100 no-overflow">
            <? if (isset($image_id_data)) { ?>
                <img alt="" src="showimage.php?id=<?=$image_id_data[0]->image_id?>&image_db_id=<?=$image_id_data[0]->image_db_id?>&w=150&h=80">
            <? } else { ?>
                <img alt="" src="showimage.php?id=0&image_db_id=0&w=150&h=80">
            <? } ?>
            </div>
            <div class="f_left wp50 hp100 s19" id="Inventory_Items_SubmitEdit_or_Add_result">&nbsp;</div>
            <div class="f_right wp25 hp100"    id="Inventory_Items_PrintLabel">&nbsp;<input type="button" class="button w70" value="LABEL" onclick="label(<?=$item_id?>, 180, 1);"></div>
        </div>
<?
}
    function top_row_service($Edit_or_Add){
            if ($Edit_or_Add == "Edit"){
                        $activeTabBackground = "bctrt";
                        if (isset($_SESSION['edit_service']['ActiveTab']) && $_SESSION['edit_service']['ActiveTab'] == "editItemImages"){
                            $editItemImagesBackground = 'bctrt';
                        }
                        else { $editItemImagesBackground = ''; }

                        if (isset($_SESSION['edit_service']['ActiveTab']) && $_SESSION['edit_service']['ActiveTab'] == "editItemSales"){
                            $editItemSalesBackground = 'bctrt';
                        }
                        else { $editItemSalesBackground = ''; }

                        if (isset($_SESSION['edit_service']['ActiveTab']) && $_SESSION['edit_service']['ActiveTab'] == "editItemReturns"){
                            $editItemReturnsBackground = 'bctrt';
                        }
                        else { $editItemReturnsBackground = ''; }

                        if (isset($_SESSION['edit_service']['ActiveTab']) && $_SESSION['edit_service']['ActiveTab'] == "editItemDeliveries" ) {
                            $editItemDeliveriesBackground = 'bctrt';
                        }
                        else { $editItemDeliveriesBackground = ''; }

                        if (isset($_SESSION['edit_service']['ActiveTab']) && $_SESSION['edit_service']['ActiveTab'] == "editItemAttribute" || !isset($_SESSION['edit_service']['ActiveTab']) ){
                            $editItemAttributeBackground = 'bctrt';
                        }
                        else { $editItemAttributeBackground = ''; }?>
                <div class="wp100 hp05">
                    <div onclick="Inventory_Items_Edit_ActiveTab_Service('editItemAttribute');" class="f_left s08 wp20 hp100 <?=$editItemAttributeBackground?>" >Attributes</div>
                    <div onclick="Inventory_Items_Edit_ActiveTab_Service('editItemImages');" class="f_left s08 wp20 hp100 <?=$editItemImagesBackground?>" >Images</div>
                    <div onclick="Inventory_Items_Edit_ActiveTab_Service('editItemSales');" class="f_left s08 wp20 hp100 <?=$editItemSalesBackground?>" >Sales</div>
                    <div onclick="Inventory_Items_Edit_ActiveTab_Service('editItemReturns');" class="f_left s08 wp20 hp100 <?=$editItemReturnsBackground?>" >Returns</div>
                    <div onclick="Inventory_Items_Edit_ActiveTab_Service('editItemDeliveries');" class="f_left s08 wp20 hp100 <?=$editItemDeliveriesBackground?>" >Deliveries</div>
                </div>
                <? } elseif ($Edit_or_Add == "Add") {?>
                <div class="d_InlineBlock wp100 hp05 HEADER main_bc_color1 main_color1_text mt5">
                    <div class="f_left s13 bold wp50 hp100">Create New Service</div>
                    <div class="f_right wp50 hp100" >
                        <div class="f_right red s09 wp100 bold" onclick="Inventory_Items_Edit_or_Add_clearValues('Items_CreateNewService')">Reset "Add Service" Values ?</div>
                        &nbsp;
                    </div>
                </div>
                <? }
}
        function ItemManagement_AddServiceStanza ($Edit_or_Add){
    top_row_service($_SESSION['edit_service']['Edit_or_Add']);
    ?>
        <div class="d_InlineBlock h350px box5">
            <?add_edit_service_left_side($_SESSION['edit_service']['Edit_or_Add'])?>
            <div class="f_left wp02">&nbsp</div>
            <?add_edit_service_right_side($_SESSION['edit_service']['Edit_or_Add'])?>
        </div>
    <?
    }

        function ItemManagement_EditServiceStanza(){
    top_row_service_images($_SESSION['edit_service']['service_id']);
    top_row_service($_SESSION['edit_service']['Edit_or_Add']);

        if ($_SESSION['edit_service']['ActiveTab'] == "editItemImages"){
            ItemManagement_EditServiceStanza_ServiceImages($_SESSION['edit_service']['Edit_or_Add']);
        }
        elseif ($_SESSION['edit_service']['ActiveTab'] == "editItemSales"){
            ItemManagement_EditStanza_Sales('Service');
        }
        elseif ($_SESSION['edit_service']['ActiveTab'] == "editItemReturns"){
        }
        elseif ($_SESSION['edit_service']['ActiveTab'] == "editItemDeliveries" ) {
        }
        elseif ($_SESSION['edit_service']['ActiveTab'] == "editItemAttribute" || !isset($_SESSION['edit_service']['ActiveTab']) ){
            ItemManagement_EditServiceStanza_ServiceAttributes($_SESSION['edit_service']['Edit_or_Add']);
        }
    }
            function ItemManagement_EditServiceStanza_ServiceAttributes($Edit_or_Add){
            ?>
            <div class="f_left wp100 hp80">
                <div class="f_left hp05 wp100">
                    <div class="f_left hp100 wp75 bctrt left textIndent15 s1">
                        Service Attributes
                    </div>
                    <div class="f_right hp100 wp25 bctrt center" >
                        <? if ($Edit_or_Add == 'Add') {?>
                            <div class="f_right hp100 red wp25 bctrt center" onclick="Inventory_Items_Edit_or_Add_clearValues('Items_CreateNewService')">Reset "Add Service" Values ?</div>
                        <? } elseif ($Edit_or_Add == 'Edit') { ?>
                            <div class="f_right hp100 wp25 bctrt" >&nbsp;</div>
                        <? } ?>
                    </div>
                </div>
                <div class="d_InlineBlock hp95 wp100">
                            <? add_edit_service_left_side($_SESSION['edit_service']['Edit_or_Add'],$_SESSION['edit_service']['service_id'])?>
                            <div class="f_left wp02">&nbsp</div>
                            <?add_edit_service_right_side($_SESSION['edit_service']['Edit_or_Add'],$_SESSION['edit_service']['service_id'])?>
                </div>
            </div>
    <?
    }
            function ItemManagement_EditServiceStanza_ServiceImages(){
            ?>
                <div class="d_InlineBlock wp100 hp80">
                <?
                    $general_dal = new GENERAL_DAL();
                    upload_file_stanza('item',$general_dal,$_SESSION['edit_service']['service_id']);
                ?>
                </div>
            <?
        }
    function add_edit_service_left_side ($Edit_or_Add,$item_id=0){
        $inventory_dal = new INVENTORY_DAL();
        # if the item ID is not Zero "0" then we can get data for the item,  then its an Edit also.
        if ($item_id) { $itemInfo = $inventory_dal->Inventory_ItemInfoByItemID($item_id);
        } else {        $itemInfo = 'null'; }
        ?>
        <div class="f_left bclightgrey wp49 hp100">
            <?
            if ( $_SESSION['settings'][$_SESSION['settings']['login_id']]['level'] >= 2 ) {$css = 'wp90 text'; } else { $css = 'wp90 text d_None'; }
            Edit_or_Add_item_attribute_choice($inventory_dal,'Items_CreateNewService',      'dropdown','wp85','wp90 text',              $Edit_or_Add,'category_id',     'Service Category',         '',                 $itemInfo,'keep',   'Inventory_Items_Edit_or_Add_CategoryChange',1);
            Edit_or_Add_item_attribute_choice($inventory_dal,'Items_CreateNewService',      'text',    'wp85','wp90 text',              $Edit_or_Add,'name',            'Service Name',             '',                 $itemInfo,'nokeep', 'no_onchange',                               1);
            Edit_or_Add_item_attribute_choice($inventory_dal,'Items_CreateNewService',      'text',    'wp25',       $css,              $Edit_or_Add,'buy_price',       'Cost of Service',          '',                 $itemInfo,'keep',   'no_onchange',                               1);
            Edit_or_Add_item_attribute_choice($inventory_dal,'Items_CreateNewService',      'text',    'wp25','wp90 text',              $Edit_or_Add,'price',           'Sell Price',               '',                 $itemInfo,'keep',   'no_onchange',                               1);
            Edit_or_Add_item_attribute_choice($inventory_dal,'Items_CreateNewService',      'text',    'wp15','wp90 text',              $Edit_or_Add,'est_time_mins',   'Service Mins',             '',                 $itemInfo,'keep',   'no_onchange',                               1);
            Edit_or_Add_item_attribute_choice($inventory_dal,'Items_CreateNewService',      'text',    'wp50','wp90 text',              $Edit_or_Add,'barcode',         'Barcode',                  'Auto-Generated',   $itemInfo,'nokeep', 'no_onchange',                               0);
            Edit_or_Add_item_attribute_choice($inventory_dal,'Items_CreateNewService',      'textarea','wp85','wp90 h100px text ',      $Edit_or_Add,'style',           'Service Description',      '',                 $itemInfo,'keep',   'no_onchange',                               1);
            ?>
        </div>
        <?
        }
    function add_edit_service_right_side($Edit_or_Add,$item_id=0){
        $inventory_dal = new INVENTORY_DAL();
        # if the item ID is not Zero "0" then we can get data for the item,  then its an Edit also.
        if ($item_id) { $itemInfo = $inventory_dal->Inventory_ItemInfoByItemID($item_id);
        } else {        $itemInfo = 'null'; }
        ?>
       <div class="f_left bclightgrey wp49 hp100">
            <?
            Edit_or_Add_item_attribute_choice($inventory_dal,'Items_CreateNewService',      'dropdown','wp85','wp90 text',              $Edit_or_Add,'department_id', 'Department',                 '',                 $itemInfo,'keep',   'no_onchange',                              1);
            Edit_or_Add_item_attribute_choice($inventory_dal,'Items_CreateNewService',      'dropdown','wp85','wp90 text',              $Edit_or_Add,'tax_group_id',  'Tax Group',                  '',                 $itemInfo,'keep',   'no_onchange',                              1);
            Edit_or_Add_item_attribute_choice($inventory_dal,'Items_CreateNewService',      'text',    'wp85','wp90 text',              $Edit_or_Add,'attribute1',    'Attribute1',                 '',                 $itemInfo,'keep',   'no_onchange',                              1);
            Edit_or_Add_item_attribute_choice($inventory_dal,'Items_CreateNewService',      'text',    'wp85','wp90 text',              $Edit_or_Add,'attribute2',    'Attribute2',                 '',                 $itemInfo,'keep',   'no_onchange',                              1);
            Edit_or_Add_item_attribute_choice($inventory_dal,'Items_CreateNewService',      'text',    'wp20','wp90 text',              $Edit_or_Add,'discount',      'discount',                   '',                 $itemInfo,'keep',   'no_onchange',                              1);
            Edit_or_Add_item_attribute_choice($inventory_dal,'Items_CreateNewService',      'text',    'wp20','wp90 text',              $Edit_or_Add,'location',      'location',                   '',                 $itemInfo,'keep',   'no_onchange',                              1);
            Edit_or_Add_item_attribute_choice($inventory_dal,'Items_CreateNewService',      'bolean',  'wp40','wp90 text',              $Edit_or_Add,'online_active', 'Online Available?',          '',                 $itemInfo,'keep',   'no_onchange',                              1);

            if($Edit_or_Add == "Edit") {
                Edit_or_Add_item_attribute_choice($inventory_dal,'Items_CreateNewService',  'bolean',  'wp40','wp90 text',              $Edit_or_Add,'archived',      'Archived',                   '',         $itemInfo,'keep',     'no_onchange',1);
                if ($_SESSION['settings'][$_SESSION['settings']['login_id']]['level'] >= 2) {
                        ?><input type="submit" value="Update Service" class="button wp50 mt5" onclick="Inventory_Items_Edit_or_Add(<?=$_SESSION['edit_service']['service_id']?>,'service','edit')"><?
                }
            }  elseif($Edit_or_Add == "Add") { ?>
                <div class="center pt20" onclick="Inventory_Items_Edit_or_Add(0,'service','add')">
                    <input type="submit" value="Create New Service" class="button">
                </div>
            <? } ?> 
       </div>
       <?
       }

function Edit_or_Add_item_attribute_choice($inventory_dal,$serviceORitem_session,$type,$dataInputWidth,$textarea_dimensions,$Edit_or_Add,$attribute,$attribute_display_name,$placeholder='null',$itemInfo='null',$keep_or_not='keep',$javaScriptOnchange='no_change',$AllowEdit=1) {
    # $serviceORitem_session = Items_CreateNewItem or Items_CreateNewService
    if ($itemInfo != 'null') {
        $value = $itemInfo[0]->$attribute ;
    } else {
        if ( isset($_SESSION[$serviceORitem_session]['keep_'.$attribute]) && $_SESSION[$serviceORitem_session]['keep_'.$attribute] == 1 ) { $value= $_SESSION[$serviceORitem_session][$attribute]; }
        else { $value = ''; }
    }?>
        <div class="d_InlineBlock wp100 h20px ">
          <div class="f_left s07 right bold wp25 hp100 no-overflow" id ="Inventory_Items_<?=$attribute?>"><?=ucwords($attribute_display_name);?>:</div>
          <div class="f_left s08 left  bold wp50 no-overflow">
            <div class="f_left left pl10 <?=$dataInputWidth?>" id="new_item_<?=$attribute?>_div">
                <?
                    if ($type == 'dropdown') {
                        $javaScriptOnchangeValue = '';
                        if ($javaScriptOnchange != "no_onchange") { $javaScriptOnchangeValue = "onchange=\"$javaScriptOnchange()\""; }
                        ?>
                        <select class="<?=$textarea_dimensions?>" id="dynamic_pannel_<?=$attribute?>" name="<?=$attribute?>" <?=$javaScriptOnchangeValue?>>
                            <?
                            switch($attribute){
                               case 'category_id':  $results = $inventory_dal->ItemManagement_GetCategories($_SESSION['settings']['company_id']);  
                                   $item_management_quick_link = "<a href='javascript: none();' onclick=mainDiv(\"Inventory_Categories\")>+</a>"; 
                                   break;
                               case 'supplier_id':  $results = $inventory_dal->ItemManagement_GetSuppliers($_SESSION['settings']['company_id']);
                                   $item_management_quick_link = "<a onclick=\"document.getElementById('post_values').value = 'item_management=1|show_type=suppliers'; document.page_form.submit(); return false;\">+</a>";
                                   break;
                               case 'brand_id':     $results = $inventory_dal->ItemManagement_GetBrands($_SESSION['settings']['company_id']);
                                   $item_management_quick_link = "<a onclick=\"document.getElementById('post_values').value = 'item_management=1|show_type=brands'; document.page_form.submit(); return false;\">+</a>";
                                   break;
                               case 'department_id':$results = $inventory_dal->ItemManagement_GetDepartments($_SESSION['settings']['company_id']);
                                   $item_management_quick_link = "<a onclick=\"document.getElementById('post_values').value = 'item_management=1|show_type=departments'; document.page_form.submit(); return false;\">+</a>";
                                   break;
                               case 'tax_group_id': $results = $inventory_dal->ItemManagement_GetTaxGroups($_SESSION['settings']['company_id']);
                                   $item_management_quick_link = "<a onclick=\"document.getElementById('post_values').value = 'item_management=1|show_type=taxgroups'; document.page_form.submit(); return false;\">+</a>";
                                   break;
                            }
                            ####  Show 1st row as -Select- or not!!
                            $checkAttributes = array ("tax_group_id");
                            if(!in_array($attribute, $checkAttributes) || $Edit_or_Add == 'Edit'){?>
                               <option value="-1">-Select-</option>
                            <?}
                            foreach($results as $result) {
                                if ($Edit_or_Add == "Add") {
                                    if ( isset($_SESSION[$serviceORitem_session]['keep_'.$attribute]) &&
                                               $_SESSION[$serviceORitem_session]['keep_'.$attribute] == 1 &&
                                               $result->id == $_SESSION[$serviceORitem_session][$attribute]
                                        ) {$selected = "selected" ;  }
                                    else {$selected = "" ;}
                                }
                                elseif ($Edit_or_Add == "Edit"){
                                    if ( $itemInfo[0]->$attribute == $result->id ) {$selected = "selected" ;  } else {$selected = "" ;}
                                }?>
                            <option value="<?=$result->id?>" <?=$selected?> ><?=$result->name?></option>
                            <? } ?>
                        </select>
                        <?= $item_management_quick_link ?>
                <? } elseif ($type == 'text') {
                    ?>
                        <? if ($AllowEdit || $Edit_or_Add == "Add") { ?>
                        <input class="<?=$textarea_dimensions?>" type="text" value="<?=$value?>" id="dynamic_pannel_<?=$attribute?>"
                            <?if ($placeholder != 'null') {?>
                               placeholder="<?=$placeholder?>"
                            <?}?>
                            <input type="hidden" id="dynamic_pannel_css_<?=$attribute?>" value="<?=$textarea_dimensions?>">
                            <input type="hidden" value="<?=$value?>" id="dynamic_pannel_<?=$attribute?>" >
                        <? } else { ?>
                            <input type="hidden" id="dynamic_pannel_css_<?=$attribute?>" value="<?=$textarea_dimensions?>">
                            <input type="hidden" value="<?=$value?>" id="dynamic_pannel_<?=$attribute?>" >
                            <?=$value?>
                        <? } ?>
                <? } elseif ($type == 'textarea') {
                    list($rows,$columns) = split('x', $textarea_dimensions)
                    ?>
                        <textarea class="<?=$textarea_dimensions?>" id="dynamic_pannel_<?=$attribute?>" ><?=$value?></textarea>
                        <input type="hidden" id="dynamic_pannel_css_<?=$attribute?>" value="<?=$textarea_dimensions?>">
                <? } elseif ($type == 'bolean') {
                                $not_active_selected = $active_selected = "" ;
                                if ($Edit_or_Add     == "Add") {
                                }
                                elseif ($Edit_or_Add == "Edit"){
                                    if ( $value      == 1 ) { $active_selected = "selected" ;  } else { $not_active_selected = "selected" ; }
                                }?>
                    <select class="<?=$textarea_dimensions?>" id="dynamic_pannel_<?=$attribute?>" name="<?=$attribute?>">
                        <option <?=$not_active_selected?>  value="0">No </option>
                        <option <?=$active_selected ?>     value="1">Yes</option>
                    </select>
                <? } else{?>
                          Edit_or_Add_item_attribute_choice(); <?=$attribute?> is not configured yet.
                <? } ?>
                        <input type="hidden" id="dynamic_pannel_css_<?=$attribute?>" value="<?=$textarea_dimensions?>">
                        <input type="hidden" id="dynamic_pannel_keep_<?=$keep_or_not?>" value="<?=$textarea_dimensions?>">
            </div>
            <? if ($Edit_or_Add == "Add" && $keep_or_not == 'keep') { ?>
            <div class="f_right wp10">
                    <? if (isset($_SESSION[$serviceORitem_session]['keep_'.$attribute]) && ($_SESSION[$serviceORitem_session]['keep_'.$attribute] == 1 ) ) { $checked = "checked" ; } else {$checked = "" ;} ?>
                    <input type="checkbox" value="1" id="dynamic_pannel_<?=$attribute?>_keep" class="ml5" <?=$checked?> >
            </div>
            <?} else {?>
            <div class="f_right wp10">
                    <input type="hidden" value="0" id="dynamic_pannel_<?=$attribute?>_keep" class="ml5" >
            </div>
            <? } ?>
          </div>
          <div class="f_left s06 center red wp25 hp100 no-overflow" id="dynamic_pannel_<?=$attribute?>_error">&nbsp;</div>
        </div>
    <?}
function ItemManagement_EditStanza_Sales($type){
            $inventory_dal = new INVENTORY_DAL();
            if ($type == 'Service'){
                $sales = $inventory_dal->ItemManagement_ItemSaleHistory($_SESSION['edit_service']['service_id']);
            }
            elseif ($type == 'Item'){
                $sales = $inventory_dal->ItemManagement_ItemSaleHistory($_SESSION['edit_item']['item_id']);
            }
            $altClass = "bctr1a";
            $count = 1;
            ?>
            <div class="d_InlineBlock wp100 hp80">
                <div class="d_InlineBlock wp100 hp100" >
                    <div class="f_left wp15 hp100">
                        <div class="d_InlineBlock wp100 hp100" >
                            Search
                        </div>
                    </div>
                    <div class="f_right d_InlineBlock wp85 hp100">
                        <div class="bctrt b1sb bold s1 wp100">SALES INCLUDING THIS <?=$type?></div>
                        <div class="profileRow wp99 HEADER main_bc_color1 main_color1_text center">
                            <div class="report_header_cell_wp05 ">#</div>
                            <div class="report_header_cell_wp30 ">SALE DATE AND TIME</div>
                            <div class="report_header_cell_wp20 ">SALE TOTAL</div>
                            <div class="report_header_cell_wp08 ">ITEMS</div>
                            <div class="report_header_cell_wp35">SHOW SALE DETAILs?</div>
                        </div>
                        <div class="scrolling wp99 h300px">
                            <?
                            if (count($sales) >0 ) {
                                foreach ($sales as $sale_data) { ?>
                                <div class="profileRow <?=$altClass?> wp100 h20px center">
                                    <div class="report_data_cell_wp05 hp100"><?=$count?></div>
                                    <div class="report_data_cell_wp30 hp100"><?=$sale_data->added?></div>
                                    <div class="report_data_cell_wp20 hp100">$&nbsp;<?=$sale_data->price?></div>
                                    <div class="report_data_cell_wp08 hp100"><?=$sale_data->quantity?></div>
                                    <div class="report_data_cell_wp35 hp100">
                                        <input type="button" onclick="document.getElementById('showsaledetails_rid').value = <?=$sale_data->id?> document.showsaledetailsform.submit();" value="SHOW SALE DETAILS" class="button S07">
                                    </div>
                                </div>
                                <?
                                $count++;
                                if ($altClass == "bctr1a") { $altClass = "bctr1b"; } else {$altClass = "bctr1a";}
                                }
                        } else { ?>
                        <div class="center h20 d_InlineBlock s07 wp99">
                            <div class="d_InlineBlock hp100 report_data_cell_wp100">Your report returned zero sales results.</div>
                        </div>
                        <? }?>
                        </div>
                   </div>
                </div>
             </div>
            <?
        }
function ItemManagement_EditStanza_Deliveries($type){
        $inventory_dal = new INVENTORY_DAL();
        if ($type == 'Service'){
            unset($_SESSION['search_data']['paging_page']);
            $deliveries = $inventory_dal->Deliveries_DeliveryList_Per_Item($_SESSION['settings']['company_id'],$_SESSION['edit_service']['service_id']);
        }
        elseif ($type == 'Item'){
            unset($_SESSION['search_data']['paging_page']);
            $deliveries = $inventory_dal->Deliveries_DeliveryList_Per_Item($_SESSION['settings']['company_id'],$_SESSION['edit_item']['item_id']);
        }
        $altClass = "bctr1a";
        $rownum = 1;
        ?>
        <div class="d_InlineBlock wp100 hp80">
            <div class="d_InlineBlock wp100 hp100" >
                <div class="f_left wp15 hp100">
                    <div class="d_InlineBlock wp100 hp100" >
                        Search
                    </div>
                </div>
                <div class="f_right d_InlineBlock wp85 hp100">
                    <div class="bctrt b1sb bold s1 wp99">Deliveries INCLUDING THIS <?=$type?></div>
                    <div class="d_InlineBlock HEADER main_bc_color1 main_color1_text wp100 s07">
                        <div class="f_left report_header_cell_wp03">#</div>
                        <div class="f_left report_header_cell_wp15">ADDED</div>
                        <div class="f_left report_header_cell_wp08">RECEIVED</div>
                        <div class="f_left report_header_cell_wp15">SUPPLIER</div>
                        <div class="f_left report_header_cell_wp05">ITEMS</div>
                        <div class="f_left report_header_cell_wp07">SHIPPING</div>
                        <div class="f_left report_header_cell_wp07">BUY PRICE</div>
                        <div class="f_left report_header_cell_wp07">SELL REVENUE</div>
                        <div class="f_left report_header_cell_wp07">PROFIT</div>
                        <div class="f_left report_header_cell_wp10">RECEIVER</div>
                        <div class="f_left report_header_cell_wp12">DETAILS?</div>
                    </div>
                    <div class="scrolling wp99 h300px">
                        <?
                        if (count($deliveries) >0 ) {
                            foreach ($deliveries as $delivery_data) {
                                $Delivery_data = $inventory_dal->deliveries_GetDeliveryInfo($delivery_data->id);
                                ?>
                                <div class="profileRow wp100 lh20 <?=$altClass?>">
                                    <div class="report_data_cell_wp03">&nbsp;<?=$rownum++?></div>
                                    <div class="report_data_cell_wp15">&nbsp;<?=$delivery_data->added?></div>
                                    <div class="report_data_cell_wp08">&nbsp;<?=$delivery_data->received?></div>
                                    <div class="report_data_cell_wp15">&nbsp;<?=$delivery_data->name?></div>
                                    <div class="report_data_cell_wp05">&nbsp;<?=$Delivery_data[0]->quantity?></div>
                                    <div class="report_data_cell_wp07">&nbsp;<?=money2($delivery_data->shipping_costs)?></div>
                                    <div class="report_data_cell_wp07">&nbsp;<?=money2($Delivery_data[0]->total_buy_price + $delivery_data->shipping_costs)?></div>
                                    <div class="report_data_cell_wp07">&nbsp;<?=money2($Delivery_data[0]->total_sell_price )?></div>
                                    <div class="report_data_cell_wp07">&nbsp;<?=money2($Delivery_data[0]->total_sell_price - $Delivery_data[0]->total_buy_price)?></div>
                                    <div class="report_data_cell_wp10">&nbsp;<?=$delivery_data->receiver?></div>
                                    <div class="report_data_cell_wp12">&nbsp;<input type='button' class='button' value='DETAILS' onclick=Deliveries_deliveryDetails(<?=$delivery_data->id?>)></div>
                                </div>
                                <? if ($altClass == "bctr1a") { $altClass = "bctr1b"; } else {$altClass = "bctr1a";} ?>
                            <?}
                        } else { ?>
                    <div class="center h20 d_InlineBlock s07 wp99">
                        <div class="d_InlineBlock hp100 report_data_cell_wp100">Your report returned zero delivery results.</div>
                    </div>
                    <? }?>
                    </div>
               </div>
            </div>
         </div>
        <?
}
    ?>