<?php
function email_TemplateTopRow   ($company_id,$customer_id,$general_dal,$IMAGE_DAL,$Companies_dal,$Customer_dal,$BGcolor){
list($host,$domain)                     = setup_path_mailer();
$SERVER_ADDRESS                         = $Companies_dal->get_TemplateTabData_by_Name('SERVER_ADDRESS',$company_id);
$COMPANY_URL                            = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_URL',$company_id);
$COMPANY_NAME                           = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_NAME',$company_id);
$COMPANY_Facebook_Link                  = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_Facebook_Link',$company_id);
$email_Template_Link_1                  = $Companies_dal->get_TemplateTabData_by_Name('email_Template_Link_1',$company_id);

$PHYSICAL_ADDRESS                       = $Companies_dal->get_TemplateTabData_by_Name('PHYSICAL_ADDRESS',$_SESSION['settings']['company_id']);
$Phone_Number_Main                      = $Companies_dal->get_TemplateTabData_by_Name('Phone_Number_Main',$_SESSION['settings']['company_id']);
$Phone_Number_2                         = $Companies_dal->get_TemplateTabData_by_Name('Phone_Number_2',$_SESSION['settings']['company_id']);

$email_Template_1_1                     = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID($company_id,'email_Template_1_1');
$email_Template_1_2                     = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID($company_id,'email_Template_1_2');

$customer_info                          = $Customer_dal->get_CustomerDataPerId($customer_id);
$customer_added                         = date("y/m/d",strtotime($customer_info[0]->added));
    ?>
    <div style="width:100%; height:25px; float:left; ">
        <div style=" float:left; border-top:1px solid #OOOOOO; width:100%; height:100%;">&nbsp;</div>
    </div>

    <div class="main_bc_color1" style="width:100%; height:80px; float:left; ">
            <a href="http://<?=$COMPANY_URL[0]->value?>" style="text-decoration:none; cursor:pointer;">
                <div style="float:left; text-align:center; width:20%; height:100%; font-size: 20px; ">
                    <img height=75 width=75 src="http://<?=$host?>.<?=$domain?>/common_includes/includes/images/header-book-online.png">
                </div>
            </a>

            <a href='<?=$email_Template_Link_1[0]->value?>' style="text-decoration:none; cursor:pointer;">
                <div class="main_color1_text" style="float:left; text-align:right; width:15%; height:100%; font-size: 20px; border-bottom-left-radius: 15px; border-left: 2px solid black; ">You<br>Could<br> Win a</div>
                <div class="main_color3_text" style="float:left; text-align:center;width:30%; height:100%; font-size: 60px; border-bottom: 2px solid black;">$100</div>
                <div class="main_color1_text" style="float:left; text-align:left;  width:15%; height:100%; font-size: 30px; border-bottom-right-radius:15px; border-right:2px solid black; ">Gift<br>Card<br></div>
            </a>

            <div style="float:left; text-align:right; width:20%; height:100%; font-size: 20px; ">
                &nbsp;
            </div>
    </div>
<?}
function email_TemplateMailBody ($company_id,$customer_id,$general_dal,$IMAGE_DAL,$Companies_dal,$Customer_dal,$BGcolor){
list($host,$domain)                     = setup_path_mailer();
$email_Template_Link_1                  = $Companies_dal->get_TemplateTabData_by_Name('email_Template_Link_1',$company_id);

$email_Template_1_1                     = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID($company_id,'email_Template_1_1');
$email_Template_1_2                     = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID($company_id,'email_Template_1_2');
?>
    <div style="float:left; position: relative; border-top:1px solid #FFF; width:100%; height:300px;">
        <table width="100%" height="100%" border="0" cellspacing="0"  background="">
            <tr width="100%" height="100%">
                <td width="100%" height="100%">
                    <img height="300px" width="100%" src="http://<?=$host?>.<?=$domain?>/pos/showimage.php?id=<?=$email_Template_1_1[0]->image_id?>&image_db_id=<?=$email_Template_1_1[0]->image_db_id?>">
                </td>
            </tr>
        </table>
    </div>

    <div class="main_bc_color2" style="width:100%; height:60px; float:left; ">
        <div                            style="width:100%; height:05%; display:inline-block; ">
            &nbsp;
        </div>
        <div class="main_bc_color2"   style="width:100%; height:90%; display:inline-block; ">
            <a href='<?=$email_Template_Link_1[0]->value?>' style="text-decoration:none; cursor:pointer;">
            <div                        style="width:13%; height:05%; border:1px; float: left;">&nbsp;</div>
                <div class="main_bc_color1" style="cursor:pointer; width:72%; height:90%; float: left; -moz-border-radius: 15px; border-radius: 15px; border: 2px solid black;   text-align:center; ">
                    <div class="main_color1_text" style="height:30%; width:100%; float:left;">Go To</div>
                    <div class="main_color3_text" style="height:30%; width:100%; float:left; ">Our Online Survey</div>
                    <div class="main_color1_text" style="height:30%; width:100%; float:left; ">and fill it out for a chance to win $100.</div>
                </div>
            </a>
            <div                        style="width:13%; height:05%; border:1px; float: left;">&nbsp;</div>
        </div>
        <div                            style="width:100%; height:05%; display:inline-block; ">
            &nbsp;
        </div>
    </div>
<?}
function email_TemplateBottomRow($company_id,$customer_id,$general_dal,$IMAGE_DAL,$Companies_dal,$Customer_dal,$BGcolor){
list($host,$domain)                     = setup_path_mailer();
$SERVER_ADDRESS                         = $Companies_dal->get_TemplateTabData_by_Name('SERVER_ADDRESS',$company_id);
$COMPANY_URL                            = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_URL',$company_id);
$COMPANY_NAME                           = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_NAME',$company_id);
$COMPANY_Facebook_Link                  = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_Facebook_Link',$company_id);

$email_Template_1_1                     = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID($company_id,'email_Template_1_1');
$email_Template_1_2                     = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID($company_id,'email_Template_1_2');
?>
<div class="main_bc_color1 main_color1_text" style="width:100%; height:4px; float:left; ">
    &nbsp;
</div>

<div class="main_bc_color1 main_color1_text" style="width:100%; height:170px; float:left; ">
    <div style="height:100%; width:100%; ">
        <a href='http://<?=$COMPANY_URL[0]->value?>' style="text-decoration:none; cursor:pointer;">
            <img height="170px" width="100%" src="http://<?=$host?>.<?=$domain?>/pos/showimage.php?id=<?=$email_Template_1_2[0]->image_id?>&image_db_id=<?=$email_Template_1_2[0]->image_db_id?>">
        </a>
    </div>
</div>

<div class="main_bc_color1 main_color1_text" style="width:100%; height:10px; float:left; ">
    &nbsp;
</div>

<div class="main_bc_color1 main_color1_text" style="width:100%; height:30px; float:left; ">
    <a href='<?=$COMPANY_Facebook_Link[0]->value?>' style="text-decoration:none; cursor:pointer;">
    <div class="main_color1_text" style="text-align:center; width:100%; height:100%; font-size: 20px;">
        Follow us on <img src="http://<?=$host?>.<?=$domain?>/common_includes/includes/images/facebook_icon.png">
    </div>
    </a>
</div>
<?}
?>