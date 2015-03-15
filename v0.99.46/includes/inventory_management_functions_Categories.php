<?php
function   Categories() {
$inventory_dal = new INVENTORY_DAL();
?>


<div class="ReportsTopRow main_bc_color2 main_color2_text"><a onclick="mainDiv('Inventory_Categories')" href="javascript: none();">Categories</a></div>
    <div style="max-height: 1000px;" class="f_left wp100 hp94">
        <div class="leftSpace main_bc_color2 main_color2_text">&nbsp;</div>
        <div class="middleSpace wp96">
                <div class="InventoryMgmtHeader">
                <div class="f_left">
                    <img alt="" height="45" src="/common_includes/includes/images/category-icon.gif">
                    <? if ( isset($_SESSION['category']['addNewCategory']) && $_SESSION['category']['addNewCategory'] == 1 ) { ?>Add New Category<? } else { ?>Category Overview<? } ?>
                </div>
                <div class="f_right">
                    <? if ( $_SESSION['settings'][$_SESSION['settings']['login_id']]['level'] >= 2) { ?>
                    <a onclick="Inventory_Categories_AddNewCategory()" href="javascript: none();">
                        <img alt="Add New Category" height="45" src="/common_includes/includes/images/add_icon.png" style="border-style: none">
                    </a>
                    <? } ?>
                </div>
            </div>
            <? if ( isset($_SESSION['category']['addNewCategory']) && $_SESSION['category']['addNewCategory'] == 1 ) { ?>
                <div class="InventoryMgmtBodyDataContainer">
                <div id="InventoryMgmtBodyCenter" class="InventoryMgmtBodyCenter" >
                <?=AddNewCategoryStanza($inventory_dal);?>
                </div>
            </div>
            <? } else { ?>
                <div class="profileBodyDataContainer wp95">
                        <div class="profileBodyCenter wp95" id="InventoryMgmtBodyCenter">
                            <?categoriesStanza();?>
                        </div>
                </div>
            <? } ?>
        </div>
        <div class="rightSpace main_bc_color2 main_color2_text">&nbsp;</div>
    </div>
<div class="ReportsBottomRow main_bc_color2 main_color2_text">&nbsp;</div>
<?
if(isset($_SESSION['category']['addNewCategory'])){ unset($_SESSION['category']['addNewCategory']);}
}
    function categoriesStanza() {
    ?>
    <div class="wp100 hp07" id="listing_search_paging_top">
        <? showPaging(); ?>
    </div>
        <? categoriesHeader(); ?>
        <div class="wp100 hp85 scrolling" id="report_data">
        <? categoriesAllCategories(); ?>
        </div>
    <div class="wp100 hp07" id="listing_search_paging_bottom">
        <? showPaging(); ?>
    </div>
    <?
    }
      function categoriesHeader() {
    ?>
        <div class="f_left wp100 s07 HEADER main_bc_color1 main_color1_text">
            <div class="report_header_cell_wp05">ID</div>
            <div class="report_header_cell_wp15">Category Name</div>
            <div class="report_header_cell_wp20">Attribute 1</div>
            <div class="report_header_cell_wp20">Attribute 2</div>
            <div class="report_header_cell_wp08 ">Active Items</div>
            <div class="report_header_cell_wp15 ">Sub Cat?</div>
            <div class="report_header_cell_wp07 ">Update?</div>
            <div class="report_header_cell_wp07">Delete?</div>
        </div>
    <?
    }
      function categoriesAllCategories() {
    $Categories_DAL = new INVENTORY_DAL();
    $categories = $Categories_DAL->Inventory_GetCategoriesByCompanyId($_SESSION['settings']['company_id'],0);
        $altClass = "bctr1a";

        if (count($categories) > 0) {
            foreach($categories as $category){
            $subCategories  = $Categories_DAL->Inventory_GetSubCategories($category->category_id,$_SESSION['settings']['company_id']);
            $activeItems    = $Categories_DAL->Inventory_GetSubActiveItems_PerCategory($category->category_id,$_SESSION['settings']['company_id']);
            ?>
                <div class="f_left wp100 lh25 s08 <?=$altClass?>">
                    <div class="report_data_cell_wp05 hp100" ><?=$category->category_id?></div>
                    <div class="report_data_cell_wp15 hp100" id="failed_upd_itemcategory_name_<?=$category->category_id?>"><input type="text" id="upd_itemcategory_name_<?=$category->category_id?>" value="<?=$category->name?>" class="w100 text cleardefault"></div>
                    <div class="report_data_cell_wp20 hp100" id="failed_upd_attribute1_<?=$category->category_id?>"><input type="text" id="upd_attribute1_<?=$category->category_id?>" value="<?=$category->attribute1?>" class="w100 text cleardefault"></div>
                    <div class="report_data_cell_wp20 hp100" id="failed_upd_attribute2_<?=$category->category_id?>"><input type="text" id="upd_attribute2_<?=$category->category_id?>" value="<?=$category->attribute2?>" class="w100 text cleardefault"></div>
                    <div class="report_data_cell_wp08 hp100" id="failed_upd_attribute2_<?=$category->category_id?>" ><?=$activeItems[0]->count?></div>
                    <div class="report_data_cell_wp15 hp100">
                        <input onclick="Inventory_Categories_SelectCatID(<?=$category->category_id?>)" type="submit" value="<?=$subCategories[0]->count?> Sub Cats" class="button s08">
                    </div>
                    <div class="report_data_cell_wp07 hp100">
                        <input onclick="Inventory_Categories_UpdCategory(<?=$category->category_id?>)" type="submit" value="Update?" class="button s08">
                    </div>
                    <div class="report_data_cell_wp07 hp100">
                        <input onclick="editCategory_Del(<?=$category->category_id?>)" type="submit" value="Delete?" class="button s08">
                    </div>
                </div>
            <?
            if ($altClass == "bctr1a") { $altClass = "bctr1b"; } else {$altClass = "bctr1a";}
            }
        }
        else { ?>
                <div class="profileRow d_InlineBlock wp100 lh25 <?=$altClass?>">
                    <div class="report_data_cell_100 box1" style="background-color:white;">There aren't any categories added yet<?if (isset($_SESSION['item_management_categories']['category_id'])) { ?> (Sub Cat:<?=$_SESSION['item_management_categories']['category_id']?>)<?}?>.</div>
                </div>
        <? }
    }
    function AddNewCategoryStanza($inventory_dal) {
    ?>
        <div class="d_InlineBlock wp70">
                <? AddNewCategoryHeader(); ?>
                <? AddNewCategoryForm($inventory_dal); ?>
        </div>
    <?
    }
      function AddNewCategoryHeader() {
    ?>
            <div class="d_InlineBlock wp100 bctrt center mt40">
                Add a new Category
            </div>
            <div class="d_InlineBlock wp100 HEADER main_bc_color1 main_color1_text ">
                <div class="report_header_cell_wp05">ID</div>
                <div class="report_header_cell_wp20">Parent Name</div>
                <div class="report_header_cell_wp20">Category Name</div>
                <div class="report_header_cell_wp20">Attribute 1</div>
                <div class="report_header_cell_wp20">Attribute 2</div>
                <div class="report_header_cell_wp10 ">Add New?</div>
            </div>
    <?
    }
      function AddNewCategoryForm($inventory_dal) {
    if(isset($_SESSION['item_management_categories']['category_id']) ) {
          $getCategoryName_byID =  $inventory_dal->getCategoryName_byID($_SESSION['item_management_categories']['category_id']);
    }
          ?>
            <div class="d_InlineBlock wp100">
                <div class="report_data_cell_wp05">&nbsp;</div>
                <div class="report_data_cell_wp20"><? if(isset($_SESSION['item_management_categories']['category_id']) ) { ?> <?=$getCategoryName_byID[0]->category_name?> <? } else {?> &nbsp; <? } ?></div>
                <div class="report_data_cell_wp20" id="failed_new_itemcategory_name"><input type="text" id="new_itemcategory_name"  class="w140 text cleardefault"></div>
                <div class="report_data_cell_wp20" id="failed_new_attribute1"><input type="text" id="new_attribute1"         class="w100 text cleardefault"></div>
                <div class="report_data_cell_wp20" id="failed_new_attribute2"><input type="text" id="new_attribute2"         class="w100 text cleardefault"></div>
                <? if(isset($_SESSION['item_management_categories']['category_id']) ) { ?>
                    <div class="report_data_cell_wp10" onclick="Inventory_Categories_SubmitNewSubCategory(<?=$_SESSION['settings']['company_id']?>,<?=$_SESSION['item_management_categories']['category_id']?>)"><input type="submit" value="ADD" class="button"></div>
                <? } else {?>
                    <div class="report_data_cell_wp10" onclick="Inventory_Categories_SubmitNewCategory(<?=$_SESSION['settings']['company_id']?>)"><input type="submit" value="ADD" class="button"></div>
                <? } ?>
            </div>
    <?
    }
?>