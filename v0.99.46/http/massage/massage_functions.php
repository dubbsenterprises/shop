<?
if(!isset($_SESSION))
{
	session_start();
}
function setup_path_massage()
{
	if(substr_count($_SERVER['SERVER_NAME'],".") == 1)
	{
		$domref = "www." . $_SERVER['SERVER_NAME'] ;
	}
	else
	{
		$domref = $_SERVER['SERVER_NAME'] ;
	}
	list($host,$domain,$ext) = split("\.",$domref);
	$domain .= "." . $ext;
	$_SESSION['settings']['domain'] = $domain;
	#################
	if(isset($_SERVER['SUBDOMAIN_DOCUMENT_ROOT']))
	{
		$orig_path_info = realpath($_SERVER['SUBDOMAIN_DOCUMENT_ROOT']);
		$pos            = strpos($orig_path_info, '/shop/');
	}
	else
	{
		$orig_path_info = realpath($_SERVER['DOCUMENT_ROOT']);
		$pos            = strpos($orig_path_info, '/shop/');
	}
	$shop_path = substr($orig_path_info, 0, $pos)."/shop/";
	$Zend_path = $shop_path . 'Zend/library/';
	$version   = 'v' . substr($orig_path_info, $pos + 7, strpos($orig_path_info, '/', $pos + 7) - ($pos + 7));

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

function loading_div(){
	?>
	<div style="padding: 0pt; border: medium none; margin: 0pt; position: absolute; left: 0pt; top: 0pt; width: 100%; z-index: 1001;">
		<a
			class="highslide-loading" title="Click to cancel" href="javascript:void(0)" style="position: absolute; opacity: 0.75; left: -9999px; z-index: 1;">Loading...
		</a>
		<table cellspacing="0" style="padding: 0pt; border: medium none; margin: 0pt; visibility: hidden; position: absolute; border-collapse: collapse;">
			<tbody style="padding: 0pt; border: medium none; margin: 0pt;">
				<tr style="padding: 0pt; border: medium none; margin: 0pt; height: auto;">
					<td style="padding: 0pt; border: medium none; margin: 0pt; line-height: 0; font-size: 0pt; background: orange;">
					</td>
					<td style="padding: 0pt; border: medium none; margin: 0pt; line-height: 0; font-size: 0pt; background: orange;">
					</td>
					<td style="padding: 0pt; border: medium none; margin: 0pt; line-height: 0; font-size: 0pt; background: orange;">
					</td>
				</tr>
				<tr style="padding: 0pt; border: medium none; margin: 0pt; height: auto;">
					<td style="padding: 0pt; border: medium none; margin: 0pt; line-height: 0; font-size: 0pt; background: orange;">
					</td>
					<td style="padding: 0pt; border: medium none; margin: 0pt; position: relative;" class="drop-shadow">
					</td>
					<td style="padding: 0pt; border: medium none; margin: 0pt; line-height: 0; font-size: 0pt; background: orange;">
					</td>
				</tr>
				<tr style="padding: 0pt; border: medium none; margin: 0pt; height: auto;">
					<td style="padding: 0pt; border: medium none; margin: 0pt; line-height: 0; font-size: 0pt; background: orange;">
					</td>
					<td style="padding: 0pt; border: medium none; margin: 0pt; line-height: 0; font-size: 0pt; background: orange;">
					</td>
					<td style="padding: 0pt; border: medium none; margin: 0pt; line-height: 0; font-size: 0pt; background: orange;">
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<?
}
function mainmenu(){
	$Companies_dal                              = new Companies_DAL();
	$Appointment_Company_3rdPartyApptURL        = $Companies_dal->get_TemplateTabData_by_Name('Appointment_Company_3rdPartyApptURL',$_SESSION['settings']['company_id']);
	$Appointment_Company_3rdPartyApptURL_Status = $Companies_dal->get_TemplateTabData_by_Name('Appointment_Company_3rdPartyApptURL_Status',$_SESSION['settings']['company_id']);
	?>
	<div class="d_InlineBlock wp98 hp100 lh2em main_bc_color1 main_color1_text center s08 mp">
		<div class="f_left wp15 hp100 no-overflow">
			<img alt="" src="common_includes/includes/images/black.gif" width="5" height="8" id="services_img" />
			<a onclick="changeData('services');" class="menu">
				OUR SERVICES
			</a>
		</div>
		<div class="f_left wp10 hp100 no-overflow">
			<img alt="" src="common_includes/includes/images/black.gif" width="5" height="8" id="about_us_img" />
			<a onclick="changeData('about_us');" class="menu">
				ABOUT US
			</a>
		</div>
		<div class="f_left wp20 hp100 no-overflow">
			<img alt="" src="common_includes/includes/images/black.gif" width="5" height="8" id="location_img" />
			<a onclick="changeData('location');" class="menu">
				LOCATIONS & HOURS
			</a>
		</div>
		<div class="f_left wp30 hp100 no-overflow">
			<img alt="" src="common_includes/includes/images/black.gif" width="5" height="8" id="packages_img" />
			<a onclick="changeData('packages');" class="menu">
				GIFT CERTIFICATES & PACKAGES
			</a>
		</div>
		<div class="f_left wp24 hp100 no-overflow">
			<img alt="" src="common_includes/includes/images/black.gif" width="5" height="8" id="appointments_img"/>
			<?
			if($Appointment_Company_3rdPartyApptURL_Status[0]->value == 1)
			{
				?>
				<a id="scheduleAnAppointment" href="<?=$Appointment_Company_3rdPartyApptURL[0]->value ?>" target="_blank" class="menu">
					SCHEDULE APPOINTMENT
				</a>
				<?
			}
			else
			{
				?>
				<a id="scheduleAnAppointment" onclick="changeData('appointments');" class="menu">
					SCHEDULE APPOINTMENT
				</a>
				<?
			} ?>
		</div>
	</div>
	<?
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
function services($IMAGE_DAL){
	$Companies_dal         = new Companies_DAL();
	$inventory_dal         = new INVENTORY_DAL();
	$link_1_img_1          = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID($_SESSION['settings']['company_id'],'link_1_img_1');
	$link_1_img_2          = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID($_SESSION['settings']['company_id'],'link_1_img_2');
	$link_1_right_column_1 = $Companies_dal->get_TemplateTabData_by_Name('link_1_right_column_1',$_SESSION['settings']['company_id']);
	?>
	<div class="d_InlineBlock wp98 hp100 main_bc_color2 main_color2_text center">
		<div class="f_left wp75 hp100 ">
			<div class="d_inlineBlock wp100 hp05">
				<div class="f_left wp100 hp100">
					&nbsp;
				</div>
			</div>
			<div class="d_inlineBlock wp100 hp10 main_bc_color1_light main_color1_light_text">
				<div class="f_left left pl10 wp45 hp100 s10 bold">
					Our Services
				</div>
			</div>
			<div class="wp100 hp85 d_InlineBlock scrolling ">
				<?
				$count                 = $total_service_count   = $current_category_id   = 0;
				$available_services    = $inventory_dal->ServiceManagement_AllActiveServices($_SESSION['settings']['company_id']);
				?>
				<div class="d_InlineBlock center wp100">
					<?
					if(count($available_services) > 0 )
					{
					foreach($available_services as $service)
					{
					if( $current_category_id != $service->category_id)
					{
					$current_category_id = $service->category_id;
					$count               = 0;
					if($total_service_count > 0 )
					{?>
				</div>
				<div class="d_InlineBlock center wp100">
					<? } ?>
					<div class="f_left left s09 wp100 mt10 main_color1_text main_bc_color1 bold">
						<?=$service->category_name?> category.
					</div>
					<? } ?>
					<div class="d_InlineBlock center wp100 ">
						<?
						if(isset($_SESSION['appointment_book']['services_selected'][$service->id]))
						{
							$chooseService_Class = ' white bclightgreen';
						}
						else
						{
							$chooseService_Class = ' black bcgrey';
						} ?>
						<div class="d_InlineBlock f_left wp98">
							<div class="d_InlineBlock box3-black wp100">
								<div class="f_left wp100 main_bc_color1_light main_color1_light_text">
									<div class="ml10 f_left wp10 s08 left no-overflow">
										<?=money2($service->price,0)?>
									</div>
									<div class="f_left wp85 button left no-overflow ml20">
										<?=$service->name?>
									</div>
								</div>
								<div class="f_left wp100 mt1 s07 h60px bclightgray text_OverFlow_ellipsis scrolling main_color1_light_text"  title="<?=$service->style?>">
									~ <?=$service->est_time_mins?> minutes. - <?=$service->style?>
								</div>
							</div>
						</div>
					</div>
					<? $count++; $total_service_count++;
					}
					}
					else
					{
						?>
						<div class="f_left left wp90 no-overflow">
							There are not any active services to choose from at this time.
						</div>
						<?
					} ?>
				</div>
			</div>
		</div>
		<div class="f_right wp24 hp100" >
			<div class="f_left wp99 h250px mt5">
				<img src="/pos/showimage.php?id=<?=$link_1_img_1[0]->image_id?>&image_db_id=<?=$link_1_img_1[0]->image_db_id?>" width="100%" height="100%" alt="">
			</div>
			<div class="f_left wp99 h150px scrolling">
				<?=$link_1_right_column_1[0]->value?>
			</div>
		</div>
	</div>
	<?php
}
function about_us($IMAGE_DAL){
	$Companies_dal                 = new Companies_DAL();
	$Main_About_us_Paragraph_Title = $Companies_dal->get_TemplateTabData_by_Name('Main_About_us_Paragraph_Title',$_SESSION['settings']['company_id']);
	$Main_About_us_Paragraph       = $Companies_dal->get_TemplateTabData_by_Name('Main_About_us_Paragraph',$_SESSION['settings']['company_id']);
	$link_2_img_1                  = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID($_SESSION['settings']['company_id'],'link_2_img_1');
	$link_2_img_2                  = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID($_SESSION['settings']['company_id'],'link_2_img_2');
	$link_2_right_column_1         = $Companies_dal->get_TemplateTabData_by_Name('link_2_right_column_1',$_SESSION['settings']['company_id']);
	?>
	<div class="d_InlineBlock wp98 hp100 main_bc_color2 main_color2_text center">
		<div class="f_left wp75 hp100 ">
			<div class="d_inlineBlock wp100 hp05">
				<div class="f_left wp100 hp100">
					&nbsp;
				</div>
			</div>
			<div class="d_inlineBlock wp100 hp10 main_bc_color1_light main_color1_light_text">
				<div class="f_left left pl10 wp45 hp100 s10 bold">
					About Us
				</div>
			</div>
			<div class="wp100 hp85 d_InlineBlock scrolling ">
				<?=$Main_About_us_Paragraph_Title[0]->value?>
				<?=$Main_About_us_Paragraph[0]->value?>
			</div>
		</div>
		<div class="f_right wp24 hp100" >
			<div class="f_left wp99 h250px mt5">
				<img src="/pos/showimage.php?id=<?=$link_2_img_1[0]->image_id?>&image_db_id=<?=$link_2_img_1[0]->image_db_id?>" width="100%" height="100%" alt="">
			</div>
			<div class="f_left wp99 h150px scrolling mt5">
				<?=$link_2_right_column_1[0]->value?>
			</div>
		</div>
	</div>
	<?php
}
function location($IMAGE_DAL){
	$Companies_dal         = new Companies_DAL();
	$preferences_dal       = new Preferences_DAL();
	$link_3_img_1          = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID($_SESSION['settings']['company_id'],'link_3_img_1');
	$link_3_img_2          = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID($_SESSION['settings']['company_id'],'link_3_img_2');
	$link_3_right_column_1 = $Companies_dal->get_TemplateTabData_by_Name('link_3_right_column_1',$_SESSION['settings']['company_id']);
	$company_addresses     = $preferences_dal->get_addresses_per_company_id($_SESSION['settings']['company_id']);
	?>
	<div class="d_InlineBlock wp98 hp100 main_bc_color2 main_color2_text center">
		<div class="f_left wp75 hp100 ">
			<div class="d_inlineBlock wp100 hp05">
				<div class="f_left wp100 hp100">
					&nbsp;
				</div>
			</div>
			<div class="d_InlineBlock wp100 hp10 main_bc_color1_light main_color1_light_text">
				<div class="f_left left pl10 wp45 hp100 s10 bold">
					Locations and Hours
				</div>
			</div>
			<div class="d_InlineBlock wp100 hp85 ">
				<div class="f_left wp100 hp60 scrolling">
					<?=$company_addresses[0]->google_map_url?>
				</div>
				<div class="f_left wp100 hp40">
					<? make_appointment_step1_business_hours($_SESSION['settings']['company_id'])?>
				</div>
			</div>
		</div>
		<div class="d_inlineBlock wp24 hp100 scrolling" >
			<div class="f_left wp99 h250px  mt5">
				<img src="/pos/showimage.php?id=<?=$link_3_img_1[0]->image_id?>&image_db_id=<?=$link_3_img_1[0]->image_db_id?>" width="100%" height="100%" alt="">
			</div>
			<div class="f_left wp99 hp20 h150px scrolling mt5">
				<?=$link_3_right_column_1[0]->value?>
			</div>
			<div class="f_left wp99 h250px mt5">
				<img src="/pos/showimage.php?id=<?=$link_3_img_1[0]->image_id?>&image_db_id=<?=$link_3_img_1[0]->image_db_id?>" width="100%" height="100%" alt="">
			</div>
		</div>
	</div>
	<?php
}
function packages($IMAGE_DAL){
	$Companies_dal         = new Companies_DAL();
	$link_4_img_1          = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID($_SESSION['settings']['company_id'],'link_4_img_1');
	$link_4_img_2          = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID($_SESSION['settings']['company_id'],'link_4_img_2');
	$link_4_right_column_1 = $Companies_dal->get_TemplateTabData_by_Name('link_4_right_column_1',$_SESSION['settings']['company_id']);
	?>
	<div class="d_InlineBlock wp98 hp100 main_bc_color2 main_color2_text center">
		<div class="f_left wp75 hp100 ">
			<div class="d_inlineBlock wp100 hp05">
				<div class="f_left wp100 hp100">
					&nbsp;
				</div>
			</div>
			<div class="d_inlineBlock wp100 hp10 main_bc_color1_light main_color1_light_text">
				<div class="f_left left pl10 wp55 hp100 s10 bold">
					Gift Certificates and Packages
				</div>
			</div>
			<div class="wp100 hp85 d_InlineBlock scrolling ">
				<h2>
					<img src="common_includes/includes/images/under_construction.jpg" width="160" height="149">
				</h2>
			</div>
		</div>
		<div class="f_right wp24 hp100 scrolling" >
			<div class="f_left wp99 h250px mt5">
				<img src="/pos/showimage.php?id=<?=$link_4_img_1[0]->image_id?>&image_db_id=<?=$link_4_img_1[0]->image_db_id?>" width="100%" height="100%" alt="">
			</div>
			<div class="f_left wp99 h150px scrolling mt5">
				<?=$link_4_right_column_1[0]->value?>
			</div>
		</div>
	</div>
	<?php
}

function massage_1_template(){
	global $version;
	$IMAGE_DAL        = new IMAGE_DATA_DAL();
	$general_dal      = new GENERAL_DAL();
	$company_name     = $general_dal->get_Company_Preference($_SESSION['settings']['company_id'],'company name');
	$meta_description = $general_dal->get_Company_Preference($_SESSION['settings']['company_id'],'meta_description');
	$meta_keywords    = $general_dal->get_Company_Preference($_SESSION['settings']['company_id'],'meta_keywords');
	?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html>
		<?=default_header();?>
		<body class="body_font main_bc_color1 m0 center">
			<div class="w800 d_InlineBlock center main_bc_color1">
				<!--Login Header-->
				<div id="login_horizontal_div"  class="wp100 h30px  d_InlineBlock main_bc_color2 main_color2_text mt5">
					<?php login_horizontal()?>
				</div>
				<!-- HEADER -->
				<div id="header_div"            class="wp100 h100px d_InlineBlock main_bc_color2 main_color2_text">
					<?php header_1($IMAGE_DAL)?>
				</div>
				<!--MAIN MENU-->
				<div id="mainmenu_div"          class="wp100 h30px  d_InlineBlock main_bc_color2 main_color2_text">
					<?php mainmenu()?>
				</div>
				<!--BODY-->
				<div id="body_div"              class="wp100 h500px d_InlineBlock main_bc_color2 main_color2_text">
					<?
					if(isset($_GET['page_load']) && $_GET['page_load'] == 'register')
					{
						?>
						<div class="wp50 d_InlineBlock center m5 main_bc_color1 main_color1_text">
							<?make_appointment_step3_RIGHT_NEWUSER('register_only');?>
						</div>
						<?
					}
					else
					{
						?>
						<?php body_2($IMAGE_DAL)?>
						<?
					} ?>
				</div>
				<!--FOOTER-->
				<div id="footer_div"            class="wp100 h25px d_InlineBlock main_bc_color2 main_color2_text">
					<?php footer_1($IMAGE_DAL)?>
				</div>
				<!--COPYRIGHT-->
				<div id="copyright_div"         class="wp100 h10px  d_InlineBlock main_bc_color1 main_color2_text mb5">
					<?php copyright($IMAGE_DAL)?>
				</div>
				<?php loading_div()?>
			</div>
		</body>
	</html>
	<?
}
function header_1($IMAGE_DAL){
	$main_company_logo = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID($_SESSION['settings']['company_id'],'main_company_logo');
	?>
	<div class="d_InlineBlock wp100 hp100 main_bc_color2">
		<div class="f_left hp100 wp02">
			&nbsp;
		</div>
		<div class="f_left hp100 wp20">
			<a href="/">
				<img class='wp100 hp95 b0 <?
				if($main_company_logo[0]->image_id > 0)
				{
					print ' mp';
				} ?>' src='/pos/showimage.php?id=<?=$main_company_logo[0]->image_id?>&image_db_id=<?=$main_company_logo[0]->image_db_id?>'  <?
				if($main_company_logo[0]->image_id > 0)
				{
					?> <?
				} ?>  />
			</a>
		</div>
		<div class="f_left hp100 wp55">
			&nbsp;
		</div>
		<div class="f_left right wp20 hp100 ">
			&nbsp;
		</div>
		<div class="f_left hp100 wp02">
			&nbsp;
		</div>
	</div>
	<?php
}
function body_1($IMAGE_DAL){
	$main_page_img_1 = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID($_SESSION['settings']['company_id'],'main_page_img_1');
	$main_page_img_2 = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID($_SESSION['settings']['company_id'],'main_page_img_2');
	$main_page_img_3 = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID($_SESSION['settings']['company_id'],'main_page_img_3');
	?>
	<div class="wp98 hp100 d_InlineBlock main_bc_color2 center">
		<div class="f_left  hp100 wp32">
			<img class='wp100 hp100 m0 <?
			if($main_page_img_1[0]->image_id > 0)
			{
				print ' mp';
			} ?>'   src='/pos/showimage.php?id=<?=$main_page_img_1[0]->image_id?>&image_db_id=<?=$main_page_img_1[0]->image_db_id?>'  <?
			if($main_page_img_1[0]->image_id > 0)
			{
				?> <?
			} ?>  />
		</div>
		<div class="f_left hp100 wp02">
			&nbsp;
		</div>
		<div class="f_left hp100 wp31">
			<img class='wp100 hp100 m0 <?
			if($main_page_img_2[0]->image_id > 0)
			{
				print ' mp';
			} ?>'   src='/pos/showimage.php?id=<?=$main_page_img_2[0]->image_id?>&image_db_id=<?=$main_page_img_2[0]->image_db_id?>'  <?
			if($main_page_img_2[0]->image_id > 0)
			{
				?> <?
			} ?>  />
		</div>
		<div class="f_left hp100 wp02">
			&nbsp;
		</div>
		<div class="f_left hp100 wp33 right">
			<img class='wp100 hp100 m0 <?
			if($main_page_img_3[0]->image_id > 0)
			{
				print ' mp';
			} ?>'   src='/pos/showimage.php?id=<?=$main_page_img_3[0]->image_id?>&image_db_id=<?=$main_page_img_3[0]->image_db_id?>'  <?
			if($main_page_img_3[0]->image_id > 0)
			{
				?> <?
			} ?>  />
		</div>
	</div>
	<?
}
function footer_1($IMAGE_DAL){
	$Companies_dal = new Companies_DAL();
	?>
	<div class="d_InlineBlock wp98 main_bc_color2 main_color2_text mb5">
		<div class="wp20 f_left">
			&nbsp;
		</div>
		<div class="wp80 f_right right main_color2_text">
			&nbsp;  Phone:  &nbsp;
			<font class="phone">
				<?=$_SESSION['company_info'][$_SESSION['settings']['company_id']]['Phone_Number_Main']?>
			</font>
		</div>
	</div>
	<?php
}
function copyright($IMAGE_DL){
	$general_dal   = new GENERAL_DAL();
	$Companies_dal = new Companies_DAL();
	$company_name  = $general_dal->get_Company_Preference($_SESSION['settings']['company_id'],'company name');
	?>
	<table width="784" cellpadding="0" cellspacing="0" border="0" align="center">
		<tr>
			<td class="copy main_color1_text">
				Copyright <? echo date("Y")?> <?=$company_name[0]->value?>
			</td>
		</tr>
	</table>
	<?php
}

function massage_2_template(){
	$general_dal      = new GENERAL_DAL();
	$IMAGE_DAL        = new IMAGE_DATA_DAL();
	$company_name     = $general_dal->get_Company_Preference($_SESSION['settings']['company_id'],'company name');
	$meta_description = $general_dal->get_Company_Preference($_SESSION['settings']['company_id'],'meta_description');
	$meta_keywords    = $general_dal->get_Company_Preference($_SESSION['settings']['company_id'],'meta_keywords');
	?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html>
		<?=default_header();?>
		<body class="main_bc_color1 m0 center">
			<div id="fb-root">
			</div>
			<script>
				(function(d, s, id)
					{
						var js, fjs = d.getElementsByTagName(s)[0];
						if (d.getElementById(id)) return;
						js = d.createElement(s); js.id = id;
						js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
						fjs.parentNode.insertBefore(js, fjs);
					}(document, 'script', 'facebook-jssdk'));
			</script>
			<div class="d_InlineBlock w884 main_bc_color1">
				<!--HEADER-->
				<div id="login_horizontal_div"  class="wp100 h30px  d_InlineBlock main_bc_color2 main_color2_text mt5">
					<?php login_horizontal()?>
				</div>
				<div id="header_div"            class="wp100 h160px d_InlineBlock main_bc_color2 main_color2_text">
					<?php header_2($IMAGE_DAL)?>
				</div>
				<!--MAIN MENU-->
				<div id="mainmenu_div"          class="wp100 h30px  d_InlineBlock main_bc_color2 main_color2_text">
					<?php mainmenu()?>
				</div>
				<!--BODY-->
				<div id="body_div"              class="wp100 h500px d_InlineBlock main_bc_color2 main_color2_text">
					<?
					if(isset($_GET['page_load']) && $_GET['page_load'] == 'register')
					{
						?>
						<div class="wp50 d_InlineBlock center m5 main_bc_color1 main_color1_text">
							<?make_appointment_step3_RIGHT_NEWUSER('register_only');?>
						</div>
						<?
					}
					else
					{
						?>
						<?php body_2($IMAGE_DAL)?>
						<?
					} ?>
				</div>
				<div id="footer_div"            class="wp100 h100px  d_InlineBlock main_bc_color2 main_color2_text mb5">
					<?php footer_2($IMAGE_DAL)?>
				</div>
				<?php loading_div()?>
			</div>
		</body>
	</html>
	<?
}
function header_2($IMAGE_DAL){
	$main_company_logo = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID($_SESSION['settings']['company_id'],'main_company_logo');
	?>
	<div class="d_InlineBlock center wp100 hp100">
		<div class="d_InlineBlock  wp99 hp100 center">
			<a href="/">
				<img class='wp100 hp100 m0 b0 <?
				if($main_company_logo[0]->image_id > 0)
				{
					print ' mp';
				} ?>' src='/pos/showimage.php?id=<?=$main_company_logo[0]->image_id?>&image_db_id=<?=$main_company_logo[0]->image_db_id?>'  <?
				if($main_company_logo[0]->image_id > 0)
				{
					?> <?
				} ?>  />
			</a>
		</div>
	</div>
	<?
}
function body_2($IMAGE_DAL){
	$main_page_img_1          = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID($_SESSION['settings']['company_id'],'main_page_img_1');
	$main_page_img_1_rotating = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID_rotating($_SESSION['settings']['company_id'],'main_page_img_1');
	if(count($main_page_img_1_rotating) > 0)
	{
		$rotating_item_css_img_1 = 'rotating-item_img1';
	}
	else
	{
		$rotating_item_css_img_1 = '';
	}

	$main_page_img_2          = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID($_SESSION['settings']['company_id'],'main_page_img_2');
	$main_page_img_2_rotating = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID_rotating($_SESSION['settings']['company_id'],'main_page_img_2');
	if(count($main_page_img_2_rotating) > 0)
	{
		$rotating_item_css_img_2 = 'rotating-item_img2';
	}
	else
	{
		$rotating_item_css_img_2 = '';
	}

	$main_page_img_3          = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID($_SESSION['settings']['company_id'],'main_page_img_3');
	$main_page_img_3_rotating = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID_rotating($_SESSION['settings']['company_id'],'main_page_img_3');
	if(count($main_page_img_3_rotating) > 0)
	{
		$rotating_item_css_img_3 = 'rotating-item_img3';
	}
	else
	{
		$rotating_item_css_img_3 = '';
	}

	?>
	<div class="wp98 hp98 d_InlineBlock main_bc_color2 t_align_center">
		<div class="f_left hp100 wp32 p_relative">
			<img style="display:block;" class='wp100 hp100 m0 <?=$rotating_item_css_img_1?> <?
			if($main_page_img_1[0]->image_id > 0)
			{
				print ' mp';
			} ?>' src='/pos/showimage.php?id=<?=$main_page_img_1[0]->image_id?>&image_db_id=<?=$main_page_img_1[0]->image_db_id?>'  <?
			if($main_page_img_1[0]->image_id > 0)
			{
				?> <?
			} ?>  />
			<?
			foreach($main_page_img_1_rotating as $main_page_img_1_rotating_iteration)
			{
				?>
				<img class='wp100 hp100 m0 <?=$rotating_item_css_img_1?> <?
				if($main_page_img_1_rotating_iteration->image_id > 0)
				{
					print ' mp';
				} ?>' src='/pos/showimage.php?id=<?=$main_page_img_1_rotating_iteration->image_id?>&image_db_id=<?=$main_page_img_1_rotating_iteration->image_db_id?>'  <?
				if($main_page_img_1_rotating_iteration->image_id > 0)
				{
					?> <?
				} ?>  />
				<?
			}?>
			<script type="text/javascript"              src="/pos/includes/image_rotator/infinite-rotator_img1.js">
			</script>
		</div>
		<div class="f_left hp100 wp02">
			&nbsp;
		</div>
		<div class="f_left hp100 wp31 p_relative">
			<img style="display:block;" class='wp100 hp100 m0 <?=$rotating_item_css_img_2?> <?
			if($main_page_img_2[0]->image_id > 0)
			{
				print ' mp';
			} ?>' src='/pos/showimage.php?id=<?=$main_page_img_2[0]->image_id?>&image_db_id=<?=$main_page_img_2[0]->image_db_id?>'  <?
			if($main_page_img_2[0]->image_id > 0)
			{
				?> <?
			} ?>  />
			<?
			foreach($main_page_img_2_rotating as $main_page_img_2_rotating_iteration)
			{
				?>
				<img class='wp100 hp100 m0 <?=$rotating_item_css_img_2?> <?
				if($main_page_img_2_rotating_iteration->image_id > 0)
				{
					print ' mp';
				} ?>' src='/pos/showimage.php?id=<?=$main_page_img_2_rotating_iteration->image_id?>&image_db_id=<?=$main_page_img_2_rotating_iteration->image_db_id?>'  <?
				if($main_page_img_2_rotating_iteration->image_id > 0)
				{
					?> <?
				} ?>  />
				<?
			}?>
			<script type="text/javascript"              src="/pos/includes/image_rotator/infinite-rotator_img2.js">
			</script>
		</div>
		<div class="f_left hp100 wp02">
			&nbsp;
		</div>
		<div class="f_left hp100 wp33 p_relative right">
			<img style="display:block;" class='wp100 hp100 m0 <?=$rotating_item_css_img_3?> <?
			if($main_page_img_3[0]->image_id > 0)
			{
				print ' mp';
			} ?>' src='/pos/showimage.php?id=<?=$main_page_img_3[0]->image_id?>&image_db_id=<?=$main_page_img_3[0]->image_db_id?>'  <?
			if($main_page_img_3[0]->image_id > 0)
			{
				?> <?
			} ?>  />
			<?
			foreach($main_page_img_3_rotating as $main_page_img_3_rotating_iteration)
			{
				?>
				<img class='wp100 hp100 m0 <?=$rotating_item_css_img_3?> <?
				if($main_page_img_3_rotating_iteration->image_id > 0)
				{
					print ' mp';
				} ?>' src='/pos/showimage.php?id=<?=$main_page_img_3_rotating_iteration->image_id?>&image_db_id=<?=$main_page_img_3_rotating_iteration->image_db_id?>'  <?
				if($main_page_img_3_rotating_iteration->image_id > 0)
				{
					?> <?
				} ?>  />
				<?
			}?>
			<script type="text/javascript"              src="/pos/includes/image_rotator/infinite-rotator_img3.js">
			</script>
		</div>
	</div>
	<?
}
function footer_2($IMAGE_DAL){
	$Companies_dal        = new Companies_DAL();
	$general_dal          = new GENERAL_DAL();
	$company_name         = $general_dal->get_Company_Preference($_SESSION['settings']['company_id'],'company name');
	$PHYSICAL_ADDRESS     = $Companies_dal->get_TemplateTabData_by_Name('PHYSICAL_ADDRESS',$_SESSION['settings']['company_id']);
	$Phone_Number_Main    = $Companies_dal->get_TemplateTabData_by_Name('Phone_Number_Main',$_SESSION['settings']['company_id']);
	$Phone_Number_2       = $Companies_dal->get_TemplateTabData_by_Name('Phone_Number_2',$_SESSION['settings']['company_id']);

	$Company_Footer_img_1 = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID($_SESSION['settings']['company_id'],'Company_Footer_img_1');
	$Company_Footer_text_1= $Companies_dal->get_TemplateTabData_by_Name('Company_Footer_text_1',$_SESSION['settings']['company_id']);
	$Company_Footer_img_2 = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID($_SESSION['settings']['company_id'],'Company_Footer_img_2');
	$Company_Footer_text_2= $Companies_dal->get_TemplateTabData_by_Name('Company_Footer_text_2',$_SESSION['settings']['company_id']);

	$COMPANY_Facebook_Link= $Companies_dal->get_TemplateTabData_by_Name('COMPANY_Facebook_Link',$_SESSION['settings']['company_id']);
	?>
	<div class="d_InlineBlock wp98 hp97 main_bc_color1 main_color1_text">
		<div class="f_left wp30 hp100 no-overflow">
			<a href="http://www.abmp.com/home/" target="_blank">
				<div class="f_left wp100 hp75">
					<img class='wp100 hp100 m0 <?
					if($Company_Footer_img_1[0]->image_id > 0)
					{
						print ' mp';
					} ?>' src='/pos/showimage.php?id=<?=$Company_Footer_img_1[0]->image_id?>&image_db_id=<?=$Company_Footer_img_1[0]->image_db_id?>'  <?
					if($Company_Footer_img_1[0]->image_id > 0)
					{
						?> <?
					} ?>  />
				</div>
				<div class="f_left wp100 hp25 s06">
					<?=$Company_Footer_text_1[0]->value?>
				</div>
			</a>
		</div>
		<div class="f_left wp40 hp100 no-overflow">
			<div class="f_left wp100 hp15 main_color1_text">
				<?=$company_name[0]->value?><br>
			</div>
			<div class="f_left wp100 hp35 main_color1_text">
				<?=$PHYSICAL_ADDRESS[0]->value?><br>
			</div>
			<div class="f_left wp100 hp20 main_color1_text">
				<font size="2">
					Phone:
				</font>
				<font size="3">
					<?=$Phone_Number_Main[0]->value?>
				</font>
			</div>
			<div class="f_left wp100 hp30 main_color1_text center vtop">
				<a href='<?=$COMPANY_Facebook_Link[0]->value?>' style="text-decoration:none; cursor:pointer;">
					<div class="f_left left wp50 hp100 vtop">
						Follow us on <img class='w20px h20px' src="/common_includes/includes/images/facebook_icon.png">
					</div>
					<div class="f_left right wp50 hp100 vtop">
						<a href="mailto:gtmassage620@hotmail.com?subjegloriact=I'm Interested In GT Massage and Skin Care">
							Email Us
						</a>
					</div>
				</a>
			</div>
		</div>
		<div class="f_left wp30 hp100 no-overflow">
			<a href="http://www.abmp.com/home/" target="_blank">
				<div class="f_left wp100 hp75">
					<img class='wp100 hp100 m0 <?
					if($Company_Footer_img_2[0]->image_id > 0)
					{
						print ' mp';
					} ?>' src='/pos/showimage.php?id=<?=$Company_Footer_img_2[0]->image_id?>&image_db_id=<?=$Company_Footer_img_2[0]->image_db_id?>'  <?
					if($Company_Footer_img_2[0]->image_id > 0)
					{
						?> <?
					} ?>  />
				</div>
				<div class="f_left wp100 hp25 s06">
					<?
					if(count($Company_Footer_text_2 > 0))
					{
						?>
						<?=$Company_Footer_text_2[0]->value?>
						<?
					}
					else
					{
						?>
						&nbsp;
						<?
					} ?>

				</div>
			</a>
		</div>
	</div>
	<?
}

function massage_3_template(){
	$general_dal      = new GENERAL_DAL();
	$IMAGE_DAL        = new IMAGE_DATA_DAL();
	$company_name     = $general_dal->get_Company_Preference($_SESSION['settings']['company_id'],'company name');
	$meta_description = $general_dal->get_Company_Preference($_SESSION['settings']['company_id'],'meta_description');
	$meta_keywords    = $general_dal->get_Company_Preference($_SESSION['settings']['company_id'],'meta_keywords');
	?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html>
		<?=default_header();?>
		<body class="main_bc_color1 m0 center">
			<div class="d_InlineBlock w700 main_bc_color1">
				<!--HederEx-->
				<div id="headerex" class="headerex">
				</div>
				<!--HEADER-->
				<div id="header_div"    class="wp100 d_InlineBlock main_bc_color2">
					<?php header_3()?>
				</div>
				<!--MAIN MENU-->
				<div id="mainmenu_div"  class="wp100 h30px d_InlineBlock main_bc_color2">
					<?php mainmenu()?>
				</div>
				<!--BODY-->
				<div id="body_div"      class="wp100 d_InlineBlock main_bc_color2">
					<?
					if(isset($_GET['page_load']) && $_GET['page_load'] == 'register')
					{
						?>
						<div class="wp50 d_InlineBlock center m5 main_bc_color1 main_color1_text">
							<?make_appointment_step3_RIGHT_NEWUSER('register_only');?>
						</div>
						<?
					}
					else
					{
						?>
						<?php body_2($IMAGE_DAL)?>
						<?
					} ?>
				</div>
				<div id="footer_div"    class="wp100 d_InlineBlock main_bc_color2">
					<?php footer_3()?>
				</div>
				<?php loading_div()?>
			</div>
		</body>
	</html>
	<?
}
function header_3($IMAGE_DAL){
	?>
	<div class="d_InlineBlock wp100">
		<img border="0" src="/common_includes/includes/images/belmontHeader.png">
	</div>
	<?
}
function body_3($IMAGE_DAL){
	?>
	<div class="wp98 h200px d_InlineBlock mb100">
		<div class="f_left wp50 white">
			<span style="color:#ffffff; line-height:11px; font-size:12px;">
				<h2 style="text-align: center; color:white;">
					<strong>
						CUTS $17 SHAVES $33
					</strong>
				</h2>
				<h3 style="text-align: center; color:white;">
					<strong>
						773-296-0894
					</strong>
				</h3>
				<h3 style="text-align: center; color:white;">
					<span style="text-decoration: underline; color:white;">
						<strong>
							APPOINTMENT HOURS<br>
						</strong>
					</span>
					<p class="white">
						<strong>
							Tuesday through Friday 8 - 7
						</strong>
					</p>
					<p class="white">
						<strong>
							Saturday 8 - 3
						</strong>
					</p>
					<p class="white">
						<strong>
							<span style="text-decoration: underline;">
								WALK IN HOURS
							</span>
						</strong>
					</p>
					<p class="white">
						<strong>
							Tuesday through Friday 9-5
						</strong>
					</p>
					<p class="white">
						<strong>
							Saturday 8-2<br>
						</strong>
					</p>
				</h3>
			</span>
		</div>
		<div class="f_left wp50 mt15">
			<img height="209" width="314" alt="" src="/common_includes/includes/images/BelmontBarbershopMainImage.jpg">
		</div>
	</div>
	<?
}
function footer_3($IMAGE_DAL){
	?>
	<div class="d_InlineBlock wp98 main_bc_color2 mb5">

		<div class="bcred footertop">
			&nbsp;
		</div>
		<div class="main_bc_color2">
			<div class="d_InlineBlock wp100 m0 center white">
				<a href="home">
					Home
				</a>
				<a href="news">
					News
				</a>
				<a href="about">
					About
				</a>
				<a href="barbers">
					Barbers
				</a>
				<a href="contact">
					Contact
				</a>
				<a href="links">
					Links
				</a>
			</div>
			<div class="d_InlineBlock f_left wp100 left white">
				Follow us on your favorite social network!
			</div>
			<div class="wp100 d_InlineBlock">
				<div class="f_left wp05">
					&nbsp;
				</div>
				<div class="f_left wp06">
					<a target="_blank" href="http://www.myspace.com/belmontbarbershop" class="soc">
						<p>
							<img width="60%" border="0" src="http://www.dubbsenterprises.com/wp-content/themes/bbs/_images/myspace.jpg"><br>
							<span class="white">
								MySpace
							</span>
						</p>
					</a>
				</div>
				<div class="f_left wp06">
					<a target="_blank" href="http://twitter.com/belmontbarber" class="soc">
						<p>
							<img width="60%" border="0" src="http://www.dubbsenterprises.com/wp-content/themes/bbs/_images/twitter.jpg"><br>
							<span class="white">
								Twitter
							</span>
						</p>
					</a>
				</div>
				<div class="f_left wp06">
					<a target="_blank" ref="ts&amp;ajaxpipe=1&amp;__a=5" href="http://www.facebook.com/home.php?#!/pages/Chicago-IL/The-Belmont-Barbershop-Ltd/61271439545?" class="soc">
						<p>
							<img width="60%" border="0" src="http://www.dubbsenterprises.com/wp-content/themes/bbs/_images/facebook.jpg"><br>
							<span class="white">
								Facebook
							</span>
						</p>
					</a>
				</div>
				<div class="f_left wp06">
					<a rel="superbox[iframe][480x520]" href="email-us" class="soc">
						<p>
							<img width="60%" border="0" src="http://www.dubbsenterprises.com/wp-content/themes/bbs/_images/email.jpg"><br>
							<span class="white">
								Email us
							</span>
						</p>
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
	<?
}

function massage_4_template(){
	$general_dal = new GENERAL_DAL();
	$IMAGE_DAL   = new IMAGE_DATA_DAL();
	?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html>
		<?=default_header();?>
		<?loading_div()?>
		<body class="h800px w800 main_bc_color1 mauto">
			<div class="wp95 hp100 main_bc_color1 mauto">
				<div class="d_InlineBlock wp100 hp07">
					<div class="f_left d_InlineBlock hp100 wp33 bclightgray">
						<a href="/">
							DubbsPOS - Point of Sale Software
						</a>
					</div>
					<div class="f_left d_InlineBlock hp100 wp02">
						&nbsp;
					</div>
					<div class="f_left d_InlineBlock hp100 wp40 s07 bclightgray">
						<div class="f_left hp100 wp20">
							<a class="signup" href="/signup">
								Get Started
							</a>
						</div>
						<div class="f_left hp100 wp20">
							<a href="/retaileasy">
								Retail. Easy.
							</a>
						</div>
						<div class="f_left hp100 wp20">
							<a href="/pricing">
								Pricing
							</a>
						</div>
						<div class="f_left hp100 wp20">
							<a href="/customers">
								Customers
							</a>
						</div>
						<div class="f_left hp100 wp20">
							<a href="/tour">
								Take a Tour
							</a>
						</div>
					</div>
					<div class="f_left d_InlineBlock hp100 wp02">
						&nbsp;
					</div>
					<div class="f_left d_InlineBlock hp100 wp23 bclightgray">
						<div class="f_left hp100 wp30">
							<a href="/blog">
								Blog
							</a>
						</div>
						<div class="f_left hp100 wp02">
							/
						</div>
						<div class="f_left hp100 wp30">
							<a href="/help">
								Help
							</a>
						</div>
						<div class="f_left hp100 wp02">
							/
						</div>
						<div class="f_left hp100 wp30">
							<a class="login" href="https://shop.dubbsenterprises.com">
								Login
							</a>
						</div>
					</div>
				</div>
				<div class="d_InlineBlock wp100 hp45 t_align_center bclightyellow">
					BIG OLE PIC
				</div>
				<div class="d_InlineBlock wp100 hp10 t_align_center bclightblue">
					<div class="f_left wp15 hp100">
						&nbsp;
					</div>
					<div class="f_left wp35 hp100">
						<label for="admin_email">
							Email Address
						</label>
						<input type="email" required="required" name="admin_email" label="Email Address" placeholder="Email Address" size="32">
						</input>
					</div>
					<div div class="f_left wp20 hp100">
						<label for="admin_password">
							Password
						</label>
						<input type="password" label="Password" minlength="6" required="required" autocomplete="off" name="admin_password" placeholder="Password" size="12">
						</input>
					</div>
					<div div class="f_left wp15 hp100">
						<input type="submit" value="Create my account">
						</input>
					</div>
					<div class="f_left wp15 hp100">
						&nbsp;
					</div>
					<input type="hidden" name="sys_type" value="small">
					</input>
					<input type="hidden" name="timestamp" value="1340187497">
					</input>
					<input type="hidden" name="token" value="afd213cccd36c3f106661c6ed43fd68c">
					</input>
				</div>
				<div class="d_InlineBlock wp100 hp20 t_align_center bclightgray">
					<?=footer_4($IMAGE_DAL)?>
				</div>
			</div>
		</body>
	</html>
	<?
}
function header_4($IMAGE_DAL){
	?>
	<div class="d_InlineBlock wp100">
		<img border="0" src="/common_includes/includes/images/belmontHeader.png">
	</div>
	<?
}
function body_4($IMAGE_DAL){
	?>
	<div class="wp98 h200px d_InlineBlock mb100">
		<div class="f_left wp50 white">
			<span style="color:#ffffff; line-height:11px; font-size:12px;">
				<h2 style="text-align: center; color:white;">
					<strong>
						CUTS $17 SHAVES $33
					</strong>
				</h2>
				<h3 style="text-align: center; color:white;">
					<strong>
						773-296-0894
					</strong>
				</h3>
				<h3 style="text-align: center; color:white;">
					<span style="text-decoration: underline; color:white;">
						<strong>
							APPOINTMENT HOURS<br>
						</strong>
					</span>
					<p class="white">
						<strong>
							Tuesday through Friday 8 - 7
						</strong>
					</p>
					<p class="white">
						<strong>
							Saturday 8 - 3
						</strong>
					</p>
					<p class="white">
						<strong>
							<span style="text-decoration: underline;">
								WALK IN HOURS
							</span>
						</strong>
					</p>
					<p class="white">
						<strong>
							Tuesday through Friday 9-5
						</strong>
					</p>
					<p class="white">
						<strong>
							Saturday 8-2<br>
						</strong>
					</p>
				</h3>
			</span>
		</div>
		<div class="f_left wp50 mt15">
			<img height="209" width="314" alt="" src="/common_includes/includes/images/BelmontBarbershopMainImage.jpg"/>
		</div>
	</div>
	<?
}
function footer_4($IMAGE_DAL){
	?>
	<div class="f_left d_InlineBlock wp100 hp100 s07 pt5">
		<ul class="wp20 f_left left pl10" style="list-style-type:none;">
			<li class="lh20" >
				<strong>
					Quisque sit
				</strong>
			</li>
			<li class="lh20">
				<a href="http://lizspetshop.com/about-us">
					Suspendisse
				</a>
			</li>
			<li class="lh20">
				<a href="http://lizspetshop.com/about-us">
					Curabitur vulputate
				</a>
			</li>
			<li class="lh20">
				<a href="http://lizspetshop.com/about-us">
					Mauris vestibulum
				</a>
			</li>
			<li class="lh20">
				<a href="http://lizspetshop.com/about-us">
					Vestibulum hendrerit
				</a>
			</li>
			<li class="lh20">
				<a href="http://lizspetshop.com/about-us">
					Sed scelerisque
				</a>
			</li>
		</ul>
		<ul class="wp20 f_left left pl10" style="list-style-type:none;">
			<li class="lh20">
				<strong>
					Suspendisse potenti
				</strong>
			</li>
			<li class="lh20">
				<a href="http://lizspetshop.com/about-us">
					Mauris vestibulum
				</a>
			</li>
			<li class="lh20">
				<a href="http://lizspetshop.com/about-us">
					Class aptent
				</a>
			</li>
			<li class="lh20">
				<a href="http://lizspetshop.com/about-us">
					Morbi ut
				</a>
			</li>
			<li class="lh20">
				<a href="http://lizspetshop.com/about-us">
					Proin interdum
				</a>
			</li>
			<li class="lh20">
				<a href="http://lizspetshop.com/about-us">
					Vestibulum diam
				</a>
			</li>
		</ul>
		<ul class="wp20 f_left left pl10" style="list-style-type:none;">
			<li class="lh20">
				<strong>
					Aenean non
				</strong>
			</li>
			<li class="lh20">
				<a href="http://lizspetshop.com/about-us">
					Suspendisse
				</a>
			</li>
			<li class="lh20">
				<a href="http://lizspetshop.com/about-us">
					Curabitur vulputate
				</a>
			</li>
			<li class="lh20">
				<a href="http://lizspetshop.com/about-us">
					Mauris vestibulum
				</a>
			</li>
			<li class="lh20">
				<a href="http://lizspetshop.com/about-us">
					Vestibulum hendrerit
				</a>
			</li>
			<li class="lh20">
				<a href="http://lizspetshop.com/about-us">
					Sed scelerisque
				</a>
			</li>
		</ul>
		<ul class="wp20 f_left left pl10" style="list-style-type:none;">
			<li class="lh20">
				<strong>
					Nullam sollicitudin
				</strong>
			</li>
			<li class="lh20">
				<a href="http://lizspetshop.com/about-us">
					Mauris vestibulum
				</a>
			</li>
			<li class="lh20">
				<a href="http://lizspetshop.com/about-us">
					Class aptent
				</a>
			</li>
			<li class="lh20">
				<a href="http://lizspetshop.com/about-us">
					Morbi ut
				</a>
			</li>
			<li class="lh20">
				<a href="http://lizspetshop.com/about-us">
					Proin interdum
				</a>
			</li>
			<li class="lh20">
				<a href="http://lizspetshop.com/about-us">
					Vestibulum diam
				</a>
			</li>
		</ul>
	</div>
	<?
}

function massage_5_template(){
	$general_dal = new GENERAL_DAL();
	$IMAGE_DAL   = new IMAGE_DATA_DAL();
	?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html>
		<?=default_header();?>
		<?loading_div()?>
		<body class="h800px w800 bc_white mauto">
			<div class="wp95 hp100 main_bc_color1 mauto">
				<div class="head_m d_InlineBlock wp100 hp20 t_align_center bclightgray">
					<?=header_5($IMAGE_DAL)?>
				</div>
				<div class="body_m d_InlineBlock wp100 hp20 t_align_center bclightgray">
					<?=body_5($IMAGE_DAL)?>
				</div>
				<div class="foot_m d_InlineBlock wp100 hp20 t_align_center bclightgray">
					<?=footer_5($IMAGE_DAL)?>
				</div>
			</div>
		</body>
	</html>
	<?
}
function header_5($IMAGE_DAL){
	$main_company_logo = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID($_SESSION['settings']['company_id'],'main_company_logo');
	?>
	<a href="/">
		<img class='wp100 hp95 b0 <?
		if($main_company_logo[0]->image_id > 0)
		{
			print ' mp';
		} ?>' src='/pos/showimage.php?id=<?=$main_company_logo[0]->image_id?>&image_db_id=<?=$main_company_logo[0]->image_db_id?>'  <?
		if($main_company_logo[0]->image_id > 0)
		{
			?> <?
		} ?>  />
	</a>
	<div class="d_InlineBlock wp100 hp07">
		<div class="f_left d_InlineBlock hp100 wp33 bclightgray">
			<a href="/">
				DubbsPOS - Point of Sale Software
			</a>
		</div>
		<div class="f_left d_InlineBlock hp100 wp02">
			&nbsp;
		</div>
		<div class="f_left d_InlineBlock hp100 wp40 s07 bclightgray">
			<div class="f_left hp100 wp20">
				<a class="signup" href="/signup">
					Get Started
				</a>
			</div>
			<div class="f_left hp100 wp20">
				<a href="/retaileasy">
					Retail. Easy.
				</a>
			</div>
			<div class="f_left hp100 wp20">
				<a href="/pricing">
					Pricing
				</a>
			</div>
			<div class="f_left hp100 wp20">
				<a href="/customers">
					Customers
				</a>
			</div>
			<div class="f_left hp100 wp20">
				<a href="/tour">
					Take a Tour
				</a>
			</div>
		</div>

		<div class="f_left d_InlineBlock hp100 wp02">
			&nbsp;
		</div>
		<div class="f_left d_InlineBlock hp100 wp23 bclightgray">
			<div class="f_left hp100 wp30">
				<a href="/blog">
					Blog
				</a>
			</div>
			<div class="f_left hp100 wp02">
				/
			</div>
			<div class="f_left hp100 wp30">
				<a href="/help">
					Help
				</a>
			</div>
			<div class="f_left hp100 wp02">
				/
			</div>
			<div class="f_left hp100 wp30">
				<a class="login" href="https://shop.dubbsenterprises.com">
					Login
				</a>
			</div>
		</div>
	</div>
	<?
}
function body_5($IMAGE_DAL){
	?>
	<div class="bop">
		BIG OLE PIC
	</div>
	<div class="u_n_login d_InlineBlock t_align_center bclightblue">
		<div class="f_left wp15 hp100">
			&nbsp;
		</div>
		
		<div class="f_left wp35 hp100">
			<label for="admin_email">
				Email Address
			</label>
			<input type="email" required="required" name="admin_email" label="Email Address" placeholder="Email Address" size="32">
			</input>
		</div>
		<div div class="f_left wp20 hp100">
			<label for="admin_password">
				Password
			</label>
			<input type="password" label="Password" minlength="6" required="required" autocomplete="off" name="admin_password" placeholder="Password" size="12">
			</input>
		</div>
		<div div class="f_left wp15 hp100">
			<input type="submit" value="Create my account">
			</input>
		</div>
		<div class="f_left wp15 hp100">
			&nbsp;
		</div>
		<input type="hidden" name="sys_type" value="small">
		</input>
		<input type="hidden" name="timestamp" value="1340187497">
		</input>
		<input type="hidden" name="token" value="afd213cccd36c3f106661c6ed43fd68c">
		</input>
	</div>
	<?
}
function footer_5($IMAGE_DAL){
	?>
	<div class="f_left d_InlineBlock wp100 hp100 s07 pt5">
		<ul class="wp20 f_left left pl10" style="list-style-type:none;">
			<li class="lh20" >
				<strong>
					Quisque sit
				</strong>
			</li>
			<li class="lh20">
				<a href="http://lizspetshop.com/about-us">
					Suspendisse
				</a>
			</li>
			<li class="lh20">
				<a href="http://lizspetshop.com/about-us">
					Curabitur vulputate
				</a>
			</li>
			<li class="lh20">
				<a href="http://lizspetshop.com/about-us">
					Mauris vestibulum
				</a>
			</li>
			<li class="lh20">
				<a href="http://lizspetshop.com/about-us">
					Vestibulum hendrerit
				</a>
			</li>
			<li class="lh20">
				<a href="http://lizspetshop.com/about-us">
					Sed scelerisque
				</a>
			</li>
		</ul>
		<ul class="wp20 f_left left pl10" style="list-style-type:none;">
			<li class="lh20">
				<strong>
					Suspendisse potenti
				</strong>
			</li>
			<li class="lh20">
				<a href="http://lizspetshop.com/about-us">
					Mauris vestibulum
				</a>
			</li>
			<li class="lh20">
				<a href="http://lizspetshop.com/about-us">
					Class aptent
				</a>
			</li>
			<li class="lh20">
				<a href="http://lizspetshop.com/about-us">
					Morbi ut
				</a>
			</li>
			<li class="lh20">
				<a href="http://lizspetshop.com/about-us">
					Proin interdum
				</a>
			</li>
			<li class="lh20">
				<a href="http://lizspetshop.com/about-us">
					Vestibulum diam
				</a>
			</li>
		</ul>
		<ul class="wp20 f_left left pl10" style="list-style-type:none;">
			<li class="lh20">
				<strong>
					Aenean non
				</strong>
			</li>
			<li class="lh20">
				<a href="http://lizspetshop.com/about-us">
					Suspendisse
				</a>
			</li>
			<li class="lh20">
				<a href="http://lizspetshop.com/about-us">
					Curabitur vulputate
				</a>
			</li>
			<li class="lh20">
				<a href="http://lizspetshop.com/about-us">
					Mauris vestibulum
				</a>
			</li>
			<li class="lh20">
				<a href="http://lizspetshop.com/about-us">
					Vestibulum hendrerit
				</a>
			</li>
			<li class="lh20">
				<a href="http://lizspetshop.com/about-us">
					Sed scelerisque
				</a>
			</li>
		</ul>
		<ul class="wp20 f_left left pl10" style="list-style-type:none;">
			<li class="lh20">
				<strong>
					Nullam sollicitudin
				</strong>
			</li>
			<li class="lh20">
				<a href="http://lizspetshop.com/about-us">
					Mauris vestibulum
				</a>
			</li>
			<li class="lh20">
				<a href="http://lizspetshop.com/about-us">
					Class aptent
				</a>
			</li>
			<li class="lh20">
				<a href="http://lizspetshop.com/about-us">
					Morbi ut
				</a>
			</li>
			<li class="lh20">
				<a href="http://lizspetshop.com/about-us">
					Proin interdum
				</a>
			</li>
			<li class="lh20">
				<a href="http://lizspetshop.com/about-us">
					Vestibulum diam
				</a>
			</li>
		</ul>
	</div>
	<?
}

function massage_6_template(){
	$general_dal = new GENERAL_DAL();
	$IMAGE_DAL   = new IMAGE_DATA_DAL();
	?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html>
		<?=default_header();?>
		<?loading_div()?>
		<body class="h800px w800 main_bc_color1 mauto">
			<div class="wp95 hp100 main_bc_color1 mauto">
				<div class="d_InlineBlock wp100 hp20 t_align_center bclightgray">
					<?=header_6($IMAGE_DAL)?>
				</div>
				<div class="d_InlineBlock wp100 hp20 t_align_center bclightgray">
					<?=body_6($IMAGE_DAL)?>
				</div>
				<div class="d_InlineBlock wp100 hp20 t_align_center bclightgray">
					<?=footer_6($IMAGE_DAL)?>
				</div>
			</div>
		</body>
	</html>
	<?
}
function header_6($IMAGE_DAL){
	$main_company_logo = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID($_SESSION['settings']['company_id'],'main_company_logo');
	?>
	<a href="/">
		<img class='wp100 hp95 b0 <?
		if($main_company_logo[0]->image_id > 0)
		{
			print ' mp';
		} ?>' src='/pos/showimage.php?id=<?=$main_company_logo[0]->image_id?>&image_db_id=<?=$main_company_logo[0]->image_db_id?>'  <?
		if($main_company_logo[0]->image_id > 0)
		{
			?> <?
		} ?>  />
	</a>
	<div class="d_InlineBlock wp100 hp07">
		<div class="f_left d_InlineBlock hp100 wp33 bclightgray">
			<a href="/">
				DubbsPOS - Point of Sale Software
			</a>
		</div>
		<div class="f_left d_InlineBlock hp100 wp02">
			&nbsp;
		</div>
		<div class="f_left d_InlineBlock hp100 wp40 s07 bclightgray">
			<div class="f_left hp100 wp20">
				<a class="signup" href="/signup">
					Get Started
				</a>
			</div>
			<div class="f_left hp100 wp20">
				<a href="/retaileasy">
					Retail. Easy.
				</a>
			</div>
			<div class="f_left hp100 wp20">
				<a href="/pricing">
					Pricing
				</a>
			</div>
			<div class="f_left hp100 wp20">
				<a href="/customers">
					Customers
				</a>
			</div>
			<div class="f_left hp100 wp20">
				<a href="/tour">
					Take a Tour
				</a>
			</div>
		</div>

		<div class="f_left d_InlineBlock hp100 wp02">
			&nbsp;
		</div>
		<div class="f_left d_InlineBlock hp100 wp23 bclightgray">
			<div class="f_left hp100 wp30">
				<a href="/blog">
					Blog
				</a>
			</div>
			<div class="f_left hp100 wp02">
				/
			</div>
			<div class="f_left hp100 wp30">
				<a href="/help">
					Help
				</a>
			</div>
			<div class="f_left hp100 wp02">
				/
			</div>
			<div class="f_left hp100 wp30">
				<a class="login" href="https://shop.dubbsenterprises.com">
					Login
				</a>
			</div>
		</div>
	</div>
	<?
}
function body_6($IMAGE_DAL){
	?>
	<div class="d_InlineBlock wp100 hp45 t_align_center bclightyellow">
		BIG OLE PIC
	</div>
	<div class="d_InlineBlock wp100 hp10 t_align_center bclightblue">
		<div class="f_left wp15 hp100">
			&nbsp;
		</div>
		<div class="f_left wp35 hp100">
			<label for="admin_email">
				Email Address
			</label>
			<input type="email" required="required" name="admin_email" label="Email Address" placeholder="Email Address" size="32">
			</input>
		</div>
		<div div class="f_left wp20 hp100">
			<label for="admin_password">
				Password
			</label>
			<input type="password" label="Password" minlength="6" required="required" autocomplete="off" name="admin_password" placeholder="Password" size="12">
			</input>
		</div>
		<div div class="f_left wp15 hp100">
			<input type="submit" value="Create my account">
			</input>
		</div>
		<div class="f_left wp15 hp100">
			&nbsp;
		</div>
		<input type="hidden" name="sys_type" value="small">
		</input>
		<input type="hidden" name="timestamp" value="1340187497">
		</input>
		<input type="hidden" name="token" value="afd213cccd36c3f106661c6ed43fd68c">
		</input>
	</div>
	<?
}
function footer_6($IMAGE_DAL){
	?>
	<div class="f_left d_InlineBlock wp100 hp100 s07 pt5">
		<ul class="wp20 f_left left pl10" style="list-style-type:none;">
			<li class="lh20" >
				<strong>
					Quisque sit
				</strong>
			</li>
			<li class="lh20">
				<a href="http://lizspetshop.com/about-us">
					Suspendisse
				</a>
			</li>
			<li class="lh20">
				<a href="http://lizspetshop.com/about-us">
					Curabitur vulputate
				</a>
			</li>
			<li class="lh20">
				<a href="http://lizspetshop.com/about-us">
					Mauris vestibulum
				</a>
			</li>
			<li class="lh20">
				<a href="http://lizspetshop.com/about-us">
					Vestibulum hendrerit
				</a>
			</li>
			<li class="lh20">
				<a href="http://lizspetshop.com/about-us">
					Sed scelerisque
				</a>
			</li>
		</ul>
		<ul class="wp20 f_left left pl10" style="list-style-type:none;">
			<li class="lh20">
				<strong>
					Suspendisse potenti
				</strong>
			</li>
			<li class="lh20">
				<a href="http://lizspetshop.com/about-us">
					Mauris vestibulum
				</a>
			</li>
			<li class="lh20">
				<a href="http://lizspetshop.com/about-us">
					Class aptent
				</a>
			</li>
			<li class="lh20">
				<a href="http://lizspetshop.com/about-us">
					Morbi ut
				</a>
			</li>
			<li class="lh20">
				<a href="http://lizspetshop.com/about-us">
					Proin interdum
				</a>
			</li>
			<li class="lh20">
				<a href="http://lizspetshop.com/about-us">
					Vestibulum diam
				</a>
			</li>
		</ul>
		<ul class="wp20 f_left left pl10" style="list-style-type:none;">
			<li class="lh20">
				<strong>
					Aenean non
				</strong>
			</li>
			<li class="lh20">
				<a href="http://lizspetshop.com/about-us">
					Suspendisse
				</a>
			</li>
			<li class="lh20">
				<a href="http://lizspetshop.com/about-us">
					Curabitur vulputate
				</a>
			</li>
			<li class="lh20">
				<a href="http://lizspetshop.com/about-us">
					Mauris vestibulum
				</a>
			</li>
			<li class="lh20">
				<a href="http://lizspetshop.com/about-us">
					Vestibulum hendrerit
				</a>
			</li>
			<li class="lh20">
				<a href="http://lizspetshop.com/about-us">
					Sed scelerisque
				</a>
			</li>
		</ul>
		<ul class="wp20 f_left left pl10" style="list-style-type:none;">
			<li class="lh20">
				<strong>
					Nullam sollicitudin
				</strong>
			</li>
			<li class="lh20">
				<a href="http://lizspetshop.com/about-us">
					Mauris vestibulum
				</a>
			</li>
			<li class="lh20">
				<a href="http://lizspetshop.com/about-us">
					Class aptent
				</a>
			</li>
			<li class="lh20">
				<a href="http://lizspetshop.com/about-us">
					Morbi ut
				</a>
			</li>
			<li class="lh20">
				<a href="http://lizspetshop.com/about-us">
					Proin interdum
				</a>
			</li>
			<li class="lh20">
				<a href="http://lizspetshop.com/about-us">
					Vestibulum diam
				</a>
			</li>
		</ul>
	</div>
	<?
}