<?
if(!isset($_SESSION)){ session_start(); }
function setup_path_massage(){
if (substr_count($_SERVER['SERVER_NAME'],".") == 1){
    $domref = "www." . $_SERVER['SERVER_NAME'] ; }
else {
    $domref =          $_SERVER['SERVER_NAME'] ; }
list($host,$domain,$ext) = split("\.",$domref);
$domain .= "." . $ext;
$_SESSION['settings']['domain'] = $domain;
#################
if (isset($_SERVER['SUBDOMAIN_DOCUMENT_ROOT'])){
    $orig_path_info = realpath($_SERVER['SUBDOMAIN_DOCUMENT_ROOT']);
    $pos = strpos($orig_path_info, '/shop/');
}
else {
    $orig_path_info = realpath($_SERVER['DOCUMENT_ROOT']);
    $pos = strpos($orig_path_info, '/shop/');
}
$shop_path  = substr($orig_path_info, 0, $pos)."/shop/";
$Zend_path  = $shop_path . 'Zend/library/';
$version    = 'v' . substr($orig_path_info, $pos + 7, strpos($orig_path_info, '/', $pos + 7) - ($pos + 7));

set_include_path($shop_path.$version.'/:'.$Zend_path);
return array ($host,$domain);
}
list($host,$domain) = setup_path_massage();

include_once("includes/general_functions.php");
include_once("includes/profiles_functions.php");
include_once("includes/appointment_functions.php");
include_once("includes/inventory_management_functions.php");
include_once("includes/companies_functions.php");
include_once("includes/preferences_functions.php");

set_preference_session($_SESSION['settings']['company_id']);
##############################################
    function loading_div(){?>
<div style="padding: 0pt; border: medium none; margin: 0pt; position: absolute; left: 0pt; top: 0pt; width: 100%; z-index: 1001;">
    <a class="highslide-loading" title="Click to cancel" href="javascript:void(0)" style="position: absolute; opacity: 0.75; left: -9999px; z-index: 1;">Loading...</a>
    <table cellspacing="0" style="padding: 0pt; border: medium none; margin: 0pt; visibility: hidden; position: absolute; border-collapse: collapse;">
        <tbody style="padding: 0pt; border: medium none; margin: 0pt;">
            <tr style="padding: 0pt; border: medium none; margin: 0pt; height: auto;">
                <td style="padding: 0pt; border: medium none; margin: 0pt; line-height: 0; font-size: 0pt; background: url(&quot;http://www.dubbsenterprises.com/wp-content/plugins/post-thumb/js/highslide/graphics/outlines/drop-shadow.png&quot;) repeat scroll 0px 0px transparent; height: 20px; width: 20px;"></td>
                <td style="padding: 0pt; border: medium none; margin: 0pt; line-height: 0; font-size: 0pt; background: url(&quot;http://www.dubbsenterprises.com/wp-content/plugins/post-thumb/js/highslide/graphics/outlines/drop-shadow.png&quot;) repeat scroll 0px -40px transparent; height: 20px; width: 20px;"></td>
                <td style="padding: 0pt; border: medium none; margin: 0pt; line-height: 0; font-size: 0pt; background: url(&quot;http://www.dubbsenterprises.com/wp-content/plugins/post-thumb/js/highslide/graphics/outlines/drop-shadow.png&quot;) repeat scroll -20px 0px transparent; height: 20px; width: 20px;"></td>
            </tr>
            <tr style="padding: 0pt; border: medium none; margin: 0pt; height: auto;"><td style="padding: 0pt; border: medium none; margin: 0pt; line-height: 0; font-size: 0pt; background: url(&quot;http://www.dubbsenterprises.com/wp-content/plugins/post-thumb/js/highslide/graphics/outlines/drop-shadow.png&quot;) repeat scroll 0px -80px transparent; height: 20px; width: 20px;"></td><td style="padding: 0pt; border: medium none; margin: 0pt; position: relative;" class="drop-shadow"></td>
                <td style="padding: 0pt; border: medium none; margin: 0pt; line-height: 0; font-size: 0pt; background: url(&quot;http://www.dubbsenterprises.com/wp-content/plugins/post-thumb/js/highslide/graphics/outlines/drop-shadow.png&quot;) repeat scroll -20px -80px transparent; height: 20px; width: 20px;"></td>
            </tr>
            <tr style="padding: 0pt; border: medium none; margin: 0pt; height: auto;"><td style="padding: 0pt; border: medium none; margin: 0pt; line-height: 0; font-size: 0pt; background: url(&quot;http://www.dubbsenterprises.com/wp-content/plugins/post-thumb/js/highslide/graphics/outlines/drop-shadow.png&quot;) repeat scroll 0px -20px transparent; height: 20px; width: 20px;"></td>
                <td style="padding: 0pt; border: medium none; margin: 0pt; line-height: 0; font-size: 0pt; background: url(&quot;http://www.dubbsenterprises.com/wp-content/plugins/post-thumb/js/highslide/graphics/outlines/drop-shadow.png&quot;) repeat scroll 0px -60px transparent; height: 20px; width: 20px;"></td>
                <td style="padding: 0pt; border: medium none; margin: 0pt; line-height: 0; font-size: 0pt; background: url(&quot;http://www.dubbsenterprises.com/wp-content/plugins/post-thumb/js/highslide/graphics/outlines/drop-shadow.png&quot;) repeat scroll -20px -20px transparent; height: 20px; width: 20px;"></td>
            </tr>
        </tbody>
    </table>
</div>
<?}
    function mainmenu() { ?>
        <div class="wp98 d_InlineBlock main_bc_color1 main_color1_text center s08 mp">
            <div class="f_left wp15 no-overflow"><img src="common_includes/includes/images/black.gif" width="5" height="8" id="services_img"> <a onclick="changeData('services');" class="menu">OUR SERVICES</a></div>
            <div class="f_left wp10 no-overflow"><img src="common_includes/includes/images/black.gif" width="5" height="8" id="about_us_img"> <a onclick="changeData('about_us');" class="menu">ABOUT US</a></div>
            <div class="f_left wp20 no-overflow"><img src="common_includes/includes/images/black.gif" width="5" height="8" id="location_img"> <a onclick="changeData('location');" class="menu">LOCATIONS & HOURS</a></div>
            <div class="f_left wp30 no-overflow"><img src="common_includes/includes/images/black.gif" width="5" height="8" id="packages_img"> <a onclick="changeData('packages');" class="menu">GIFT CERTIFICATES & PACKAGES</a></div>
            <div class="f_left wp24 no-overflow"><img src="common_includes/includes/images/black.gif" width="5" height="8" id="appointments_img"> <a id="scheduleAnAppointment" onclick="changeData('appointments');" class="menu">SCHEDULE APPOINTMENT</a></div>
        </div>
        <?php
    }
    function login_horizontal(){
	?>
	<div class="d_InlineBlock wp100 hp100 mt5">
		<?login_horizontal_data()?>
	</div>
	<?php
}
        function login_horizontal_data(){
	$Companies_dal         = new Companies_DAL();
	$COMPANY_Facebook_Link = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_Facebook_Link',$_SESSION['settings']['company_id']);
	if(!isset($_SESSION['appointment']['customer_id']))
	{
		?>
		<div class="d_InlineBlock wp100 hp100" id="login_horizontal" >
			<div id="login_horizontal_Error" class="wp50 hp100 f_left red left">
				<?
				if(count($COMPANY_Facebook_Link > 0))
				{
					?>
					<div class="fb-like ml10" data-href="<?=$COMPANY_Facebook_Link[0]->value ?>" data-send="true" data-layout="button_count" data-width="200" data-show-faces="true" data-font="lucida grande">
					</div>
					<?
				}
				else
				{
					?>
					&nbsp;
					<?
				} ?>
			</div>
			<div class="wp10 hp100 f_left s07 right">
				<div class="wp100 hp100 right mp mt5" onclick="load_registerNewUser('body_div')">
					Register?
				</div>
			</div>
			<div class="wp10 hp100 f_left">
				<a class="mp" onclick="ValidateUserQuickCheck('login_horizontal');" >
					<img src="/common_includes/includes/images/btn-login.png" alt="login" title="Login">
					</img>
				</a>
			</div>
			<div class="wp25 hp100 f_left">
				<input class="wp90" placeholder="Email Address" type="text" id="user_email_quick_check">
				</input>
			</div>
		</div>
		<?
	}
	else
	{
		?>
		<div class="d_InlineBlock wp100 hp100" id="login_horizontal" >
			<div class="d_InlineBlock wp65 hp100 p_relative">
				<div class="f_left wp100 hp100 s06 pr10 right mp" onclick="Logout('appointment','login_horizontal_div')">
					Logout?
				</div>
			</div>
			<div class="d_InlineBlock wp30 hp100">
				<div class="f_left wp100 hp100 s09 pl10 left">
					Welcome Back
					<font class="s11 bold">
						<?=$_SESSION['appointment']['first_name']?> <?=$_SESSION['appointment']['last_name']?>
					</font>
				</div>
			</div>
		</div>
		<?
	} ?>
	<?
}
    function services($IMAGE_DAL) {
            $Companies_dal                      = new Companies_DAL();
            $inventory_dal                      = new INVENTORY_DAL();
            $link_1_img_1                       = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID($_SESSION['settings']['company_id'],'link_1_img_1');
            $link_1_img_2                       = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID($_SESSION['settings']['company_id'],'link_1_img_2');

            $count = $total_service_count = $current_category_id = 0;
            $available_services                 = $inventory_dal->ServiceManagement_AllActiveServices($_SESSION['settings']['company_id']);
            ?>
    <table class="main_bc_color2_light main_color2_light_text wp98"  cellpadding="0" cellspacing="0" border="0" align="center">
    <tr>
            <td valign="top" width="75%">
                <table class="body" width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td bgcolor="#a26441" colspan="2"></td>
                    </tr>
                    <tr align="left">
                        <td bgcolor="#67381b" ><img src='common_includes/includes/images/our-services.gif' width="200" height="31" alt="Our Services" hspace="26"></td>
                        <td bgcolor="#67381b" align="right"></td>
                    </tr>
                    <tr>
                        <td>
                        <div class="wp99 d_InlineBlock mb5 ml5 scrolling " style="min-height: 400px; height: 400px;">
                            <div class="d_InlineBlock f_left center wp100 ">
                                <?  if (count($available_services) > 0 ) {
                                        foreach ($available_services as $service) {
                                            if ( $current_category_id != $service->category_id) {
                                                    $current_category_id = $service->category_id;
                                                    $count =0;
                                                    if ($total_service_count >0 ) {
                                                        ?></div><?
                                                    }
                                                ?>
                                                <div class="f_left left s15 wp100 mt10 main_color1_text main_bc_color1"><?=$service->category_name?> category.</div>
                                                <?
                                            }
                                            if (isset($_SESSION['appointment_book']['services_selected'][$service->id])) {$chooseService_Class ='white bcgreen'; } else { $chooseService_Class = ' bcgrey';}
                                            ?>
                                            <div class="d_InlineBlock wp95 center box3-black">
                                                <div class="f_left wp100 main_color1_light_text">
                                                    <div class="f_left wp20 s08 center no-overflow"><?=money2($service->price)?></div>
                                                    <div id="ChooseService_<?=$service->id?>" class="f_left wp70 s11 left bold no-overflow"><?=$service->name?></div>
                                                    <div class="f_right wp10 center no-overflow mp" onclick="appointmentProcessSelectService(<?=$service->id?>,'f_left wp70 s11 left bold no-overflow');">
                                                        <img src="common_includes/includes/images/selectIcon.png" height="22" width="22" title="Click to choose add service to basket.">
                                                    </div>
                                                </div>
                                                <div class="f_left wp100 mt1 s08 h60px bclightgray text_OverFlow_ellipsis scrolling main_color1_light_text"  title="<?=$service->style?>">
                                                    ~ <?=$service->est_time_mins?> minutes. - <?=$service->style?>
                                                </div>
                                            </div>
                                            <div class="f_left wp01">&nbsp;</div>
                            </div>
                            <div class="d_InlineBlock wp100"><? 
                            $total_service_count++;
                            }
                            } else { ?>
                                    <div class="f_left left wp90 no-overflow">
                                        There are not any active services to choose from at this time.
                                    </div>
                            <?} ?>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
            <td bgcolor="#a26441" rowspan="3" valign="top"  width="25%">
            <table class="sidebar" cellpadding="0" cellspacing="0" border="0">
            <tr><td><img src="/pos/showimage.php?id=<?=$link_1_img_1[0]->image_id?>&image_db_id=<?=$link_1_img_1[0]->image_db_id?>" width="259" height="350" alt=""></td></tr>
            <tr><td bgcolor="#67381b" height="1"></td></tr>
            <tr><td class="tips" valign="top">
                    <h3> Keep yourself looking tight.  First impressions only happen once. </h3>
                    <p style="margin-bottom: 20px;"></p>
            </td></tr>
            <tr><td bgcolor="#67381b" height="1"></td></tr>
            </table></td>
    </tr>
    </table>
    <?php }
    function about_us($IMAGE_DAL) {
            $Companies_dal                      = new Companies_DAL();
            $link_2_img_1                       = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID($_SESSION['settings']['company_id'],'link_2_img_1');
            $link_2_img_2                       = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID($_SESSION['settings']['company_id'],'link_2_img_2');
            $Main_About_us_Paragraph_Title      = $Companies_dal->get_TemplateTabData_by_Name('Main_About_us_Paragraph_Title',$_SESSION['settings']['company_id']);
            $Main_About_us_Paragraph            = $Companies_dal->get_TemplateTabData_by_Name('Main_About_us_Paragraph',$_SESSION['settings']['company_id']);
            ?>
            <table class="main_bc_color2_light main_color2_light_text wp98" cellpadding="0" cellspacing="0" border="0" align="center">
                <tr>
                    <td valign="top" width="75%">
                        <table class="body"  width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td bgcolor="#a26441" colspan="2"></td>
                            </tr>
                            <tr>
                                <td bgcolor="#67381b" ><img src="common_includes/includes/images/about-us.gif" width="200" height="31" alt="About Us" hspace="26"></td>
                                <td bgcolor="#67381b" align="right"></td>
                            </tr>
                            <tr>
                                <td colspan="2" valign="top" class="content">
                                    <?=$Main_About_us_Paragraph_Title[0]->value?>
                                    <?=$Main_About_us_Paragraph[0]->value?>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td bgcolor="#a26441" rowspan="3" valign="top" width="25%">
                        <table class="sidebar" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td><img src="/pos/showimage.php?id=<?=$link_2_img_1[0]->image_id?>&image_db_id=<?=$link_2_img_1[0]->image_db_id?>" width="259" height="250" alt=""></td>
                            </tr>
                            <tr>
                                <td bgcolor="#67381b" height="1"></td>
                            </tr>
                            <tr>
                                <td class="tips" height="114" valign="top">
                                    <h3>GROOMING TIPS</h3>
                                    Shave after the shower: the hot water loosens up pores and softens hair for a closer shave.
                                </td>
                            </tr>
                            <tr>
                                <td bgcolor="#67381b" height="1"></td>
                            </tr>
                        </table>
                        <p class="sub">&nbsp;</p>
                    </td>
                </tr>
            </table>
                <?php }
    function location($IMAGE_DAL) {
            $preferences_dal                    = new Preferences_DAL();
            $Companies_dal                      = new Companies_DAL();
            $link_3_img_1                       = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID($_SESSION['settings']['company_id'],'link_3_img_1');
            $link_3_img_2                       = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID($_SESSION['settings']['company_id'],'link_3_img_2'); 
            $company_addresses                  = $preferences_dal->get_addresses_per_company_id($_SESSION['settings']['company_id']);
        ?>
    <div class="d_InlineBlock main_bc_color2 main_color2_text wp98 center">
        <div class="d_InlineBlock f_left wp75">   
            <div class="d_InlineBlock h400px">
                <div class="f_left wp100 hp02">
                    &nbsp;
                </div>

                <div class="d_InlineBlock f_left wp100 hp05 main_bc_color2_light main_color2_light_text">
                    <div class="f_left wp60 s12">
                        &nbsp;&nbsp; Location & Hours
                    </div>
                    <div class="f_right wp40 s12">
                        &nbsp;&nbsp; <?=$_SESSION['company_info'][$_SESSION['settings']['company_id']]['Phone_Number_Main']?>
                    </div>
                </div>

                <div class="d_InlineBlock f_left wp100 hp85">
                    <div class="f_left wp60 hp100">
                        <?=$company_addresses[0]->google_map_url?>
                    </div>
                    
                    <div class="f_left wp05 hp100">
                        &nbsp;
                    </div>                    

                    <div class="f_right wp35 hp60">
                        <? make_appointment_step1_business_hours($_SESSION['settings']['company_id'])?>
                    </div>
                </div>
            </div>
        </div>
        <div class="d_InlineBlock f_right wp25">
            <table class="sidebar" cellpadding="0" cellspacing="0" border="0">
                <tr><td><img src="/pos/showimage.php?id=<?=$link_3_img_1[0]->image_id?>&image_db_id=<?=$link_3_img_1[0]->image_db_id?>" width="259" height="350" alt=""></td></tr>
                <tr><td bgcolor="#67381b" height="1"></td></tr>
                <tr><td class="tips" height="120" valign="top">
                        <h3>GROOMING TIPS</h3>
                        Shave in the same direction as the hair growth. Use short strokes and take care over the neck and throat.
                </td></tr>
                <tr><td bgcolor="#67381b" height="1"></td></tr>
                <tr><td><img src="/pos/showimage.php?id=<?=$link_3_img_2[0]->image_id?>&image_db_id=<?=$link_3_img_2[0]->image_db_id?>" width="259" height="350" alt=""></td></tr>
            </table>
        </div>
    </tr>
    </div>
    <?php }
    function packages($IMAGE_DAL) { 
            $Companies_dal                      = new Companies_DAL();
            $link_4_img_1                       = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID($_SESSION['settings']['company_id'],'link_4_img_1');
            $link_4_img_2                       = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID($_SESSION['settings']['company_id'],'link_4_img_2');
    ?>
    <table class="main_bc_color2_light main_color2_light_text wp98"  cellpadding="0" cellspacing="0" border="0" align="center">
    <tr>
        <td border="10" valign="top" width="75%">
            <table class="body"  cellpadding="0" cellspacing="0">
                <tr>
                    <td bgcolor="#a26441" colspan="2"></td>
                </tr>

                <tr>
                    <td height="38" bgcolor="#67381b"><img src="common_includes/includes/images/packages.gif" width="200" height="31" alt="Gift Certificates & Packages" hspace="26"></td>
                </tr>
                <tr>
                    <td class="content" valign="top" height="501" colspan="2">
                        <h2><img src="common_includes/includes/images/under_construction.jpg" width="160" height="149"></h2>
                    </td>
                </tr>
            </table>
        </td>
        <td bgcolor="#a26441" valign="top" width="25%">
            <table class="sidebar" cellpadding="0" cellspacing="0">
                <tr>
                    <td><img src="/pos/showimage.php?id=<?=$link_4_img_1[0]->image_id?>&image_db_id=<?=$link_4_img_1[0]->image_db_id?>" width="259" height="350" alt=""></td>
                </tr>
                <tr>
                    <td bgcolor="#67381b" height="1"></td>
                </tr>
                <tr>
                    <td class="tips" height="150" valign="top">
                        <h3>GROOMING TIPS</h3>Hair cuts every 4-5 weeks will keep that groomed look in tact, not grown out.
                        <br><br>Don't forget you can get complimentary neck clean ups every week from your last hair cut.
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#67381b" height="1"></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>
            </table>

        </td>
    </tr>
    </table>
    <?php }
    function copyright() {
        $general_dal            = new GENERAL_DAL();
        $Companies_dal           = new Companies_DAL();
        $company_name           = $general_dal->get_Company_Preference($_SESSION['settings']['company_id'],'company name');
        ?>
            <div class="">
                Copyright <? echo date("Y")?> <?=$company_name[0]->value?>
            </div>
        <?php
        }
  
function BarberShop_1_template(){
    global $version;
$IMAGE_DAL              = new IMAGE_DATA_DAL();
$general_dal            = new GENERAL_DAL();
$company_name           = $general_dal->get_Company_Preference($_SESSION['settings']['company_id'],'company name');
$meta_description       = $general_dal->get_Company_Preference($_SESSION['settings']['company_id'],'meta_description');
$meta_keywords          = $general_dal->get_Company_Preference($_SESSION['settings']['company_id'],'meta_keywords');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
        <title><?=$company_name[0]->value?></title>
        <link href="http://abc.com/images/landing_page/favicon.ico" rel="SHORTCUT ICON">
        <META name="description"    content="<?=$meta_description[0]->value?>">
        <META name="keywords"       content="<?=$meta_keywords[0]->value?>">
        <link   rel="stylesheet" type="text/css"    href="pos/includes/pos.css">
        <script type="text/javascript"              src="pos/includes/jQueryJS/jquery-1.4.4.min.js"></script>
        <script type="text/javascript"              src="pos/includes/jQueryJS/jquery-ui-1.8.9.custom.min.js"></script>
        <script type="text/javascript"              src= "common_includes/common.js"></script>

        <link   rel="stylesheet" type="text/css"    href="common_includes/includes/common.css" media="screen">
        <link   rel="stylesheet" type="text/css"    href="BarberShop/includes/BarberShop.css" media="screen">
        <link   rel="stylesheet" type="text/css"    href="/common_includes/colors_styles.php?style=Include" media="screen">
    </head>
    <body class="body_font main_bc_color1 m0 center">
        <div style="max-width:1000px" class="wp95 d_InlineBlock center main_bc_color1">
            <!--HEADER-->
            <div id="login_horizontal_div"  class="wp100 h30px  d_InlineBlock main_bc_color2 main_color2_text mt5">
                    <?php login_horizontal()?>
            </div>
            <div id="header_div"     class="wp100 h100px d_InlineBlock main_bc_color2">
                <?php header_1($IMAGE_DAL)?>
            </div>
            <!--MAIN MENU-->
            <div id="mainmenu_div"   class="wp100 d_InlineBlock main_bc_color2">
                <?php mainmenu($IMAGE_DAL)?>
            </div>
            <!--BODY-->
            <div id="body_div"       class="wp100 h400px d_InlineBlock main_bc_color2 main_color2_text scrolling">
                <?php body_1($IMAGE_DAL)?>
            </div>
            <!--FOOTER-->
            <div id="footer_div"     class="wp100 d_InlineBlock main_bc_color2">
                <?php footer_1($IMAGE_DAL)?>
            </div>
            <!--COPYRIGHT-->
            <div id="copyright_div"  class="wp100 d_InlineBlock main_bc_color1">
                 <?php copyright($IMAGE_DAL)?>
            </div>
            <?php loading_div()?>
        </div>
    </body>
</html>
<? }
    function header_1($IMAGE_DAL) {
        $main_company_logo = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID($_SESSION['settings']['company_id'],'main_company_logo');
        $widget[1] = '<iframe src="http://snapwidget.com/in/?u=ZXNxdWlyZWJhcmJlcnNob3B8aW58MTAwfDN8MXx8bm98NXxub25lfG9uU3RhcnR8eWVzfG5v&ve=131114" title="Instagram Widget" class="snapwidget-widget" allowTransparency="true" frameborder="0" scrolling="no" style="border:none; overflow:hidden; width:300px; height:95px"></iframe>';
        $widget[2] = '<div id=yelpwidget></div>';
        $random = rand(1, 2);
        ?>  
            <script>
                (
                function() { var   s = document.createElement("script");
                            s.async = true;
                            s.onload = s.onreadystatechange = function(){getYelpWidget("esquire-barbershop-chicago","300","RED","y","y","1");};
                            s.src='http://chrisawren.com/widgets/yelp/yelpv2.js' ;
                            var x = document.getElementsByTagName('script')[0];x.parentNode.insertBefore(s, x);
                }
                )();
            </script>
            <div class="f_left wp02 hp100">&nbsp;</div>
            <div class="f_left wp20 hp100">
                <a href="/">
                    <img class='wp100 m0 b0 <? if ($main_company_logo[0]->image_id > 0) { print ' mp'; } ?>'  height="87"  width="172" src='/pos/showimage.php?id=<?=$main_company_logo[0]->image_id?>&image_db_id=<?=$main_company_logo[0]->image_db_id?>'  <? if ($main_company_logo[0]->image_id > 0) { ?> <? } ?>  />
                </a>
            </div>
            <div class="f_left wp40 hp100">&nbsp;</div>
            <div class="f_left right wp36 hp100">
                <!-- SnapWidget -->
                <?=$widget[$random]?>
            </div>            
            <div class="f_left wp02 hp100">&nbsp;</div>  
        <?php }
    # function mainmenu above 
    function body_1($IMAGE_DAL) {
        $main_page_img_1 = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID($_SESSION['settings']['company_id'],'main_page_img_1');
        $main_page_img_2 = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID($_SESSION['settings']['company_id'],'main_page_img_2');
        $main_page_img_3 = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID($_SESSION['settings']['company_id'],'main_page_img_3');
        ?>
        <div class="wp98 hp100 d_InlineBlock main_bc_color2 center">
            <div class="f_left hp100 wp32">
                <img class='wp100 hp100 m0 <? if ($main_page_img_1[0]->image_id > 0) { print ' mp'; } ?>'  height="350"  src='/pos/showimage.php?id=<?=$main_page_img_1[0]->image_id?>&image_db_id=<?=$main_page_img_1[0]->image_db_id?>'  <? if ($main_page_img_1[0]->image_id > 0) { ?> <? } ?>  />
            </div>
            <div class="f_left hp100 wp02">&nbsp;</div>
            <div class="f_left hp100 wp31">
                <img class='wp100 hp100 m0 <? if ($main_page_img_2[0]->image_id > 0) { print ' mp'; } ?>'  height="350"  src='/pos/showimage.php?id=<?=$main_page_img_2[0]->image_id?>&image_db_id=<?=$main_page_img_2[0]->image_db_id?>'  <? if ($main_page_img_2[0]->image_id > 0) { ?> <? } ?>  />
            </div>
            <div class="f_left hp100 wp02">&nbsp;</div>
            <div class="f_right hp100 wp32">
                <img class='wp100 hp100 m0 <? if ($main_page_img_3[0]->image_id > 0) { print ' mp'; } ?>'  height="350"  src='/pos/showimage.php?id=<?=$main_page_img_3[0]->image_id?>&image_db_id=<?=$main_page_img_3[0]->image_db_id?>'  <? if ($main_page_img_3[0]->image_id > 0) { ?> <? } ?>  />
            </div>
        </div>
    <?}
    function footer_1() {
        $Companies_dal                      = new Companies_DAL();
        ?>
        <div class="d_InlineBlock wp98 h20px main_bc_color2 main_color2_text">
            <div class="wp20 hp100 f_left"><a href="http://www.yelp.com/biz/esquire-barber-chicago#hrid:7n4_BBWSqn3hFlnAWQ-IZQ/src:search/query:Esquire%20Barber%20Shop" target="_blank" class="main_color2_text">YELP</a> &nbsp;</div>
            <div class="wp80 hp100 f_right right main_color2_text"> &nbsp;  Phone:  &nbsp; <font class="phone"><?=$_SESSION['company_info'][$_SESSION['settings']['company_id']]['Phone_Number_Main']?></font></div>
        </div>
        <?php
        }

function BarberShop_2_template(){
$general_dal            = new GENERAL_DAL();
$IMAGE_DAL              = new IMAGE_DATA_DAL();
$company_name           = $general_dal->get_Company_Preference($_SESSION['settings']['company_id'],'company name');
$meta_description       = $general_dal->get_Company_Preference($_SESSION['settings']['company_id'],'meta_description');
$meta_keywords          = $general_dal->get_Company_Preference($_SESSION['settings']['company_id'],'meta_keywords');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
        <title><?=$company_name[0]->value?></title>
        <link href="http://abc.com/images/landing_page/favicon.ico" rel="SHORTCUT ICON">
        <META name="description"    content="<?=$meta_description[0]->value?>">
        <META name="keywords"       content="<?=$meta_keywords[0]->value?>">
        <link   rel="stylesheet" type="text/css"    href="pos/includes/pos.css">
        <script type="text/javascript"              src="pos/includes/jQueryJS/jquery-1.4.4.min.js"></script>
        <script type="text/javascript"              src="pos/includes/jQueryJS/jquery-ui-1.8.9.custom.min.js"></script>
        <script type="text/javascript"              src= "common_includes/common.js"></script>

        <link   rel="stylesheet" type="text/css"    href="common_includes/includes/common.css" media="screen">
        <link   rel="stylesheet" type="text/css"    href="/common_includes/colors_styles.php?style=Include" media="screen">
    </head>
    <body class="main_bc_color1 m0 center">
        <div class="d_InlineBlock w884 main_bc_color1">
            <!--HEADER-->
            <div id="login_horizontal_div" class="wp100 d_InlineBlock main_bc_color2">
                <?php login_horizontal()?>
            </div>
            <div id="header_div"    class="wp100 d_InlineBlock main_bc_color2">
                <?php header_2($IMAGE_DAL)?>
            </div>
            <!--MAIN MENU-->
            <div id="mainmenu_div"  class="wp100 d_InlineBlock main_bc_color2">
                <?php mainmenu()?>
            </div>
            <!--BODY-->
            <div id="body_div"      class="wp100 d_InlineBlock main_bc_color2">
                <?php body_2($IMAGE_DAL)?>
            </div>
            <div id="footer_div"    class="wp100 d_InlineBlock main_bc_color2">
                <?php footer_2()?>
            </div>
            <?php loading_div()?>
        </div>
    </body>
</html>
<? }
    function header_2($IMAGE_DAL){
        $main_company_logo = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID($_SESSION['settings']['company_id'],'main_company_logo');
        ?>
        <div class="d_InlineBlock center wp100">
            <div class="d_InlineBlock  wp99 center">
                <a href="/">
                    <img class='wp100 m0 b0 <? if ($main_company_logo[0]->image_id > 0) { print ' mp'; } ?>'  height="175"  width="100%" src='/pos/showimage.php?id=<?=$main_company_logo[0]->image_id?>&image_db_id=<?=$main_company_logo[0]->image_db_id?>'  <? if ($main_company_logo[0]->image_id > 0) { ?> <? } ?>  />
                </a>
           </div>
        </div>
    <?}
    # function mainmenu above
    function body_2($IMAGE_DAL) {
        $main_page_img_1 = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID($_SESSION['settings']['company_id'],'main_page_img_1');
        $main_page_img_2 = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID($_SESSION['settings']['company_id'],'main_page_img_2');
        $main_page_img_3 = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID($_SESSION['settings']['company_id'],'main_page_img_3');
        ?>
        <div class="wp98 hp100 d_InlineBlock main_bc_color2 center">
            <div class="f_left hp100 wp32">
                <img class='wp100 hp100 m0 <? if ($main_page_img_1[0]->image_id > 0) { print ' mp'; } ?>'  height="350"  src='/pos/showimage.php?id=<?=$main_page_img_1[0]->image_id?>&image_db_id=<?=$main_page_img_1[0]->image_db_id?>'  <? if ($main_page_img_1[0]->image_id > 0) { ?> <? } ?>  />
            </div>
            <div class="f_left hp100 wp02">&nbsp;</div>
            <div class="f_left hp100 wp31">
                <img class='wp100 hp100 m0 <? if ($main_page_img_2[0]->image_id > 0) { print ' mp'; } ?>'  height="350"  src='/pos/showimage.php?id=<?=$main_page_img_2[0]->image_id?>&image_db_id=<?=$main_page_img_2[0]->image_db_id?>'  <? if ($main_page_img_2[0]->image_id > 0) { ?> <? } ?>  />
            </div>
            <div class="f_left hp100 wp02">&nbsp;</div>
            <div class="f_left hp100 wp32">
                <img class='wp100 hp100 m0 <? if ($main_page_img_3[0]->image_id > 0) { print ' mp'; } ?>'  height="350"  src='/pos/showimage.php?id=<?=$main_page_img_3[0]->image_id?>&image_db_id=<?=$main_page_img_3[0]->image_db_id?>'  <? if ($main_page_img_3[0]->image_id > 0) { ?> <? } ?>  />
            </div>
        </div>
    <?}
    function footer_2(){
        $Companies_dal                      = new Companies_DAL();
        $general_dal                        = new GENERAL_DAL();
        $company_name                       = $general_dal->get_Company_Preference($_SESSION['settings']['company_id'],'company name');
        $PHYSICAL_ADDRESS                   = $Companies_dal->get_TemplateTabData_by_Name('PHYSICAL_ADDRESS',$_SESSION['settings']['company_id']);
        $Phone_Number_Main                  = $Companies_dal->get_TemplateTabData_by_Name('Phone_Number_Main',$_SESSION['settings']['company_id']);
        $Phone_Number_2                     = $Companies_dal->get_TemplateTabData_by_Name('Phone_Number_2',$_SESSION['settings']['company_id']);
        ?>
        <div class="d_InlineBlock wp98 main_bc_color1 mb5">
            <div class="f_left wp30">
                <p align="center">
                    <a href="http://www.abmp.com/home/" target="_blank">
                    <img height="97" border="0" width="100" src="common_includes/includes/images/abmplogo1.jpg"><br>
                        <span style="font-variant: small-caps">
                            <font face="Microsoft JhengHei" color="#996633" size="1">member of<br>associated bodywork and massage professionals </font>
                        </span>
                    </a>
                </p>
            </div>
            <div class="f_left wp70">
                <p align="center">
                    <span style="font-variant: small-caps; font-weight: 700">
                        <?=$company_name[0]->value?><br>
                        <?=$PHYSICAL_ADDRESS[0]->value?><br>
                    </span>
                    <span style="font-variant: small-caps; font-weight: 700">
                        <font size="2">Office: </font>
                        <font size="4"><?=$Phone_Number_Main[0]->value?> </font>
                    </span>
                    <span style="font-variant: small-caps; font-weight: 700">
                        <font size="2">Cellular: </font>
                        <font size="4"><?=$Phone_Number_2[0]->value?></font><br>
                    </span>
                    <span style="font-variant: small-caps; font-weight: 700"><br>
                        click <a href="mailto:gloria@gtmassageandskincare.com?subject=I'm Interested In GT Massage and Skin Care"> here</a> to send us an email
                    </span>
                </p>
            </div>
        </div>
    <?}

function BarberShop_3_template(){
$general_dal            = new GENERAL_DAL();
$IMAGE_DAL              = new IMAGE_DATA_DAL();
$company_name           = $general_dal->get_Company_Preference($_SESSION['settings']['company_id'],'company name');
$meta_description       = $general_dal->get_Company_Preference($_SESSION['settings']['company_id'],'meta_description');
$meta_keywords          = $general_dal->get_Company_Preference($_SESSION['settings']['company_id'],'meta_keywords');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
        <title><?=$company_name[0]->value?></title>
        <link href="http://abc.com/images/landing_page/favicon.ico" rel="SHORTCUT ICON">
        <META name="description"    content="<?=$meta_description[0]->value?>">
        <META name="keywords"       content="<?=$meta_keywords[0]->value?>">
        <link   rel="stylesheet" type="text/css"    href="pos/includes/pos.css">
        <script type="text/javascript"              src="pos/includes/jQueryJS/jquery-1.4.4.min.js"></script>
        <script type="text/javascript"              src="pos/includes/jQueryJS/jquery-ui-1.8.9.custom.min.js"></script>
        <script type="text/javascript"              src= "common_includes/common.js"></script>

        <link   rel="stylesheet" type="text/css"    href="common_includes/includes/common.css" media="screen">
        <link   rel="stylesheet" type="text/css"    href="/common_includes/colors_styles.php?style=Include" media="screen">
    </head>
    <body class="main_bc_color1 m0 center">
        <div class="d_InlineBlock w700 main_bc_color1">
            <!--HederEx-->
            <div id="headerex" class="headerex">
            </div>
            <div id="maincontainer" class="maincontainer">
                <!--HEADER-->
                <div id="header_div"    class="wp100 d_InlineBlock main_bc_color2">
                    <?php header_3()?>
                </div>
                <!--MAIN MENU-->
                <div id="mainmenu_div"  class="wp100 d_InlineBlock main_bc_color2">
                    <?php mainmenu()?>
                </div>
                <!--BODY-->
                <div id="body_div"      class="wp100 d_InlineBlock main_bc_color2">
                    <?php body_3()?>
                </div>
                <div id="footer_div"    class="wp100 d_InlineBlock main_bc_color2">
                    <?php footer_3()?>
                </div>
            </div>
            <?php loading_div()?>
        </div>
    </body>
</html>
<? }
    function header_3($IMAGE_DAL){?>
    <div class="d_InlineBlock wp100">
        <img border="0" src="/common_includes/includes/images/belmontHeader.png">
    </div>
    <?}
    # function mainmenu above
    function body_3($IMAGE_DAL) { ?>
        <div class="wp98 hp100 d_InlineBlock mb100">
            <div class="f_left wp50 hp100 white">
                <span style="color:#ffffff; line-height:11px; font-size:12px;">
                    <h2 style="text-align: center; color:white;"><strong>CUTS $17 SHAVES $33</strong></h2>
                    <h3 style="text-align: center; color:white;"><strong> 773-296-0894</strong></h3>
                    <h3 style="text-align: center; color:white;">
                        <span style="text-decoration: underline; color:white;">
                            <strong>APPOINTMENT HOURS<br></strong>
                        </span>
                        <p class="white"><strong>Tuesday through Friday 8 - 7</strong></p>
                        <p class="white"><strong>Saturday 8 - 3</strong></p>
                        <p class="white"><strong><span style="text-decoration: underline;">WALK IN HOURS</span></strong></p>
                        <p class="white"><strong>Tuesday through Friday 9-5</strong></p>
                        <p class="white"><strong>Saturday 8-2<br></strong></p>
                    </h3>
                </span>
            </div>
            <div class="f_left wp50 hp100 mt15">
                <img height="209" width="314" alt="" src="/common_includes/includes/images/BelmontBarbershopMainImage.jpg">
            </div>
        </div>
    <?}
    function footer_3($IMAGE_DAL){?>
        <div class="d_InlineBlock wp98 main_bc_color2 mb5">

    	<div class="bcred footertop">&nbsp;</div>
    	<div class="main_bc_color2">
        	<div class="d_InlineBlock wp100 m0 center white">
                    <a href="home">Home</a>
                    <a href="news">News</a>
                    <a href="about">About</a>
                    <a href="barbers">Barbers</a>
                    <a href="contact">Contact</a>
                    <a href="links">Links</a>
                </div>
                <div class="d_InlineBlock f_left wp100 left white">Follow us on your favorite social network!</div>
	    	<div class="wp100 d_InlineBlock">
                        <div class="f_left wp05">
                            &nbsp;
                        </div>
                        <div class="f_left wp06">
                            <a target="_blank" href="http://www.myspace.com/belmontbarbershop" class="soc">
                            <p><img width="60%" border="0" src="http://www.dubbsenterprises.com/wp-content/themes/bbs/_images/myspace.jpg"><br>
                            <span class="white">MySpace</span></p>
                            </a>
                        </div>
                        <div class="f_left wp06">
                            <a target="_blank" href="http://twitter.com/belmontbarber" class="soc">
                            <p><img width="60%" border="0" src="http://www.dubbsenterprises.com/wp-content/themes/bbs/_images/twitter.jpg"><br>
                            <span class="white">Twitter</span></p>
                            </a>
                        </div>
                        <div class="f_left wp06">
                            <a target="_blank" ref="ts&amp;ajaxpipe=1&amp;__a=5" href="http://www.facebook.com/home.php?#!/pages/Chicago-IL/The-Belmont-Barbershop-Ltd/61271439545?" class="soc">
                            <p><img width="60%" border="0" src="http://www.dubbsenterprises.com/wp-content/themes/bbs/_images/facebook.jpg"><br>
                            <span class="white">Facebook</span></p>
                            </a>
                        </div>
                        <div class="f_left wp06">
                            <a rel="superbox[iframe][480x520]" href="email-us" class="soc">
                            <p><img width="60%" border="0" src="http://www.dubbsenterprises.com/wp-content/themes/bbs/_images/email.jpg"><br>
                            <span class="white">Email us</span></p>
                            </a>
                         </div>
                        <div class="f_left wp65 ml30 left white s08">
                            <br>
                            The Belmont Barbershop - 2328 W. Belmont Ave. - Chicago, IL 60618 - 773-296-0894<br>
                            All images and content are property of The Belmont Barber Shop Ltd. (c) 2012<br>
                        </div>
                </div>
        </div>
	</div>
    <?}

function BarberShop_4_template(){
$general_dal            = new GENERAL_DAL();
$IMAGE_DAL              = new IMAGE_DATA_DAL();
$company_name           = $general_dal->get_Company_Preference($_SESSION['settings']['company_id'],'company name');
$meta_description       = $general_dal->get_Company_Preference($_SESSION['settings']['company_id'],'meta_description');
$meta_keywords          = $general_dal->get_Company_Preference($_SESSION['settings']['company_id'],'meta_keywords');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
        <title><?=$company_name[0]->value?></title>
        <link href="http://abc.com/images/landing_page/favicon.ico" rel="SHORTCUT ICON">
        <META name="description"    content="<?=$meta_description[0]->value?>">
        <META name="keywords"       content="<?=$meta_keywords[0]->value?>">
        <link   rel="stylesheet" type="text/css"    href="pos/includes/pos.css">
        <script type="text/javascript"              src="pos/includes/jQueryJS/jquery-1.4.4.min.js"></script>
        <script type="text/javascript"              src="pos/includes/jQueryJS/jquery-ui-1.8.9.custom.min.js"></script>
        <script type="text/javascript"              src= "common_includes/common.js"></script>

        <link   rel="stylesheet" type="text/css"    href="common_includes/includes/common.css" media="screen">
        <link   rel="stylesheet" type="text/css"    href="/common_includes/colors_styles.php?style=Include" media="screen">
    </head>
    <body class="main_bc_color1 m0 center">
        <div class="d_InlineBlock w700 main_bc_color1">
            <!--HederEx-->
            <div id="headerex" class="headerex">
            </div>
            <div id="maincontainer" class="maincontainer">
                <div id="login_horizontal_div" class="wp100 d_InlineBlock main_bc_color2">
                    <?php login_horizontal()?>
                </div>
                <!--HEADER-->
                <div id="header_div"    class="wp100 d_InlineBlock main_bc_color2">
                    <?php header_4()?>
                </div>
                <!--MAIN MENU-->
                <div id="mainmenu_div"  class="wp100 d_InlineBlock main_bc_color2">
                    <?php mainmenu()?>
                </div>
                <!--BODY-->
                <div id="body_div"      class="wp100 d_InlineBlock main_bc_color2">
                    <?php body_4()?>
                </div>
                <div id="footer_div"    class="wp100 d_InlineBlock main_bc_color2">
                    <?php footer_4()?>
                </div>
            </div>
            <?php loading_div()?>
        </div>
    </body>
</html>
<? }
    function header_4($IMAGE_DAL){?>
    <div class="d_InlineBlock wp100">
        <img border="0" src="/common_includes/includes/images/belmontHeader.png">
    </div>
    <?}
    # function mainmenu above
    function body_4($IMAGE_DAL) { ?>
        <div class="wp98 hp100 d_InlineBlock mb100">
            <div class="f_left hp100 wp50 white">
                <span style="color:#ffffff; line-height:11px; font-size:12px;">
                    <h2 style="text-align: center; color:white;"><strong>CUTS $17 SHAVES $33</strong></h2>
                    <h3 style="text-align: center; color:white;"><strong> 773-296-0894</strong></h3>
                    <h3 style="text-align: center; color:white;">
                        <span style="text-decoration: underline; color:white;">
                            <strong>APPOINTMENT HOURS<br></strong>
                        </span>
                        <p class="white"><strong>Tuesday through Friday 8 - 7</strong></p>
                        <p class="white"><strong>Saturday 8 - 3</strong></p>
                        <p class="white"><strong><span style="text-decoration: underline;">WALK IN HOURS</span></strong></p>
                        <p class="white"><strong>Tuesday through Friday 9-5</strong></p>
                        <p class="white"><strong>Saturday 8-2<br></strong></p>
                    </h3>
                </span>
            </div>
            <div class="f_left hp100 wp50 mt15">
                <img height="209" width="314" alt="" src="/common_includes/includes/images/BelmontBarbershopMainImage.jpg">
            </div>
        </div>
    <?}
    function footer_4($IMAGE_DAL){?>
        <div class="d_InlineBlock wp98 main_bc_color2 mb5">

    	<div class="bcred footertop">&nbsp;</div>
    	<div class="main_bc_color2">
        	<div class="d_InlineBlock wp100 m0 center white">
                    <a href="home">Home</a>
                    <a href="news">News</a>
                    <a href="about">About</a>
                    <a href="barbers">Barbers</a>
                    <a href="contact">Contact</a>
                    <a href="links">Links</a>
                </div>
                <div class="d_InlineBlock f_left wp100 left white">Follow us on your favorite social network!</div>
	    	<div class="wp100 d_InlineBlock">
                        <div class="f_left wp05">
                            &nbsp;
                        </div>
                        <div class="f_left wp06">
                            <a target="_blank" href="http://www.myspace.com/belmontbarbershop" class="soc">
                            <p><img width="60%" border="0" src="http://www.dubbsenterprises.com/wp-content/themes/bbs/_images/myspace.jpg"><br>
                            <span class="white">MySpace</span></p>
                            </a>
                        </div>
                        <div class="f_left wp06">
                            <a target="_blank" href="http://twitter.com/belmontbarber" class="soc">
                            <p><img width="60%" border="0" src="http://www.dubbsenterprises.com/wp-content/themes/bbs/_images/twitter.jpg"><br>
                            <span class="white">Twitter</span></p>
                            </a>
                        </div>
                        <div class="f_left wp06">
                            <a target="_blank" ref="ts&amp;ajaxpipe=1&amp;__a=5" href="http://www.facebook.com/home.php?#!/pages/Chicago-IL/The-Belmont-Barbershop-Ltd/61271439545?" class="soc">
                            <p><img width="60%" border="0" src="http://www.dubbsenterprises.com/wp-content/themes/bbs/_images/facebook.jpg"><br>
                            <span class="white">Facebook</span></p>
                            </a>
                        </div>
                        <div class="f_left wp06">
                            <a rel="superbox[iframe][480x520]" href="email-us" class="soc">
                            <p><img width="60%" border="0" src="http://www.dubbsenterprises.com/wp-content/themes/bbs/_images/email.jpg"><br>
                            <span class="white">Email us</span></p>
                            </a>
                         </div>
                        <div class="f_left wp65 ml30 left white s08">
                            <br>
                            The Belmont Barbershop - 2328 W. Belmont Ave. - Chicago, IL 60618 - 773-296-0894<br>
                            All images and content are property of The Belmont Barber Shop Ltd. (c) 2012<br>
                        </div>
                </div>
        </div>
	</div>
    <?}

function BarberShop_5_template(){
global $version;
$template_name          = __FUNCTION__ ;
$IMAGE_DAL              = new IMAGE_DATA_DAL();
$general_dal            = new GENERAL_DAL();
$company_name           = $general_dal->get_Company_Preference($_SESSION['settings']['company_id'],'company name');
$meta_description       = $general_dal->get_Company_Preference($_SESSION['settings']['company_id'],'meta_description');
$meta_keywords          = $general_dal->get_Company_Preference($_SESSION['settings']['company_id'],'meta_keywords');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
        <title><?=$company_name[0]->value?></title>
        <link href="http://abc.com/images/landing_page/favicon.ico" rel="SHORTCUT ICON">
        <META name="description"    content="<?=$meta_description[0]->value?>">
        <META name="keywords"       content="<?=$meta_keywords[0]->value?>">
        <link   rel="stylesheet" type="text/css"    href="pos/includes/pos.css">
        <script type="text/javascript"              src="pos/includes/jQueryJS/jquery-1.4.4.min.js"></script>
        <script type="text/javascript"              src="pos/includes/jQueryJS/jquery-ui-1.8.9.custom.min.js"></script>
        <script type="text/javascript"              src= "common_includes/common.js"></script>

        <link   rel="stylesheet" type="text/css"    href="common_includes/includes/common.css" media="screen">
        <link   rel="stylesheet" type="text/css"    href="BarberShop/includes/BarberShop.css" media="screen">
        <link   rel="stylesheet" type="text/css"    href="/common_includes/colors_styles.php?style=Include" media="screen">
        <link   rel="stylesheet" type="text/css"    href="BarberShop/includes/<?=__FUNCTION__?>/css/styles.css" >
        <link   type="text/css" rel="stylesheet"    href="http://fonts.googleapis.com/css?family=PT+Sans:400,400italic,700,700italic">       
    </head>
    <body class="body_font main_bc_color1 m0 center">
        <div style="" class="w1000 d_InlineBlock center main_bc_color1">        
            <div id="login_horizontal_div">
                <?php login_horizontal_5($IMAGE_DAL,$template_name);?>
            </div>
             
            <div id="header_div">
                <?php header_5($IMAGE_DAL,$template_name);?>
            </div>
            
            <div id="menu_div">
                <?php menu_5($IMAGE_DAL,$template_name);?>
            </div>
            
            <div id="body_div"       class="wp100 h400px f_left main_bc_color2 main_color2_text scrolling-y">
                <?php body_5($IMAGE_DAL,$template_name);?>
            </div>
             
            <div id="copyright_div">
                <?php copyright_5($IMAGE_DAL,$template_name);?>
            </div>
            
            <div id="loading_div">
                <?php loading_div($IMAGE_DAL,$template_name)?>
            </div>
        </div>
        <script src="BarberShop/includes/<?=__FUNCTION__?>/js/jquery.validate.js"></script>
        <script src="BarberShop/includes/<?=__FUNCTION__?>/js/jquery.bxslider.js"></script>
        <script src="BarberShop/includes/<?=__FUNCTION__?>/js/smooth-scroll.js"></script>
        <script src="BarberShop/includes/<?=__FUNCTION__?>/js/filter-gallery.js"></script>
        <script src="BarberShop/includes/<?=__FUNCTION__?>/js/jquery.easing.1.3.js"></script>
        <script src="BarberShop/includes/<?=__FUNCTION__?>/js/jquery.prettyPhoto.js"></script>
        <script src="BarberShop/includes/<?=__FUNCTION__?>/js/js.js"></script>
    </body>
</html>
<? }
    function login_horizontal_5($IMAGE_DAL,$template_name){?>
        <div id="login_horizontal_div"  class="wp100 h30px  d_InlineBlock main_bc_color2 main_color2_text mt5">
                <?php login_horizontal()?>
        </div>
    <?}
    function header_5($IMAGE_DAL,$template_name){?>
            <div class="menu">
                <a href="#home"><img width="70" height="70" alt="menu" src="BarberShop/includes/<?=$template_name?>/img/menu.png"></a>
            </div>
    <?}
    function menu_5($IMAGE_DAL,$template_name){ 
        $main_company_logo  = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID($_SESSION['settings']['company_id'],'main_company_logo');
        $Companies_dal      = new Companies_DAL();
        $SERVER_ADDRESS     = $Companies_dal->get_TemplateTabData_by_Name('SERVER_ADDRESS', $_SESSION['settings']['company_id']);
        ?>
        <div class="show-bg" style="width:1000px;">
            <div class="d_Block wp100 f_left ">
                <header id="home" class="header cf">
                    <nav class="main cf">
                        <ul>
                            <li><img src="common_includes/includes/images/black.gif" width="5" height="8" id="about_us_img"><a class="mp" onclick="changeData('about_us');">About Us</a></li>
                            <li><img src="common_includes/includes/images/black.gif" width="5" height="8" id="services_img"><a class="mp" onclick="changeData('services');">Services</a></li>
                            <li><img src="common_includes/includes/images/black.gif" width="5" height="8" id="location_img"><a class="mp" onclick="changeData('location');">Location</a></li>
                            <li class="ign">
                                <div class="logo">
                                    <a href="http://<?=$SERVER_ADDRESS[0]->value?>">
                                        <img alt="Logo" src="BarberShop/includes/<?=$template_name?>/img/logo.png">
                                    </a>
                                </div>
                            </li>
                            <li><a class="mp" href="#barbers">The Barbers</a></li>
                            <li><img src="common_includes/includes/images/black.gif" width="5" height="8" id="packages_img"><a class="mp" href="#packages">Packages</a></li>
                            <li><img src="common_includes/includes/images/black.gif" width="5" height="8" id="appointments_img"><a class="mp" id="scheduleAnAppointment" onclick="changeData('appointments');">Appointment</a></li>
                        </ul>
                    </nav>
                </header>
            </div>
        </div>    
    <?} 
    function body_5($IMAGE_DAL,$template_name){?>
        <section class="feature">
            <div class="d_InlineBlock wp100 f_left " > 
                <div class="d_InlineBlock f_left wp100 hp10 s13">
                    <div class="f_left  wp25 hp100">
                        Open Monday-Saturday
                   </div>
                    <div class="f_right wp25 hp100">
                        Phone: .<?=$_SESSION['company_info'][$_SESSION['settings']['company_id']]['Phone_Number_Main']?>
                    </div>
                </div>

                <div class="d_InlineBlock wp100 hp90 slider">
                    <div class="bx-wrapper" style="max-width: 100%;">
                        <div class="bx-viewport" style="width: 100%; overflow: hidden; position: relative; height: 300px;">
                            <ul class="bxslider" style="width: auto; position: relative;">
                                <li style="float: none; list-style: none outside none; position: absolute; width: 580px; z-index: 50; display: block;"><img width="580"   height="465" src="BarberShop/includes/<?=$template_name?>/img/slide01.png" alt="Slider Preview"></li>
                                <li style="float: none; list-style: none outside none; position: absolute; width: 580px; z-index: 0; display: none;">  <img width="580"   height="465" src="BarberShop/includes/<?=$template_name?>/img/slide02.png" alt="Slider Preview"></li>
                                <li style="float: none; list-style: none outside none; position: absolute; width: 580px; z-index: 0; display: none;">  <img width="580"   height="465" src="BarberShop/includes/<?=$template_name?>/img/slide03.png" alt="Slider Preview"></li>
                            </ul>
                        </div>
                        <div class="bx-controls bx-has-pager bx-has-controls-direction">
                            <div class="bx-pager bx-default-pager">
                                <div class="bx-pager-item">
                                    <a class="bx-pager-link active" data-slide-index="0" href="">1</a>
                                </div>
                                <div class="bx-pager-item">
                                    <a class="bx-pager-link"        data-slide-index="1" href="">2</a>
                                </div>
                                <div class="bx-pager-item">
                                    <a class="bx-pager-link"        data-slide-index="2" href="">3</a>
                                </div>
                            </div>
                            <div class="bx-controls-direction">
                                <a href="" class="bx-prev">Prev</a>
                                <a href="" class="bx-next">Next</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    <?}
    function copyright_5($IMAGE_DAL,$template_name){?>
        <div id="copyright_div"  class="mt5 wp100 d_InlineBlock main_bc_color2 main_color2_text">
             <?php copyright($IMAGE_DAL)?>
        </div>
    <?}
    function footer_5($IMAGE_DAL,$template_name){?>
    <?}