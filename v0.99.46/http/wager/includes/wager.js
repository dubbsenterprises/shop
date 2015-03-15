
function getData(part)
{
    	var div = part + '_div';
        $.fx.speeds.xslow = 3000; // 'xslow' means 3 seconds
	var url = "?part="+part;
	$("#"+div).load("/common_includes/ajax/getData.php"+url).fadeIn(2000);
}
function changeData(str)
{
    var menuLinks =["services", "about_us", "location", "packages","appointments"] ; //3) literal array
    var menu;
    var div = 'body_div';
    var img_id =  str + '_img';
       document.getElementById(img_id).src='massage/includes/images/red.gif';
       for (menu in menuLinks)
       {
           var current_image_id = menuLinks[menu] + '_img';
           if (str != menuLinks[menu]) { document.getElementById(current_image_id).src='massage/includes/images/black.gif'; }
       }
        $.fx.speeds.xslow = 3000; // 'xslow' means 3 seconds
	var url = "?part="+str;
	$("#"+div).load("/common_includes/ajax/getData.php"+url).fadeIn(2000);
}