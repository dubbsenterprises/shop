<?php
    function email_Template_2_TopRow($company_id,$customer_id,$general_dal,$IMAGE_DAL,$Companies_dal,$Customer_dal,$BGcolor){
    list($host,$domain)                     = setup_path_mailer();
    $COMPANY_URL                            = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_URL',$company_id);

    $email_Template_1_1                     = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID($company_id,'email_Template_1_1');
    $email_Template_1_2                     = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID($company_id,'email_Template_1_2');
    $email_Template_Link_1                  = $Companies_dal->get_TemplateTabData_by_Name('email_Template_Link_1',$company_id);
?>
<div class="main_bc_color1" style="width:100%; height:150px; float:left; ">
    <div style="height:20%; width:100%; border-bottom: 1px solid #dbd6c9;">
        <div style="float:left; height:100%; width:80%;">
            <p style="font-size: 8pt; color: #c2bcaa; margin-top: 5px;">Having trouble reading this email?</p>
        </div>
        <div style="float:left; height:100%; width:20%;">
            <div style="float:left; height:100%; width:100%;">
                <a href="#" style="font-family: Verdana; float: right; background-color:#add0d0;text-decoration: none; font-size: 9pt; border-bottom: 1px solid #91b9b9; border-left: 1px solid #91b9b9; border-right: 1px solid #91b9b9;">
                    Visit Our Site
                </a>
            </div>
        </div>
    </div>

    <div style="float:left; height:5%; width:100%;">
        &nbsp;
    </div>

    <div style="float:left; height:75%; width:100%;">
        <div style="float:left; height:100%; width:100%;">
            <div style="float:left; text-align:center; width:20%; height:100%; margin-top:10px">
                <a href="http://<?=$COMPANY_URL[0]->value?>" style="text-decoration:none; cursor:pointer;">
                    <img style="height:80%; width:80%;" src="http://<?=$host?>.<?=$domain?>/common_includes/includes/images/header-book-online.png">
                </a>
            </div>

            <div style="float:left; text-align:center; width:75%; height:100%; ">
                <a href='<?=$email_Template_Link_1[0]->value?>' style="text-decoration:none; cursor:pointer;">
                    <img style="height:100%; width:100%;" src="http://<?=$host?>.<?=$domain?>/pos/showimage.php?id=<?=$email_Template_1_2[0]->image_id?>&image_db_id=<?=$email_Template_1_2[0]->image_db_id?>">
                </a>
            </div>

            <div style="float:left; text-align:right; width:5%; height:100%;">
                &nbsp;
            </div>
        </div>
    </div>
</div>
<?}
    function email_Template_DIV_TEXT($company_id,$customer_id,$general_dal,$IMAGE_DAL,$Companies_dal,$Customer_dal,$BGcolor){
    list($host,$domain)                     = setup_path_mailer();
    $COMPANY_URL                            = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_URL',$company_id);

    $email_Template_1_1                     = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID($company_id,'email_Template_1_1');
    $email_Template_1_2                     = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID($company_id,'email_Template_1_2');
    $email_Template_Link_1                  = $Companies_dal->get_TemplateTabData_by_Name('email_Template_Link_1',$company_id);
    ?>
    <div style="border: 1px solid #fff;">
        <div style="padding: 0px 0px 0px 05px; height: 32px; margin: 05px; background: #6c6755;">
                <h4 style="float: left;  margin: 0; padding: 0; margin-top: 8px; text-transform: uppercase; color: #fefde9;  font-size: 16px;">TIME TO GET READY FOR THE HOLIDAYS!</h4>
                <a href="#"><img src="http://<?=$host?>.<?=$domain?>/common_includes/includes/images/go.jpg" style="float: right; margin: 0; padding: 0; border: none;" alt="Go to article"></a>
        </div>
        <p style="padding: 0px 10px 0px 05px; font-size: 11px; letter-spacing: 2px; ">Microdermabrasion & LED Lights  regular price $160 on special in the month of December for $79  51% off.</p>
        <ul style="padding: 0px 10px 0px 05px; font-size: 11px; letter-spacing: 2px;">
                <li> The gentle abrasion of the crystals remove the dead outer layer of skin while bringing the smoother, softer under layer of skin to the surface. </li>
                <li> Followed by our deep cleansing facial to truly give your skin that added glow.</li>
                <li> This December Deal can be used as a gift certificate or all year long.</li>
                <li class="red"> View our video featuring this procedure.</li>
        </ul>
    </div>
    <?}
    function email_Template_DIV_PICTURE($company_id,$customer_id,$general_dal,$IMAGE_DAL,$Companies_dal,$Customer_dal,$BGcolor){
    list($host,$domain)                     = setup_path_mailer();
    $COMPANY_URL                            = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_URL',$company_id);

    $email_Template_1_1                     = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID($company_id,'email_Template_1_1');
    $email_Template_1_2                     = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID($company_id,'email_Template_1_2');
    $email_Template_Link_1                  = $Companies_dal->get_TemplateTabData_by_Name('email_Template_Link_1',$company_id);
    ?>
    <div style="border: 1px solid #fff;">
        <div style="padding: 0px 0px 0px 5px; height: 32px; margin: 10px; background: #6c6755;">
            <h4 style="float: left;  margin: 0; padding: 0; margin-top: 8px; text-transform: uppercase; color: #fefde9;  font-size: 16px;">Flower power</h4>
            <a href="#"><img src="http://<?=$host?>.<?=$domain?>/common_includes/includes/images/go.jpg" style="float: right; margin: 0; padding: 0; border: none;" alt="Go to article"></a>
        </div>
        <img src="http://www.sanneterpstra.com/themeforest/htmlmail/images/photo.jpg" alt="Photo placeholder" style="padding: 0px 10px 10px 10px;">
    </div>
    <?}
    function email_Template_DIV_TEXT_n_PICTURE($company_id,$customer_id,$general_dal,$IMAGE_DAL,$Companies_dal,$Customer_dal,$BGcolor){
    list($host,$domain)                     = setup_path_mailer();
    $COMPANY_URL                            = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_URL',$company_id);

    $email_Template_1_1                     = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID($company_id,'email_Template_1_1');
    $email_Template_1_2                     = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID($company_id,'email_Template_1_2');
    $email_Template_Link_1                  = $Companies_dal->get_TemplateTabData_by_Name('email_Template_Link_1',$company_id);
    ?>
            <div style="border: 1px solid #fff;">
                <div style="padding: 0px 0px 0px 5px; height: 32px; margin: 10px; background: #6c6755;">
                    <h4 style="float: left;  margin: 0; padding: 0; margin-top: 8px; text-transform: uppercase; color: #fefde9;  font-size: 16px;">Text and photo together</h4>
                    <a href="#"><img src="http://<?=$host?>.<?=$domain?>/common_includes/includes/images/go.jpg" style="float: right; margin: 0; padding: 0; border: none;" alt="Go to article"></a>
                </div>
                <img src="http://www.sanneterpstra.com/themeforest/htmlmail/images/text-photo.jpg" alt="Photo placeholder" style="padding: 0px 10px 0px 10px;">
                <p style="padding: 10px 10px 0px 10px; margin: 0;font-size: 11px; letter-spacing: 2px; ">Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Morbi commodo, ipsum sed <a style="" href="#">pharetra</a> gravida, orci magna rhoncus neque, id pulvinar odio lorem non turpis. </p>
                <ul style="padding: 0px 10px 0px 05px; font-size: 11px; letter-spacing: 2px;">
                        <li> Phasellus vel mi eget metus mollis tristique</li>
                        <li> Nulla congue feugiat nibh, non semper est accumsan in</li>
                        <li> Suspendisse aliquet felis bibendum nibh</li>
                        <li> Sed dolor nisi, faucibus nec euismod ac</li>
                </ul>
            </div>
    <?}
    function email_Template_DIV_SocialMedia($company_id,$customer_id,$general_dal,$IMAGE_DAL,$Companies_dal,$Customer_dal,$BGcolor){
    list($host,$domain)                     = setup_path_mailer();
    $SERVER_ADDRESS                         = $Companies_dal->get_TemplateTabData_by_Name('SERVER_ADDRESS',$company_id);
    $COMPANY_URL                            = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_URL',$company_id);
    $COMPANY_NAME                           = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_NAME',$company_id);
    $COMPANY_RSS_Link                       = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_RSS_Link',$company_id);
    $COMPANY_Facebook_Link                  = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_Facebook_Link',$company_id);
    $COMPANY_Linkedin_Link                  = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_Linkedin_Link',$company_id);
    $COMPANY_Twitter_Link                   = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_Twitter_Link',$company_id);
    ?>
        <div style="border: 1px solid #fff;">
            <h4 style="text-transform: uppercase; color: #fefde9; padding: 8px 0px 0px 5px; margin: 10px; height: 24px; background: #6e6753; font-size: 16px;">Follow us</h4>
            <ul style="list-style: none; margin: 0; padding: 0;">
                        <li style="display: inline; margin-left: 10px;" ><a href='<?=$COMPANY_RSS_Link[0]->value?>'     ><img style="border: none;"  src="http://<?=$host?>.<?=$domain?>/common_includes/includes/images/rss_32.png"        alt="Rss"></a></li>
                        <li style="display: inline; margin: 0;"         ><a href='<?=$COMPANY_Facebook_Link[0]->value?>'><img style="border: none;"  src="http://<?=$host?>.<?=$domain?>/common_includes/includes/images/facebook_32.png"   alt="Facebook"></a></li>
                        <li style="display: inline; margin: 0;"         ><a href='<?=$COMPANY_Linkedin_Link[0]->value?>'><img style="border: none;"  src="http://<?=$host?>.<?=$domain?>/common_includes/includes/images/linkedin_32.png"   alt="Linkedin"></a></li>
                        <li style="display: inline; margin: 0;"         ><a href='<?=$COMPANY_Twitter_Link[0]->value?>' ><img style="border: none;"  src="http://<?=$host?>.<?=$domain?>/common_includes/includes/images/twitter_32.png"    alt="Twitter"></a></li>
            </ul>
            <div style="clear:both;"></div>
            <h4 style="text-transform: uppercase; color: #fefde9; padding: 8px 0px 0px 5px; margin: 10px; height: 24px; background: #6e6753; font-size: 16px;">Sponsors</h4>
            <img src="http://www.sanneterpstra.com/themeforest/htmlmail/images/add.jpg" alt="Add" style="padding: 0px 10px;">
            <img src="http://www.sanneterpstra.com/themeforest/htmlmail/images/add.jpg" alt="Add" style="padding: 10px 10px;">
        </div>
    <?}
    function email_Template_2_Footer   ($company_id,$customer_id,$general_dal,$IMAGE_DAL,$Companies_dal,$Customer_dal,$BGcolor){
list($host,$domain)                     = setup_path_mailer();
$SERVER_ADDRESS                         = $Companies_dal->get_TemplateTabData_by_Name('SERVER_ADDRESS',$company_id);
$COMPANY_URL                            = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_URL',$company_id);
$COMPANY_NAME                           = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_NAME',$company_id);
$COMPANY_Facebook_Link                  = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_Facebook_Link',$company_id);
$email_Template_Link_1                  = $Companies_dal->get_TemplateTabData_by_Name('email_Template_Link_1',$company_id);
?>
<div class="main_bc_color1 main_color1_text" style="width:100%; float:left; font-family: Arial; ">
<div style="text-align:center; width:100%; height:100%;">
    <table width="100%" height="100%" border="0" cellspacing="0"  background="">
        <tr>
            <td colspan="3" style="height:32px; padding: 0; border-top: 1px solid #cac8c1; border-left: 1px solid #cac8c1; border-right: 1px solid #cac8c1;">
                <div style="height:32px; padding: 0; border-top: 1px solid #fff; border-left: 1px solid #fff; border-right: 1px solid #fff; overflow: hidden;">
                <p style="float: left; font-size: 8pt; margin:0;  padding: 10px;">Don't want to receive this email anymore? <a style="text-decoration:none;" href="mailto:gloria@gtmassageandskincare.com?subject=REMOVEME" target="_blank" rel="nofollow">Unsubscribe</a>.</p>
                <p style="float: right; font-size: 8pt; margin:0; padding: 10px;">Copyright &copy; <?=date("Y")?> <a style="" href="#"><?=$COMPANY_NAME[0]->value?></a>. All rights reserved.</p>
                </div>
            </td>
        </tr>
    </table>
</div>
</div>
<?}
?>
