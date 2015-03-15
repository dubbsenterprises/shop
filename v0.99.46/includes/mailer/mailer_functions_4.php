<?php
    function email_Template_4_Row_1($company_id,$customer_id,$general_dal,$IMAGE_DAL,$Companies_dal,$Customer_dal,$BGcolor){
    list($host,$domain)                     = setup_path_mailer();
    $SERVER_ADDRESS                         = $Companies_dal->get_TemplateTabData_by_Name('SERVER_ADDRESS',$company_id);
    $COMPANY_URL                            = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_URL',$company_id);
    $COMPANY_NAME                           = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_NAME',$company_id);
    $PHYSICAL_ADDRESS                       = $Companies_dal->get_TemplateTabData_by_Name('PHYSICAL_ADDRESS',$_SESSION['settings']['company_id']);
    $Phone_Number_Main                      = $Companies_dal->get_TemplateTabData_by_Name('Phone_Number_Main',$_SESSION['settings']['company_id']);
    $COMPANY_RSS_Link                       = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_RSS_Link',$company_id);
    $COMPANY_Facebook_Link                  = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_Facebook_Link',$company_id);
    $COMPANY_Linkedin_Link                  = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_Linkedin_Link',$company_id);
    $COMPANY_Twitter_Link                   = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_Twitter_Link',$company_id);

    $Customer_Info                          = $Customer_dal->get_CustomerDataPerId($customer_id);
    ?>
    <tr>
        <td style="padding-top:5px;padding-right:15px;vertical-align:top;" rowspan="4" width="50%" >
            <img style="vertical-align:top;" alt="" src="http://<?=$host?>.<?=$domain?>/common_includes/includes/images/holiday_greetings.jpg" width="300" height="527" >
        </td>
        <td style="padding-bottom:5px;border-left-color:currentColor;border-left-width:1px;border-left-style:none;background-color:rgb(51,51,102);" height="198" width="50%" valign="bottom" colspan="2" >
            <div >
                <span style="color:rgb(254,254,252);line-height:150%;font-family:arial;font-size:16px;font-weight:bold;">Dear <?=ucfirst($Customer_Info[0]->firstname)?>,</span><br>
                <img style="padding-top:15px;width:90%;" alt="Good Wishes Image" src="http://<?=$host?>.<?=$domain?>/common_includes/includes/images/celebrate_the_season.jpg" >
            </div>
        </td>
    </tr>
    <?}
    function email_Template_4_Row_2($company_id,$customer_id,$general_dal,$IMAGE_DAL,$Companies_dal,$Customer_dal,$BGcolor){
    list($host,$domain)                     = setup_path_mailer(); 
    $SERVER_ADDRESS                         = $Companies_dal->get_TemplateTabData_by_Name('SERVER_ADDRESS',$company_id);
    $COMPANY_URL                            = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_URL',$company_id);
    $COMPANY_NAME                           = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_NAME',$company_id);
    $PHYSICAL_ADDRESS                       = $Companies_dal->get_TemplateTabData_by_Name('PHYSICAL_ADDRESS',$_SESSION['settings']['company_id']);
    $Phone_Number_Main                      = $Companies_dal->get_TemplateTabData_by_Name('Phone_Number_Main',$_SESSION['settings']['company_id']);
    $COMPANY_RSS_Link                       = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_RSS_Link',$company_id);
    $COMPANY_Facebook_Link                  = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_Facebook_Link',$company_id);
    $COMPANY_Linkedin_Link                  = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_Linkedin_Link',$company_id);
    $COMPANY_Twitter_Link                   = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_Twitter_Link',$company_id);
    ?>
    <tr >
        <td style="border-left-color:currentColor;border-left-width:1px;border-left-style:none;background-color:rgb(51,51,102);" height="120" valign="bottom" colspan="2" >
            <div style="color:rgb(254,254,252);line-height:150%;padding-bottom:5px;font-family:arial;font-size:15px;font-weight:bold;">
                <br>
            </div>
            <div style="margin:10px 0px;color:white;line-height:100%;padding-bottom:5px;font-family:arial;font-size:15px;font-weight:bold;" >
              <span>Warm regards,</span><br>
              <span><?=$COMPANY_NAME[0]->value?></span><br>
            </div>
        </td>
    </tr>
    <?}
    function email_Template_4_Row_3($company_id,$customer_id,$general_dal,$IMAGE_DAL,$Companies_dal,$Customer_dal,$BGcolor){
    list($host,$domain)                     = setup_path_mailer();
    $SERVER_ADDRESS                         = $Companies_dal->get_TemplateTabData_by_Name('SERVER_ADDRESS',$company_id);
    $COMPANY_URL                            = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_URL',$company_id);
    $COMPANY_NAME                           = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_NAME',$company_id);
    $PHYSICAL_ADDRESS                       = $Companies_dal->get_TemplateTabData_by_Name('PHYSICAL_ADDRESS',$_SESSION['settings']['company_id']);
    $Phone_Number_Main                      = $Companies_dal->get_TemplateTabData_by_Name('Phone_Number_Main',$_SESSION['settings']['company_id']);
    $COMPANY_RSS_Link                       = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_RSS_Link',$company_id);
    $COMPANY_Facebook_Link                  = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_Facebook_Link',$company_id);
    $COMPANY_Linkedin_Link                  = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_Linkedin_Link',$company_id);
    $COMPANY_Twitter_Link                   = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_Twitter_Link',$company_id);
    $link_2_img_1                           = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID($company_id,'link_2_img_1');
    ?>
    <tr >
        <td style="padding-bottom:13px;border-left-width:1px;border-left-style:none;background-color:rgb(51,51,102);" valign="bottom" colspan="2" >
            <div style="float:left;line-height:120%;width:40%;">
                <img style="margin-right:20px;" alt="Personal photo" src="http://<?=$host?>.<?=$domain?>/pos/showimage.php?id=<?=$link_2_img_1[0]->image_id?>&image_db_id=<?=$link_2_img_1[0]->image_db_id?>" width="94" height="114" >
            </div>
            <div style="float:left;line-height:120%;padding-top:20px;color:white;width:60%;" >
                <?=$COMPANY_NAME[0]->value?><br>
                <?=$Phone_Number_Main[0]->value?>
            </div>
      </td>
    </tr>
    <?}
    function email_Template_4_Row_4($company_id,$customer_id,$general_dal,$IMAGE_DAL,$Companies_dal,$Customer_dal,$BGcolor){
    list($host,$domain)                     = setup_path_mailer();
    $SERVER_ADDRESS                         = $Companies_dal->get_TemplateTabData_by_Name('SERVER_ADDRESS',$company_id);
    $COMPANY_URL                            = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_URL',$company_id);
    $COMPANY_NAME                           = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_NAME',$company_id);
    $PHYSICAL_ADDRESS                       = $Companies_dal->get_TemplateTabData_by_Name('PHYSICAL_ADDRESS',$_SESSION['settings']['company_id']);
    $Phone_Number_Main                      = $Companies_dal->get_TemplateTabData_by_Name('Phone_Number_Main',$_SESSION['settings']['company_id']);
    $COMPANY_RSS_Link                       = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_RSS_Link',$company_id);
    $COMPANY_Facebook_Link                  = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_Facebook_Link',$company_id);
    $COMPANY_Linkedin_Link                  = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_Linkedin_Link',$company_id);
    $COMPANY_Twitter_Link                   = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_Twitter_Link',$company_id);
    $email_Template_1_1                     = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID($company_id,'email_Template_1_1');
    $email_Template_1_2                     = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID($company_id,'email_Template_1_2');
    ?>
    <tr>
        <td style="border-left-color:currentColor;border-left-width:1px;border-left-style:none;background-color:rgb(51,51,102);" valign="top" width="100%">
            <a href='http://<?=$COMPANY_URL[0]->value?>' style="text-decoration:none; cursor:pointer;">
            <img alt="Company logo" src="http://<?=$host?>.<?=$domain?>/pos/showimage.php?id=<?=$email_Template_1_2[0]->image_id?>&image_db_id=<?=$email_Template_1_2[0]->image_db_id?>" width="100%" height="65">
            </a>
        </td>
    </tr>
    <?}
?>
