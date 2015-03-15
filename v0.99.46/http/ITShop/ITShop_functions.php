<?php
if(!isset($_SESSION)){ session_start(); }
if (isset($_SERVER['SUBDOMAIN_DOCUMENT_ROOT'])){
    $orig_path_info = realpath($_SERVER['SUBDOMAIN_DOCUMENT_ROOT']);
    $pos = strpos($orig_path_info, '/shop/');
}
else {
    $orig_path_info = realpath($_SERVER['DOCUMENT_ROOT']);
    $pos = strpos($orig_path_info, '/shop/');
}
$shop_pos   = strpos($orig_path_info, '/shop');
$shop_path  = substr($orig_path_info, 0, $shop_pos)."/shop/";
$Zend_path = $shop_path . 'Zend/library/';
set_include_path($shop_path.'v0.99.40/:'.$Zend_path);

include_once("includes/general_functions.php");
include_once("includes/profiles_functions.php");
include_once("includes/appointment_functions.php");
include_once("includes/inventory_management_functions.php");

set_preference_session($_SESSION['settings']['company_id']);
####################

function ITShop_1_template(){?>
<head>
<title>Home - Home Page | IT - Free Website Template from Templates.com</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<meta name="description" content="Place your description here" />
<meta name="keywords" content="put, your, keyword, here" />
    <link href="ITShop/style.css" rel="stylesheet" type="text/css" />
    <link href="ITShop/layout.css" rel="stylesheet" type="text/css" />
    
    <link   rel="stylesheet" type="text/css"    href="pos/includes/pos.css">
    <script type="text/javascript"              src="pos/includes/jQueryJS/jquery-1.4.4.min.js"></script>
    <script type="text/javascript"              src="pos/includes/jQueryJS/jquery-ui-1.8.9.custom.min.js"></script>
    <script type="text/javascript"              src= "common_includes/common.js"></script>
        
</head>
<body id="page1">
<div class="tail-top-right"></div>
<div class="tail-top">
  <div class="tail-bottom">
    <div id="main">
      <!-- header -->
      <div id="header">
        <?php header_search_div(); ?>
        <?php #show_top_right_icons();?>
        <?php #show_site_nav();?>
        <div class="logo"><a href="home.html"><img src="common_includes/includes/images/logo.gif" alt="" /></a></div>
        <div class="slogan"><img src="common_includes/includes/images/slogan.gif" alt="" /></div>
      </div>
      <!-- content -->
      <div id="body_div">
        <div class="wrapper">
            <?php main_column1(); ?>
            <?php main_column2(); ?>
            <?php main_column3(); ?>
        </div>
      </div>
      <!-- footer -->
      <?php footer_1_template(); ?>
    </div>
  </div>
</div>
</body>
</html>

<? }
function footer_1_template() {
?>
      <div id="footer">
        <div class="indent">
          <div class="fleft">Copyright - DubbsEnterprises</div>
          <div class="fright">Designed by: &nbsp;Dubbsenterprises</div>
        </div>
      </div>
<?php
}
    function main_column1(){
        ?>
              <div class="col-1">
                <h3>News</h3>
                <ul class="list1 p2">
                  <li><img src="common_includes/includes/images/1page-img1.jpg" alt="" />
                    <h4><a href="#">November 4, 2009</a></h4>
                    <p class="zoom">At vero eos et accusamus et iusto odio dignissimos quidem rerum facui  praesentium.</p>
                  </li>
                  <li><img src="common_includes/includes/images/1page-img2.jpg" alt="" />
                    <h4><a href="#">November 1, 2009</a></h4>
                    <p class="zoom">Voluptatum deleniti atque corrupti quos dolores et quidem rerum facilis est et expedita distincti.</p>
                  </li>
                  <li><img src="common_includes/includes/images/1page-img3.jpg" alt="" />
                    <h4><a href="#">October 23, 2009</a></h4>
                    <p class="zoom">Sint occaecati cupiditate non provi- dent, similique animi, id est laborum et dolorum fuga. Et harum sunt in culpa qui officia.</p>
                  </li>
                </ul>
                <div class="wrapper"><a href="#" class="link1"><em><b>News Archive<span>News Archive</span></b></em></a></div>
              </div>

    <?php
    }
    function main_column2(){
        ?>
              <div class="col-2">
                <h3>Welcome!</h3>
                <p class="p1">DubbsEnterprises POS is the answer to an affordable and dependable Point of Sales system.</p>
                <p class="p1">DubbsEnterprises POS specializes in easy to use, fully customizable POS Software
                    to meet the needs of any Boutique, Restaurant, Retail Store, Salon, or other Business. Our
                    POS Software offers such a dynamic and customizable solution that the issue of "which hardware should I
                    purchase for MY POS system diminishes" because our customizable software can sole many issues once solved by hardware.</p>
                <p class="p1">No Software to download and no special hardware needed.  Perfect for Specialty Boutiques and All retail outlets.</p>
                <p class="p1">Manage your store at its location or remotely via our remote manager interface.  Add inventory, track sales, view live reports.</p>
                <div class="wrapper"><a href="#" class="link1"><em><b>Read More<span>Read More</span></b></em></a></div>
              </div>
    <?php
    }
    function main_column3(){
        ?>
              <div class="col-3">
                <h3><a id="scheduleAnAppointment" onclick="load_appointments_div('body_div');" class="menu">ARTICLES</a></h3>
                <ul class="list1 p2">
                  <li><img src="common_includes/includes/images/icon4.jpg" alt="" />
                    <h4><a href="#">Welcome To our Company</a></h4>
                    <p class="zoom">If you are looking for perfect IT services for your business, you will find them here!</p>
                  </li>
                  <li><img src="common_includes/includes/images/icon5.jpg" alt="" />
                    <h4><a href="#">Our services</a></h4>
                    <p class="zoom">We specialize in POS systems as a gateway to a partnership with your business. From there
                        we provide customized solutions to help manage your business. We can automate any process to help run
                        your business more efficiently.</p>
                  </li>
                  <li><img src="common_includes/includes/images/icon6.jpg" alt="" />
                    <h4><a href="#">About "DubbsEnterprises"</a></h4>
                    <p class="zoom">DubbsEnterprises has over 10 years experience creating applications to help businesses get ahold of their bottom line.</p>
                  </li>
                </ul>
                <div class="wrapper"><a href="#" class="link1"><em><b>News Archive<span>News Archive</span></b></em></a></div>
              </div>
    <?php
    }
    function header_search_div() {
        ?>
        <div>
                 <form action="" method="post" id="form">
                <label>Website Search:</label>
                <span>
                <input type="text" />
                </span>
                </form>
        </div>
    <?php
    }
    function show_top_right_icons() {
        ?>
            <ul class="list">
              <li><a href="home.html"><img src="common_includes/includes/images/icon1.gif" alt="" /></a></li>
              <li><a href="contact-us.html"><img src="common_includes/includes/images/icon2.gif" alt="" /></a></li>
              <li class="last"><a href="sitemap.html"><img src="common_includes/includes/images/icon3.gif" alt="" /></a></li>
            </ul>
    <?php
    }
    function show_site_nav() {
        ?>
            <ul class="site-nav">
              <li><a href="home.html">Home</a></li>
              <li><a href="about-us.html">About Us</a></li>
              <li><a href="services.html">Services</a></li>
              <li><a href="support.html">Support</a></li>
              <li><a href="contact-us.html">Contact Us</a></li>
              <li class="last"><a href="sitemap.html">Site Map</a></li>
            </ul>
    <?
    }

function ITShop_2_template(){
include_once("includes/general_functions.php");
$general_dal = new GENERAL_DAL();
$company_name           = $general_dal->get_Company_Preference($_SESSION['settings']['company_id'],'company name');
$meta_description       = $general_dal->get_Company_Preference($_SESSION['settings']['company_id'],'meta_description');
$meta_keywords          = $general_dal->get_Company_Preference($_SESSION['settings']['company_id'],'meta_keywords');
    ?>
<html>
    <head>
        <title><?=$company_name[0]->value?></title>
        <META name="description"    content="<?=$meta_description[0]->value?>">
        <META name="keywords"       content="<?=$meta_keywords[0]->value?>">
        <link   rel="stylesheet" type="text/css"    href="pos/includes/pos.css">
        <script type="text/javascript"              src="pos/includes/jQueryJS/jquery-1.4.4.min.js"></script>
        <script type="text/javascript"              src="pos/includes/jQueryJS/jquery-ui-1.8.9.custom.min.js"></script>
        <script type="text/javascript"              src= "common_includes/common.js"></script>

        <link   rel="stylesheet" type="text/css"    href="common_includes/includes/common.css">
        <link   rel="stylesheet" type="text/css"    href="/common_includes/colors_styles.php?style=Include" media="screen">
    </head>
    <body class="main_bc_color1 m0 center">
        <div class="w800 d_InlineBlock center main_bc_color1">
            <div id="container">
                <header>
                    <div id="logo"><a href="http://www.schedulicity.com/">Schedulicity</a></div>
                    <div class="signInContainer">

                      <div><a target="_blank" href="http://schedulicity.extole.com/m/1971208463">Refer a friend, get $50</a> &nbsp; &nbsp; &nbsp;<a id="signInButton" href="https://www.schedulicity.com/SignIn.aspx">Sign In</a></div>

                    </div>
                    <nav>
                        <ul>
                            <li><a href="/Why/A-Better-Way.aspx">Why Schedulicity</a></li>
                            <li><a href="/Essentials/Everything-You-Need.aspx">Essentials</a></li>
                            <li><a href="https://www.schedulicity.com/Business/Setup/CreateAccount.aspx">Business Sign Up</a></li>
                        </ul>
                    </nav>
                    <div class="clear"></div>
                </header>
	</div>
            <?php loading_div()?>
        </div>
    </body>
</html>
<? }
    function login_horizontal() { ?>
    <div class="d_InlineBlock wp100 mt5">
        <? if (!isset($_SESSION['appointment']['customer_id'])) {   ?>
            <div id="loginPanel" class="d_InlineBlock wp100 white s12 f_right">
                <div id="loginPanel_Error" class="f_right red s1 pr10">
                    &nbsp;
                </div>
                <div class="wp30 f_right">
                    <input class="wp90" placeholder="email address" type="text" id="user_email_quick_check">
                </div>
                <div style="float:right;">
                    <a onclick="ValidateUserQuickCheck();" ><img src="/common_includes/includes/images/btn-login.png"></a>
                </div>
            </div>
        <? } else { ?>
            <div id="loginPanel" class="d_InlineBlock wp100">
                <div class="white s12 f_right">Welcome Back <?=$_SESSION['appointment']['first_name']?> <?=$_SESSION['appointment']['last_name']?></div>
            </div>
        <? } ?>
    </div>
    <?php
    }
    function header_function() { ?>
    <div class="d_InlineBlock wp100 mt5 mb5 main_bc_color2">
        <div class="f_left wp02">&nbsp;</div>
        <div class="f_left wp20"><a href="/"><img src="common_includes/includes/images/esquire2.gif" width="172" height="87" border="0"></a></div>
        <div class="f_left wp75">&nbsp;</div>
    </div>
        <?php }
    # function main_menu above
    function body_1() {
        $IMAGE_DAL = new IMAGE_DATA_DAL();
        $main_page_img_1 = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID($_SESSION['settings']['company_id'],'main_page_img_1');
        $main_page_img_2 = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID($_SESSION['settings']['company_id'],'main_page_img_2');
        $main_page_img_3 = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID($_SESSION['settings']['company_id'],'main_page_img_3');
        ?>
        <div class="wp98 h350px d_InlineBlock main_bc_color2 center">
            <div class="f_left wp33"><img width="259" height="350" class='m0<? if ($main_page_img_1[0]->image_id > 0) { print ' mp'; } ?>' src='/pos/showimage.php?id=<?=$main_page_img_1[0]->image_id?>&image_db_id=<?=$main_page_img_1[0]->image_db_id?>'<? if ($main_page_img_1[0]->image_id > 0) { ?> <? } ?>/></div>
            <div class="f_left wp33"><img width="259" height="350" class='m0<? if ($main_page_img_2[0]->image_id > 0) { print ' mp'; } ?>' src='/pos/showimage.php?id=<?=$main_page_img_2[0]->image_id?>&image_db_id=<?=$main_page_img_2[0]->image_db_id?>'<? if ($main_page_img_2[0]->image_id > 0) { ?> <? } ?>/></div>
            <div class="f_left wp33"><img width="259" height="350" class='m0<? if ($main_page_img_3[0]->image_id > 0) { print ' mp'; } ?>' src='/pos/showimage.php?id=<?=$main_page_img_3[0]->image_id?>&image_db_id=<?=$main_page_img_3[0]->image_db_id?>'<? if ($main_page_img_3[0]->image_id > 0) { ?> <? } ?>/></div>
        </div>
        <?php
        }
        function services() { ?>
            <table class="main_bc_color2_light main_color2_light_text" width="784" cellpadding="0" cellspacing="0" border="0" align="center">
            <tr>
                    <td valign="top"><table class="body" cellpadding="0" cellspacing="0" border="0">
                      <tr>
                        <td bgcolor="#a26441" colspan="2"></td>
                        </tr>
                      <tr>
                        <td bgcolor="#67381b"><img src="common_includes/includes/images/our-services.gif" width="200" height="31" alt="Our Services" hspace="26"></td>
                        <td bgcolor="#67381b" align="right"></td>
                        </tr>
                      <tr>
                        <td height="450" colspan="2" valign="top" class="content"><h2>HEAD</h2>
                          <b>Signature: $17</b> <br>
                          Precision haircut, hot lather neck shave, and hot towel.
                          <p><b>Traditional Clipper: $15</b> <br>
                              Not your ordinary clipper cut. Includes a clipper only cut,
                          hot lather neck shave, and hot towel.
                          <p><b>Hot Lather Straight Razor Shave: $25</b> <br>
                                    Hot towel  shave and clean up around ears.
                        <p><i>Complimentary</i> ear and neck clean up within two weeks of last hair cut service.
                        <p align="center"><img src="common_includes/includes/images/brown.gif" width="5" height="12" alt=""> <img src="common_includes/includes/images/brown.gif" width="5" height="12" alt=""> <img src="common_includes/includes/images/brown.gif" width="5" height="12" alt=""></p>
                          <h2>FACE</h2>
                          <p><b>Beard Trim: $10</b>
                            <p><b>Mustache or Goatee: $10</b>
                        <p>
                        <p align="center"><img src="common_includes/includes/images/brown.gif" width="5" height="12" alt=""> <img src="common_includes/includes/images/brown.gif" width="5" height="12" alt=""> <img src="common_includes/includes/images/brown.gif" width="5" height="12" alt=""></p>
                          <h2>&nbsp;</h2></td>
                        </tr>
                </table></td>
                    <td bgcolor="#a26441" rowspan="3" valign="top">
                    <table class="sidebar" cellpadding="0" cellspacing="0" border="0">
                    <tr><td><img src="common_includes/includes/images/services.jpg" width="259" height="350" alt=""></td></tr>
                    <tr><td bgcolor="#67381b" height="1"></td></tr>
                    <tr><td class="tips" valign="top">
                            <h3> Keep yourself looking tight.  First impressions only happen once. </h3>
                            <p style="margin-bottom: 20px;"></p>
                    </td></tr>
                    <tr><td bgcolor="#67381b" height="1"></td></tr>
                    <tr><td>&nbsp;</td></tr>
                    <tr><td bgcolor="#67381b" height="1"></td></tr>
                    <tr><td class="tips" valign="top">&nbsp;</td></tr>
                    <tr><td bgcolor="#67381b" height="1"></td></tr>
                    <tr><td bgcolor="#67381b" height="1"></td></tr>
                    </table></td>
            </tr>
            </table>
            <?php }
        function about_us() { ?>
        <table class="main_bc_color2_light main_color2_light_text" width="784" cellpadding="0" cellspacing="0" border="0" align="center">
            <tr>
                <td valign="top">
                    <table class="body" style="height: 816px;"  cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td bgcolor="#a26441" height="1" colspan="2"></td>
                        </tr>
                        <tr>
                            <td bgcolor="#67381b" height="31"><img src="common_includes/includes/images/about-us.gif" width="200" height="31" alt="About Us" hspace="26"></td>
                            <td bgcolor="#67381b" align="right"></td>
                        </tr>
                        <tr>
                            <td colspan="2" valign="top" class="content">
                                <h2>Esquire, the premier men's barbershop experience.</h2>
                                <p>Men don't belong in a beauty salon.  They belong in a barbershop! </p>
                                <p>Since re-opening our doors in Fall, 2009, Esquire has prided itself on our emphasis towards personal and uncompromising service.  </p>
                                <p>Owned and operated locally in Chicago's Andersonville neighborhood, Esquire has focused on masculine tastes and needs and has firmly established ourselves as the premier men's barbershop.  Owner Aaron Williams recognized the need for a Old Time Men's barbershop in which today's busy man may feel comfortable and receive quality service along with excellent grooming.   We also provide the opportunity to relax in our comfortable  chairs and take a few minutes to read a newspaper or magazine, watch TV news or catch up on the big game.</p>
                                <p><object width="425" height="344"><param name="movie" value="http://www.youtube.com/v/hG4XdbSD2I8&hl=en&fs=1"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="http://www.youtube.com/v/hG4XdbSD2I8&hl=en&fs=1" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="425" height="344"></embed></object>&nbsp;</p>
                                <p>&nbsp;</p>
                            </td>
                        </tr>
                    </table>
                </td>
                <td bgcolor="#a26441" rowspan="3" valign="top">
                    <table class="sidebar" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td><img src="common_includes/includes/images/about.jpg" width="259" height="250" alt=""></td>
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
                        <tr>
                            <td>&nbsp;</td>
                        </tr>
                    </table>
                    <p class="sub">&nbsp;</p>
                </td>
            </tr>
        </table>
            <?php }
        function location() { ?>

        <table class="main_bc_color2_light main_color2_light_text" width="784" cellpadding="0" cellspacing="0" border="0" align="center">
        <tr>
                <td valign="top">
                    <table class="body" style="height: 500px;" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td bgcolor="#a26441" height="1" colspan="2"></td>
                        </tr>

                        <tr>
                                <td bgcolor="#67381b" height="31"><img src="common_includes/includes/images/location.gif" width="200" height="31" alt="Location & Hours" hspace="26"></td>
                                <td bgcolor="#67381b" align="right"></td>
                        </tr>

                        <tr>
                            <td class="content" valign="top" colspan="2">
                              <iframe width="425" height="350" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="http://maps.google.com/maps?f=q&amp;source=s_q&amp;hl=en&amp;geocode=&amp;q=1511+West+Foster+Ave+Chicago,+IL&amp;sll=41.883876,-87.653046&amp;sspn=0.338419,0.575409&amp;ie=UTF8&amp;hq=&amp;hnear=1511+W+Foster+Ave,+Chicago,+Cook,+Illinois+60640&amp;ll=41.976178,-87.668581&amp;spn=0.005583,0.00912&amp;z=16&amp;iwloc=A&amp;output=embed"></iframe>
                              <br />
                              <small><a href="http://maps.google.com/maps?f=q&amp;source=embed&amp;hl=en&amp;geocode=&amp;q=1511+West+Foster+Ave+Chicago,+IL&amp;sll=41.883876,-87.653046&amp;sspn=0.338419,0.575409&amp;ie=UTF8&amp;hq=&amp;hnear=1511+W+Foster+Ave,+Chicago,+Cook,+Illinois+60640&amp;ll=41.976178,-87.668581&amp;spn=0.004786,0.006437&amp;z=16&amp;iwloc=A" style="color:#0000FF;text-align:left">View Larger Map</a></small>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <? make_appointment_step1_business_hours($_SESSION['settings']['company_id'])?>
                            </td>
                        </tr>
                    </table>
                </td>
                <td bgcolor="#a26441"  valign="top">
                        <table class="sidebar" cellpadding="0" cellspacing="0" border="0">
                        <tr><td><img src="common_includes/includes/images/newphoto1.jpg" width="259" height="350" alt=""></td></tr>
                        <tr><td bgcolor="#67381b" height="1"></td></tr>
                        <tr><td class="tips" height="120" valign="top">
                                <h3>GROOMING TIPS</h3>
                                Shave in the same direction as the hair growth. Use short strokes and take care over the neck and throat.
                        </td></tr>
                        <tr><td bgcolor="#67381b" height="1"></td></tr>
                        <tr><td><img src="common_includes/includes/images/home1.jpg" width="259" height="350" alt=""></td></tr>
                        </table>
                </td>
        </tr>
        </table>
            <?php }
        function packages() { ?>

        <table class="main_bc_color2_light main_color2_light_text" width="784" cellpadding="0" cellspacing="0" border="0" align="center">
        <tr>
                <td border="10" valign="top">
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
                <td bgcolor="#a26441" valign="top" >
                    <table class="sidebar" cellpadding="0" cellspacing="0">
                        <tr>
                            <td><img src="common_includes/includes/images/packages.jpg" width="259" height="350" alt=""></td>
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
    function footer() { ?>
        <div class="d_InlineBlock wp98 main_bc_color2 main_color2_text mb5">
            <div class="wp20 f_left"><a href="http://www.yelp.com/biz/esquire-barber-chicago#hrid:7n4_BBWSqn3hFlnAWQ-IZQ/src:search/query:Esquire%20Barber%20Shop" target="_blank" class="main_color2_text">YELP</a> &nbsp; <a href="products.html"  class="main_color2_text">PRODUCTS</a></div>
            <div class="wp80 f_right right main_color2_text"><a href="mailto:aaron@esquirebarberchicago.com"  class="main_color2_text">EMAIL: aaron@esquirebarberchicago.com </a> &nbsp;  &nbsp; <font class="phone"><?=$_SESSION['company_info'][$_SESSION['settings']['company_id']]['Phone_Number_Main']?></font></div>
        </div>
        <?php
        }
    function copyright() { ?>
            <table width="784" cellpadding="0" cellspacing="0" border="0" align="center">
            <tr>
                <td class="copy main_color1_text"><br>
                    Copyright <? echo date("Y")?> Esquire Barbershop<br>
                </td>
            </tr>
            </table>
        <?php
        }
?>