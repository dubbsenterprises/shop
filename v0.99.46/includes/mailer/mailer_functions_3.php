<?php
    $Customer_Info                          = $Customer_dal->get_CustomerDataPerId($customer_id);


    function email_Template_3_TopRow            ($company_id,$customer_id,$general_dal,$IMAGE_DAL,$Companies_dal,$Customer_dal,$BGcolor){
    list($host,$domain)                     = setup_path_mailer();
    $COMPANY_URL                            = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_URL',$company_id);

    $email_Template_1_1                     = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID($company_id,'email_Template_1_1');
    $email_Template_1_2                     = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID($company_id,'email_Template_1_2');
    $email_Template_Link_1                  = $Companies_dal->get_TemplateTabData_by_Name('email_Template_Link_1',$company_id);
?>
<div class="main_bc_color1" style="width:100%; height:100px; float:left; ">
    <div style="height:20%; width:100%; border-bottom: 1px solid #dbd6c9;">
        <div style="float:left; height:100%; width:80%;">
            <p style="font-size: 8pt; color: #c2bcaa; margin-top: 5px;">Having trouble reading this email?</p>
        </div>

        <div style="float:left; height:100%; width:20%;">
            <div style="float:left; height:100%; width:100%;">
                <a href="http://<?=$COMPANY_URL[0]->value?>" style="font-family: Verdana; float: left; background-color:#add0d0;text-decoration: none; font-size: 12pt;">
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

            <div style="float:left; text-align:right; width:75%; height:100px; ">
                <a href="http://<?=$COMPANY_URL[0]->value?>" style="text-decoration:none; cursor:pointer;">
                    <img style="height:80px; width:130px;" src="http://<?=$host?>.<?=$domain?>/pos/showimage.php?id=<?=$email_Template_1_2[0]->image_id?>&image_db_id=<?=$email_Template_1_2[0]->image_db_id?>">
                </a>
            </div>

            <div style="float:left; text-align:right; width:5%; height:100%;">
                &nbsp;
            </div>
        </div>
    </div>
</div>
<?}
    function email_Template_3_DIV_TEXT          ($company_id,$customer_id,$general_dal,$IMAGE_DAL,$Companies_dal,$Customer_dal,$BGcolor){
    list($host,$domain)                     = setup_path_mailer();
    $COMPANY_URL                            = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_URL',$company_id);

    $email_Template_1_1                     = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID($company_id,'email_Template_1_1');
    $email_Template_1_2                     = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID($company_id,'email_Template_1_2');
    $email_Template_Link_1                  = $Companies_dal->get_TemplateTabData_by_Name('email_Template_Link_1',$company_id);            ?>
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
    function email_Template_3_DIV_PICTURE       ($company_id,$customer_id,$general_dal,$IMAGE_DAL,$Companies_dal,$Customer_dal,$BGcolor){
    list($host,$domain)                     = setup_path_mailer();
    $COMPANY_URL                            = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_URL',$company_id);

    $email_Template_1_1                     = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID($company_id,'email_Template_1_1');
    $email_Template_1_2                     = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID($company_id,'email_Template_1_2');
    $email_Template_Link_1                  = $Companies_dal->get_TemplateTabData_by_Name('email_Template_Link_1',$company_id);            ?>
    <div style="border: 1px solid #fff;">
        <div style="padding: 0px 0px 0px 5px; height: 32px; margin: 10px; background: #6c6755;">
            <h4 style="float: left;  margin: 0; padding: 0; margin-top: 8px; text-transform: uppercase; color: #fefde9;  font-size: 16px;">Flower power</h4>
            <a href="#"><img src="http://<?=$host?>.<?=$domain?>/common_includes/includes/images/go.jpg" style="float: right; margin: 0; padding: 0; border: none;" alt="Go to article"></a>
        </div>
        <img src="http://www.sanneterpstra.com/themeforest/htmlmail/images/photo.jpg" alt="Photo placeholder" style="padding: 0px 10px 10px 10px;">
    </div>
    <?}
    function email_Template_3_DIV_TEXT_n_PICTURE($company_id,$customer_id,$general_dal,$IMAGE_DAL,$Companies_dal,$Customer_dal,$BGcolor){
    list($host,$domain)                     = setup_path_mailer();
    $COMPANY_URL                            = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_URL',$company_id);
    $Customer_Info                          = $Customer_dal->get_CustomerDataPerId($customer_id);

    
    $email_Template_1_1                     = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID($company_id,'email_Template_1_1');
    $email_Template_1_2                     = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID($company_id,'email_Template_1_2');
    $email_Template_Link_1                  = $Companies_dal->get_TemplateTabData_by_Name('email_Template_Link_1',$company_id);
?>
    <div style="border: 1px solid #fff; vertical-align: top; color:#fff;">
        <div style="padding: 0px 0px 0px 5px; margin: 10px; background: #6c6755;">
            <h4 style="text-align: center;  margin: 0; padding: 0; margin-top: 8px; text-transform: uppercase; color: #fefde9;  font-size: 9px;">- Aaron Williams says goodbye to Esquire -</h4>
        </div>
        <img style="height:100%; width:100%;" src="http://<?=$host?>.<?=$domain?>/pos/showimage.php?id=<?=$email_Template_1_1[0]->image_id?>&image_db_id=<?=$email_Template_1_1[0]->image_db_id?>">
        
        <p style="padding: 0px 2px 0px 2px; font-size: 09px; letter-spacing: 2px; text-align: left"><?=$Customer_Info[0]->firstname?> <?=$Customer_Info[0]->lastname?>,</p>
        <p style="padding: 0px 2px 0px 2px; font-size: 09px; letter-spacing: 2px; ">As you may or may not have heard, I will be leaving Esquire at the end of June and opening a new barbershop near Lincoln Square ( 2415 W. Lawrence Ave. Western/Lawrence).
        I just wanted to take the opportunity to thank you all for helping grow Esquire Barbershop into one of the best shops in the city.
        Esquire was a one chair operation when I first started, so to see where it is at now is very satisfying. Please be clear, Esquire is going nowhere and will still be operational under the guidance of Milosh and Tara. To those that decide to stay at Esquire or follow me to Lincoln Square Barbershop, I'd like to thank you all from the bottom of my heart.</p>
        <p style="padding: 0px 2px 0px 2px; font-size: 10px; letter-spacing: 2px; text-align: left;">Aaron "Dubs" Williams</p>
        <p style="padding: 0px 2px 0px 2px; font-size: 10px; letter-spacing: 2px; text-align: left;"><a href="http://www.lincolnsquarebarbershop.com">www.lincolnsquarebarbershop.com</a></p>
        <p style="padding: 0px 2px 0px 2px; font-size: 10px; letter-spacing: 2px; text-align: left; color:#fff">773-888-7492</p>

    </div>
    <?}
    function email_Template_3_DIV_SocialMedia   ($company_id,$customer_id,$general_dal,$IMAGE_DAL,$Companies_dal,$Customer_dal,$BGcolor){
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
            <div style="color:#fff; border: 1px solid #fff; text-align: center;">
                <h4 style="text-transform: uppercase; color: #fefde9; padding: 8px 0px 0px 5px; margin: 10px; height: 24px; background: #6e6753; font-size: 16px;">
                    Follow Lincoln Square
                </h4>
                <ul style="list-style: none; margin: 0; padding: 0;">
                    <li style="display: inline; margin: 0;" >
                        <a href='<?=$COMPANY_Twitter_Link[0]->value?>'><img style="border: none;"  src="http://<?=$host?>.<?=$domain?>/common_includes/includes/images/twitter_32.png"   alt="Twitter"></a>
                    </li>
                </ul>
                <div style="clear:both;">
                    
                </div>
            </div>
            <div style="color:#fff; border: 1px solid #fff;">
                <h4 style="text-align: center; text-transform: uppercase; color: #fefde9; padding: 8px 0px 0px 5px; margin: 10px; height: 24px; background: #6e6753; font-size: 16px;">
                    Addresses
                </h4>
                <ul style="text-align: center; list-style: none; margin: 0; padding: 0;">
                    <li style="display: inline; margin: 0;"         ><?=$COMPANY_NAME[0]->value?> <br></li>
                    <li style="display: inline; margin: 0;"         ><?=$PHYSICAL_ADDRESS[0]->value?><br></li>
                    <li style="display: inline; margin: 0;"         ><?=$Phone_Number_Main[0]->value?><br></li>
                </ul>
                <br>
                <ul style="text-align: center; list-style: none; margin: 0; padding: 0;">
                    <li style="display: inline; margin: 0;"         >Lincoln Square Barbershop<br></li>
                    <li style="display: inline; margin: 0;"         >2415 W. Lawrence Ave.<br> Chicago, IL 60625 <br></li>
                    <li style="display: inline; margin: 0;"         >773-888-7492<br></li>
                </ul>
            </div>
            <div style="color:#fff; border: 1px solid #fff;">
                <h4 style="text-align: center; text-transform: uppercase; color: #fefde9; padding: 8px 0px 0px 5px; margin: 10px; height: 24px; background: #6e6753; font-size: 16px;">
                    Press
                </h4>
                <ul style="list-style: none; margin: 0; padding: 5; font-size: small;">
                    <li style="margin: 0;"         ><a href="http://www.dnainfo.com/chicago/20140529/lincoln-square/barbershop-coming-lincoln-square-offers-neighbors-cut-of-business">DNAInfo Chicago</a></li>
                </ul>
                <ul style="list-style: none; margin: 0; padding: 5; font-size: small;">
                    <li style="margin: 0;"         ><a href="https://chicago.everyblock.com/local-business/may13-new-lincoln-square-barbershop-opening-6136837/">Everyblock - Chicago</a></li>
                </ul>
            </div>
    <?}
?>
