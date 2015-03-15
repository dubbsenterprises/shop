$initial = 0;
$started = 0;

function init($milli) {
	if ($item = document.getElementById("focusitem")) { $item.focus(); }
	updateTime($milli);
}
function updateTime($milli) {
	if ($item = document.getElementById("localtime")) {
		if ($milli > 0) { $initial = $milli; }
		$milli = $initial;
		$now = new Date();
		if ($started == 0) { $started = $now.getTime(); }
		$milli += $now.getTime() - $started;
		$date = new Date($milli);
		$year = $date.getYear() + 1900;
		$month = $date.getMonth() + 1; if ($month < 10) { $month = '0' + $month; }
		$day = $date.getDate(); if ($day < 10) { $day = '0' + $day; }
		$hour = $date.getHours(); if ($hour < 10) { $hour = '0' + $hour; }
		$minute = $date.getMinutes(); if ($minute < 10) { $minute = '0' + $minute; }
		$item.innerHTML = $month + '/' + $day + '/' + $year + ' ' + $hour + ':' + $minute;
		window.setTimeout("updateTime()", 1000);
	}
}
function label($id, $width, $count,all,view_type) {
    if(!all) { //If the optional argument is not there, create a new variable with that name.
            all = 0;
    }
    if(!view_type) { //If the optional argument is not there, create a new variable with that name.
            view_type = 'none';
    }

    if (!($count > 0)) { $count = 1; }
    if ($count > 1) { $countadd = '&count=' + $count; } else { $countadd = ''; }
    if (all == 1) {
            window.open('itemlabel.php?id=' + $id + '&all=1' + '&view_type=' + view_type + '&w=' + $width + $countadd, 'itemlabel', 'screenx=100,screeny=100,width=' + ($width + 50) + ',height=200,scrollbars=yes');
    }
    else {   
	window.open('itemlabel.php?id=' + $id + '&w=' + $width + $countadd, 'itemlabel', 'screenx=100,screeny=100,width=' + ($width + 50) + ',height=200,scrollbars=yes');
    }
}
function none () {
	// DO NOTHING
}
function trim(s)
{
  return s.replace(/^\s+|\s+$/, '');
}
function sendRequest(url) {
	var httpRequest;
	if (window.XMLHttpRequest) {
		httpRequest = new XMLHttpRequest();
		if (httpRequest.overrideMimeType) {
			httpRequest.overrideMimeType('text/xml');
		}
	} else if (window.ActiveXObject) {
		try {
			httpRequest = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
			try {
				httpRequest = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (e) {
				// AJAX NOT WORKING FOR THIS INTERNET EXPLORER
			}
		}
	}
	if (!httpRequest) {
		return false; // AJAX NOT WORKING
	}
	httpRequest.onreadystatechange = function() { receiveAnswer(httpRequest); };
	httpRequest.open('GET', url, true);
	httpRequest.send('');
        return 1;
}
function receiveAnswer(httpRequest) {
	if (httpRequest.readyState == 4) {
		var lines = httpRequest.responseText.split("\n");
		var line = '';
		var update = 0;
		var mode = '';
		var replace = 0;
		var append = 0;

		while (line = lines.shift().replace(/^\s+/, '')) {
			if (line.substr(0, 8) == '<update>') {
				update = 1;
			} else if (update == 1 && line.substr(0, 8) == '<option>') {
				mode = 'option';
			} else if (update == 1 && line.substr(0, 6) == '<text>') {
				mode = 'text';
			} else if (update == 1 && mode != '' && replace + append == 0 && line.substr(0, 9) == '<replace>') {
				replace = 1;
			} else if (update == 1 && mode != '' && replace + append == 0 && line.substr(0, 8) == '<append>') {
				append = 1;
			} else if (update == 1 && line.substr(0, 9) == '</update>') {
				update = 0;
			} else if (update == 1 && mode == 'option' && line.substr(0, 9) == '</option>') {
				mode = '';
			} else if (update == 1 && mode == 'text' && line.substr(0, 7) == '</text>') {
				mode = '';
			} else if (update == 1 && mode != '' && replace == 1 && line.substr(0, 10) == '</replace>') {
				replace = 0;
			} else if (update == 1 && mode != '' && append == 1 && line.substr(0, 9) == '</append>') {
				append = 0;
			} else if (update == 1 && mode != '' && replace + append == 1) {
				var setting = line.split('>');
				var id = setting[0].replace(/^</, '');
				var value = setting[1].replace(/<.*$/, '');

				if (mode == 'option') {
					tmp = value.split(',', 2);

					if (append == 1) {
						new_option = new Option(tmp[1], tmp[0], false, false);
						document.getElementById(id).options[document.getElementById(id).options.length] = new_option;
					}
				} else {
					if (append == 1) {
						document.getElementById(id).innerHTML = document.getElementById(id).innerHTML + '' + value;
					}
	
					if (replace == 1) {
						document.getElementById(id).innerHTML = value;
					}
				}
			}
		}
	}
}
function max_days(year, month) {
	month = parseInt(month);
	days = 31;
	switch (month) {
		case 4: case 6: case 9: case 11: days--; break;
		case 2: days -= 2;
			if (year % 4 != 0 || year % 100 == 0) {
				days--;
			}
	}

	return days;
}
function update_calendar(year_obj, month_obj, day_obj, date_obj, update_days) {
	if (update_days == 1) {
		s = day_obj.options.selectedIndex;
		day_obj.options.length = 28;
		max = max_days(year_obj.options[year_obj.options.selectedIndex].value, month_obj.options[month_obj.options.selectedIndex].value);
		s = s > max - 1 ? max - 1 : s;

		for(i = 29; i <= max_days(year_obj.options[year_obj.options.selectedIndex].value, month_obj.options[month_obj.options.selectedIndex].value); i++) {
			new_option = new Option(i + ',', i, false, false);
  			day_obj.options[i - 1] = new_option;
		}

		day_obj.options.selectedIndex = s;
	}

	date_obj.value = year_obj.options[year_obj.options.selectedIndex].value + '-' + month_obj.options[month_obj.options.selectedIndex].value + '-' + day_obj.options[day_obj.options.selectedIndex].value;
}
function update_delivery_items(number_obj) {
	document.getElementById('ajax_delivery_item_select').options.length = 1;
	if (number_obj.options.selectedIndex == 0) {
		sendRequest('getinfo.php?update_delivery_items=1');
	} else {
		sendRequest('getinfo.php?update_delivery_items=1&number_item_id=' + number_obj.options[number_obj.options.selectedIndex].value);
	}
}
function clearText(field){
    if (field.defaultValue == field.value) field.value = '';
    else if (field.value == '') field.value = field.defaultValue;
}
function loading_div(div){
    loadingDiv  = '<div class=\'centeredImage\' style=\'background:url(/common_includes/includes/images/328.gif) no-repeat center center;");\'></div>'
    html        = '\n\
    <div id="mainBody" class="d_InlineBlock wp100 hp100">\n\
        <div class="ReportsTopRow main_bc_color2 main_color2_text">&nbsp;</div> \n\
            <div style="max-height: 1000px;" class="f_left wp100 hp94">\n\
                <div class="leftSpace main_bc_color2 main_color2_text">&nbsp;</div>\n\
                <div class="middleSpace wp96">\n\
                    '+loadingDiv+'\n\
                </div>\n\
                <div class="rightSpace main_bc_color2 main_color2_text">&nbsp;</div>\n\
            </div>\n\
        <div class="ReportsBottomRow main_bc_color2 main_color2_text">&nbsp;</div>\n\
    </div>';

    if (div != 'mainBody') {
        html = loadingDiv ;
    }
    $('#'+div).html(html);
}
function doPulsate(div_id,count){
  var i = 0;
  function pulsate() {
    if(i >= count) return;
    $('#'+div_id).
      animate({opacity: 0.2}, 1000, 'linear').
      animate({opacity: 1}  , 1000, 'linear', pulsate);
    i++;
  }
  pulsate();
}

function mainDiv( page ){
    $.fx.speeds.xslow = 3000; // 'xslow' means 3 seconds
    loading_div('mainBody');
    var url = "?page="+page;
    $('#mainBody').load("ajax/mainDiv.php"+url);   
}
function paging(action,reportType){
    if (window.XMLHttpRequest)  {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        }
    else    {// code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }
    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
            
            showPaging(action);  ///  Updates both top and bottom div for paging

            if ( reportType.startsWith("ItemsReport_") ) {
                loading_div(reportType+'BodyCenter')
                ReportData(1,reportType);  ///  Call Function to reload report_data div.
            }
            if ( reportType.startsWith("SalesReport") || reportType.startsWith("DailyInventoryReport") ) {
                loading_div(reportType+'BodyCenter')
                SalesData(1,reportType);  ///  Call Function to reload report_data div.
            }
            if ( reportType.startsWith("Profiles_") ) {
                loading_div(reportType+'BodyCenter')
                ProfilesData(1,reportType);  ///  Call Function to reload report_data div.
            }
            if ( reportType.startsWith("Customers_") ) {
                loading_div(reportType+'BodyCenter')
                CustomersData(1,reportType);  ///  Call Function to reload report_data div.
            }
            if ( reportType.startsWith("Inventory_") || reportType.startsWith("Deliveries_") || reportType.startsWith("item_") ) {
                loading_div(reportType+'BodyCenter');
                InventoryData(1,reportType);  ///  Call Function to reload report_data div.
            }
            if ( reportType.startsWith("Companies_") ) {
                loading_div(reportType+'BodyCenter')
                CompaniesData(1,reportType);  ///  Call Function to reload report_data div.
            }
            if ( reportType.startsWith("Mailer_") ) {
                loading_div(reportType+'BodyCenter')
                MailerData(1,reportType);  ///  Call Function to reload report_data div.
            }
            if ( reportType.startsWith("Jobs_") ) {
                loading_div(reportType+'BodyCenter')
                JobsData(1,reportType);  ///  Call Function to reload report_data div.
            }
        }
    }
    var url =  "?action=" + action;
    xmlhttp.open("GET","ajax/paging.php"+url,true);
    xmlhttp.send();
}
function showPaging(){
    if (window.XMLHttpRequest)  {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        }
    else    {// code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }
    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
            document.getElementById('listing_search_paging_top').innerHTML    = xmlhttp.responseText;
            document.getElementById('listing_search_paging_bottom').innerHTML = xmlhttp.responseText;
        }
    }
    xmlhttp.open("GET","ajax/showPaging.php",true);
    xmlhttp.send();
}

function showNotification(params){
    // options array
    var options = { 
        'showAfter': 0, // number of sec to wait after page loads
        'duration': 0, // display duration
        'autoClose' : false, // flag to autoClose notification message
        'type' : 'success', // type of info message error/success/info/warning
        'message': '', // message to dispaly
        'link_notification' : '', // link flag to show extra description
        'description' : '' // link to desciption to display on clicking link message
    }; 
    // Extending array from params
    $.extend(true, options, params);
    
    var msgclass = 'succ_bg'; // default success message will shown
    if(options['type'] == 'error'){
        msgclass = 'error_bg'; // over write the message to error message
    } else if(options['type'] == 'information'){
        msgclass = 'info_bg'; // over write the message to information message
    } else if(options['type'] == 'warning'){
        msgclass = 'warn_bg'; // over write the message to warning message
    } 
    
    // Parent Div container
    var container = '<div id="info_message" class="'+msgclass+'"><div class="center_auto"><div class="info_message_text message_area">';
    container += options['message'];
    container += '</div><div class="info_close_btn button_area" onclick="return closeNotification()"></div><div class="clearboth"></div>';
    container += '</div><div class="info_more_descrption"></div></div>';
    
    $notification = $(container);
    
    // Appeding notification to Body
    $('body').append($notification);
    
    var divHeight = $('div#info_message').height();
    // see CSS top to minus of div height
    $('div#info_message').css({
        top : '-'+divHeight+'px'
    });
    
    // showing notification message, default it will be hidden
    $('div#info_message').show();
    
    // Slide Down notification message after startAfter seconds
    slideDownNotification(options['showAfter'], options['autoClose'],options['duration']);
    
    $('.link_notification').live('click', function(){
        $('.info_more_descrption').html(options['description']).slideDown('fast');
    });
    
}
// function to close notification message
// slideUp the message
function closeNotification(duration){
    var divHeight = $('div#info_message').height();
    setTimeout(function(){
        $('div#info_message').animate({
            top: '-'+divHeight
        }); 
        // removing the notification from body
        setTimeout(function(){
            $('div#info_message').remove();
        },200);
    }, parseInt(duration * 1000));   
    

    
}
// sliding down the notification
function slideDownNotification(startAfter, autoClose, duration){    
    setTimeout(function(){
        $('div#info_message').animate({
            top: 0
        }); 
        if(autoClose){
            setTimeout(function(){
                closeNotification(duration);
            }, duration);
        }
    }, parseInt(startAfter * 1000));    
}




function orderBy(column,reportType){
var url =  "?column=" + column;
$.ajax({
      url: 'ajax/orderBy.php'+url,
      type: 'GET',
      beforeSend: function() {
           // show indicator
      },
      complete: function() {
          // hide indicator
      },
      success: function() {
        if ( reportType.startsWith("ItemsReport_") ) {
            ReportData(1,reportType);  ///  Call Function to reload report_data div.
        }
        if ( reportType.startsWith("SalesReport") ) {
            SalesData(1,reportType);  ///  Call Function to reload report_data div.
        }
        if ( reportType.startsWith("Profiles_") ) {
            ProfilesData(1,reportType);  ///  Call Function to reload report_data div.
        }
        if ( reportType.startsWith("Customers_") ) {
            CustomersData(1,reportType);  ///  Call Function to reload report_data div.
        }
        if ( reportType.startsWith("Inventory_") || reportType.startsWith("Deliveries_") || reportType.startsWith("item_") ) {
            InventoryData(1,reportType);  ///  Call Function to reload report_data div.
        }
        if ( reportType.startsWith("Companies_") ) {
            CompaniesData(1,reportType);  ///  Call Function to reload report_data div.
        }
        if ( reportType.startsWith("Jobs_") ) {
            JobsData(1,reportType);  ///  Call Function to reload report_data div.
        }
      }
});
}
function listingAdvancedSearch(dynamic_pannel){
    var VALUE_dynamic_pannel_advanced_search = document.getElementById('dynamic_pannel_advanced_search').style.display ;
    if ( VALUE_dynamic_pannel_advanced_search == "none" ){
        document.getElementById('dynamic_pannel_advanced_search').style.display = 'block';
        document.getElementById('dynamic_pannel_advanced_toggle').style.display = 'none';
    }
    if ( VALUE_dynamic_pannel_advanced_search == "block" ){
        document.getElementById('dynamic_pannel_advanced_search').style.display = 'none';
        document.getElementById('dynamic_pannel_advanced_toggle').style.display = '';
    }
}
function isValidDate(txtDate) {
      var objDate,  // date object initialized from the txtDate string
          mSeconds, // txtDate in milliseconds
          day,      // day
          month,    // month
          year;     // year
     // date length should be 10 characters (no more no less)
     if (txtDate.length !== 10) {
           return false;
     }
     // third and sixth character should be '/'
     if (txtDate.substring(4, 5) !== '-' || txtDate.substring(7, 8) !== '-') {
          return false;
     }
     // extract month, day and year from the txtDate (expected format is mm/dd/yyyy  yyyy/mm/dd)
     // subtraction will cast variables to integer implicitly (needed
     // for !== comparing)
     //month  = txtDate.substring(0, 2) - 1; // because months in JS start from 0
     //day    = txtDate.substring(3, 5) - 0;
     //year   = txtDate.substring(6, 10) - 0;
     year   = txtDate.substring(0, 4) - 0;
     day    = txtDate.substring(8, 10) - 0;
     month  = (txtDate.substring(5, 7) - 0) - 1;
     if (year < 1000 || year > 3000) {
        // test year range
        return false;
     }
     // convert txtDate to milliseconds
     mSeconds = (new Date(year, month, day)).getTime();
     // initialize Date() object from calculated milliseconds
   objDate = new Date();
    objDate.setTime(mSeconds);
    // compare input date and parts from Date() object
   // if difference exists then date isn't valid
   if (objDate.getFullYear() !== year ||
       objDate.getMonth() !== month ||
       objDate.getDate() !== day) {
      return false;
    }
    // otherwise return true
    return true;
}
function isValidCurrencyFormat(curr_value)
{
    var message = "";
    var Amnt = curr_value;
    var objRegExp = /^[a-z ]+$/i;
    Amnt = Amnt.replace(/^\s*|\s*$/g,"");

    if(Amnt.length !=0 && objRegExp.test(Amnt ))
    {
        message = "Amount should be a valid currency value";
        return{returnStatus:0,message:message};
    }
return{returnStatus:1,message:message};
}
function IsNumeric(sText){
    if (isNaN(parseFloat(sText))) {
        return 0;
     }
        return 1;
}
function validatePwd(password,password2) {
var invalid = " "; // Invalid character is a space
var minLength = 8; // Minimum length
var pw1 = password ;
var pw2 = password2;
// check for a value in both fields.
if (pw1 == '' || pw2 == '') {
message = 'Please enter your password twice.';
return{returnStatus:0,message:message};
}
// check for minimum length
if (password.length < minLength) {
message = 'Your password must be at least ' + minLength + ' characters long. Try again.';
return{returnStatus:0,message:message};
}
// check for spaces
if (password.indexOf(invalid) > -1) {
message = 'Sorry, spaces are not allowed.';
return{returnStatus:0,message:message};
}
else {
if (pw1 != pw2) {
message = 'You did not enter the same new password twice. Please re-enter your password.';
return{returnStatus:0,message:message};
}
else {
message = 'Password updated Successfully!';
return{returnStatus:1,message:message};
      }
   }
}
function validateEmail(email_address) {
    var error="";
    var tfld = trim(email_address);                        // value of field with whitespace trimmed off
    var emailFilter = /^[^@]+@[^@.]+\.[^@]*\w\w$/ ;
    var illegalChars= /[\(\)\<\>\,\;\:\\\"\[\]]/ ;

    if (email_address == "") {
        //email_address.style.background = 'Yellow';
        error = "You didn't enter an email address.\n";
    } else if (!emailFilter.test(tfld)) {              //test email for illegal characters
        //email_address.style.background = 'Yellow';
        error = "Please enter a valid email address.\n";
    } else if (email_address.match(illegalChars)) {
        //email_address.style.background = 'Yellow';
        error = "The email address contains illegal characters.\n";
    } else {
        //email_address.style.background = 'White';
    }
    return error;
}


function AppointmentCalendar_Display_ChangeDate(date){
    $.fx.speeds.xslow = 3000; // 'xslow' means 3 seconds
    var url = "?date="+date;
    loading_div('mainBody');
    $("#mainBody").load("ajax/Appointments/AppointmentCalendar_Display_ChangeDate.php"+url);
}
function AppointmentCalendar_Display_ShowAppointment_info(appointment_id,div_name){
    var url = "?appointment_id="+appointment_id;
    loading_div(div_name);
    $("#"+div_name).load("ajax/Appointments/AppointmentCalendar_Display_ShowAppointment_info.php"+url);
}
function AppointmentCalendar_Open_Slot_Book(staff_id,selected_date,selected_time,appointment_slot_interval,div_name){
    AttributesData = {
    "action"                   : 'AppointmentCalendar_Open_Slot_Book',
    "staff_id"                 : staff_id,
    "selected_date"            : selected_date,
    "selected_time"            : selected_time,
    "appointment_slot_interval": appointment_slot_interval
};

    if ( this.valid != "false") {
        $.ajax({
        type    : 'Post',
            url     : 'ajax/Appointments/AppointmentCalendar_Open_Slot_Book.php',
            data    : AttributesData,
            dataType: 'json',
            success:function(data){
                    if (data.returnCode == 0) {
                        //$('#InventoryMgmtBodyCenter').attr("style", "display:none");
                    }
                    else {
                        $('#'+div_name).html(data.html);
                    }
            },
            error : function(data,XMLHttpRequest, textStatus, errorThrown) {
                    $('#Inventory_Items_SubmitEdit_or_Add_result').html('There was an ERROR.\n').delay(2000);
            }
        });
    }
}
function AppointmentCalendar_Open_Slot_Book_Set_UnSet(staff_id,action,selected_date,selected_time,appointment_slot_interval){
        var open_slot_div           = 'Calendar_Employees_Open_Slot_Available_Icon_' + staff_id;
        var appointment_div         = 'ChooseApttime_' + selected_date + '_' + selected_time + "_" + staff_id;
        var appointment_div_slot    = 'ChooseApttime_' + selected_date + '_' + selected_time + "_" + staff_id + '_slot';

        var Data        = {
                        "AppointmentCalendar_Open_Slot_Book_Set_UnSet": 1,
                        "staff_id": staff_id,
                        "action": action,
                        "appointment_slot_interval": appointment_slot_interval,
                        "selected_date": selected_date,
                        "selected_time": selected_time
                        }
        $.ajax({
            type : 'Post',
            url : 'ajax/Appointments/AppointmentCalendar_Open_Slot_Book_Set_UnSet.php',
            data: Data,
            dataType : 'json',
            success:function(data){
                    if (data.status == 1) {
                       document.getElementById(open_slot_div).innerHTML = '<img src="../common_includes/includes/images/Drink_BeerBottle.png"    height="12" width="12" alt="Slot Off" title="Slot Off">';
                       document.getElementById(open_slot_div).setAttribute("onclick","AppointmentCalendar_Open_Slot_Book_Set_UnSet("+staff_id+",0,'"+selected_date+"','"+selected_time+"',"+appointment_slot_interval+");");
                       $('#'+appointment_div).removeClass("ChooseApt_MouseOut");
                       $('#'+appointment_div).addClass("ChooseApt_EXPIRED");
                       $('#'+appointment_div_slot).html('Appt Slot <br> Off');

                    } else {
                       document.getElementById(open_slot_div).innerHTML = '<img src="../common_includes/includes/images/work.png"                height="12" width="12" alt="Work" title="Work">';
                       document.getElementById(open_slot_div).setAttribute("onclick","AppointmentCalendar_Open_Slot_Book_Set_UnSet("+staff_id+",1,'"+selected_date+"','"+selected_time+"',"+appointment_slot_interval+");");
                       $('#'+appointment_div).removeClass("ChooseApt_EXPIRED");
                       $('#'+appointment_div).addClass("ChooseApt_MouseOut");
                       $('#'+appointment_div_slot).html(data.dateTimeStart_massaged);
                    }
            },
            error : function(XMLHttpRequest, textStatus, errorThrown) {
                    //$('#Inventory_Items_SubmitNewItem_result').html('There was an error.').delay(2000);
            }
        });
}


function Show_Upcoming_Appts_Calendar_View(div_name){
    loading_div(div_name);
    $("#"+div_name).load("ajax/Appointments/Show_Upcoming_Appts_Calendar_View.php");
}

function Company_AddCompany() {
    $.fx.speeds.xslow = 3000; // 'xslow' means 3 seconds
    var url = "?CompanyAdd=1";
    $("#mainBody").load("ajax/Companies/Companies_AddCompany.php"+url).hide().fadeIn(2000);
}
function Company_editCompany(company_id){
var url = "?edit_company=1&company_id="+company_id;
    $("#mainBody").load("ajax/Companies/Companies_editCompany.php"+url).hide().fadeIn(2000);
}
function Companies_ActiveLoginTabs(ActiveTab){
    $.fx.speeds.xslow = 3000; // 'xslow' means 3 seconds
    var url = "?ActiveTab="+ActiveTab;
    $("#mainBody").load("ajax/Companies/Companies_ActiveLoginTabs.php"+url).hide().fadeIn(2000);
}
function companies_UpdSettings(company_id,setting){
    var myValue = document.getElementById(setting).value;
    var valid;
    var url =  "?company_id=" + company_id +
        "&setting=" + encodeURIComponent(setting) +
        "&value=" + encodeURIComponent(myValue);

    if ( myValue == "" || myValue.length < 1 ) {
            valid = "false";
        }
    if ( valid != "false") {
        $.fx.speeds.xslow = 3000; // 'xslow' means 3 seconds
        $("#mainBody").load("ajax/Companies/Companies_UpdSettings.php"+url).hide().fadeIn(2000);
    }
}
function Companies_SubmitNewCompany(){
    this.valid = "true";
    var name                    = validateAttribute(["length_lt_2","existingCompany"],'name','nokeep');
    var domain                  = validateAttribute(["length_lt_2","existingDomainName"],'domain','nokeep');
    var subdomain               = validateAttribute(["length_lt_2","existingSubDomain"],'subdomain','nokeep');
    var templateType            = validateAttribute([],'templateType','nokeep');
    var defaultPOS              = validateAttribute([],'defaultPOS','nokeep');

    AttributesData          = {
    "action"                : 'AddCompany',
    "name"                  : name[0],              "keep_name": name[1],
    "domain"                : domain[0],            "keep_domain" : domain[1],
    "subdomain"             : subdomain[0],         "keep_subdomain" : subdomain[1],
    "templateType"          : templateType[0],      "keep_templateType" : templateType[1],
    "defaultPOS"            : defaultPOS[0],        "keep_defaultPOS" : defaultPOS[1]
    };

    if ( this.valid != "false") {
        $.ajax({
            type : 'Post',
            url : 'ajax/Companies/Companies_SubmitNewCompany.php',
            data: AttributesData,
            dataType : 'json',
            success:function(data){
                        $('#mainBody').html(data.html).fadeIn(5000);
            },
            error : function(data,XMLHttpRequest, textStatus, errorThrown) {
                    $('#XXXXXXXXXXXXXXXX').html('There was an ERROR.\n').delay(2000);
            }
        });
    }
}
function Companies_showTemplateTab_byID(templateId,company_id){
    PassedInData          = {
    "templateId"         : templateId,
    "company_id"         : company_id
    };
        $.ajax({
            type : 'Post',
            url : 'ajax/Companies/Companies_showTemplateTab_byID.php',
            data: PassedInData,
            dataType : 'json',
            success:function(data){
                        $('#Companies_showTemplateTab_byID').html(data.html).fadeIn(5000);
            },
            error : function(data,XMLHttpRequest, textStatus, errorThrown) {
                    $('#XXXXXXXXXXXXXXXX').html('There was an ERROR.\n').delay(2000);
            }
        });    
}
function Companies_UpdTemplateData_byID(TemplateDataID,TemplateTabId,TemplateTabGroupID,Company_ID){
    this.valid = "true";
    var TemplateTabData     = validateAttribute(["length_lt_1"],TemplateTabId,'nokeep');
    AttributesData          = {
    "action"                : 'updateTabData',
    "TemplateDataID"        : TemplateDataID,
    "Company_ID"            : Company_ID,
    "TemplateTabId"         : TemplateTabId,
    "TemplateTabGroupID"    : TemplateTabGroupID,
    "TemplateTabData"       : TemplateTabData[0],              "keep_TemplateTabId": TemplateTabData[1]
    };

    if ( this.valid != "false") {
        $.ajax({
            type : 'Post',
            url : 'ajax/Companies/Companies_UpdTemplateData_byID.php',
            data: AttributesData,
            dataType : 'json',
            success:function(data){
                        $('#edit_TemplateTabGroupData_'+TemplateTabGroupID).html(data.html).fadeIn(5000);
            },
            error : function(data,XMLHttpRequest, textStatus, errorThrown) {
                    $('#XXXXXXXXXXXXXXXX').html('There was an ERROR.\n').delay(2000);
            }
        });
    }
}
function Companies_CreateNewTemplateTab_by_GroupID(TemplateTabGroupID,company_id){
 this.valid = "true";
    var name                = validateAttribute(["length_lt_5","ExistingTemplateTabGroupName"],'name_'+TemplateTabGroupID,'nokeep');
    var dataType            = validateAttribute(["length_lt_2"],'dataType_'+TemplateTabGroupID,'nokeep');
    var defaultValue        = validateAttribute(["length_lt_5"],'DefaultValue_'+TemplateTabGroupID,'nokeep');
    TemplateTabDataSet      = {
        "action"                : 'AddNewTemplateTab_by_GroupID',
        "company_id"            : company_id,
        "TemplateTabGroupID"    : TemplateTabGroupID,
        "name"                  : name[0],              "keep_name": name[1],
        "dataType"              : dataType[0],          "keep_dateType": dataType[1],
        "DefaultValue"          : defaultValue[0],      "keep_DefaultValue": defaultValue[1]
    };

    if ( this.valid != "false") {
        $.ajax({
            type : 'Post',
            url : 'ajax/Companies/Companies_CreateNewTemplateTab_by_GroupID.php',
            data: TemplateTabDataSet,
            dataType : 'json',
            success:function(data){
                        $('#edit_TemplateTabGroupData_'+TemplateTabGroupID).html(data.html).fadeIn(5000);
            },
            error : function(data,XMLHttpRequest, textStatus, errorThrown) {
                    $('#XXXXXXXXXXXXXXXX').html('There was an ERROR.\n').delay(2000);
            }
        });
    }    
}
function Companies_CompaniesSearch_searchBy(reportType){
    var url =  "?reportType=" + reportType ;
    var company_search_name            = document.getElementById('dynamic_pannel_name').value;
    var company_search_domain          = document.getElementById('dynamic_pannel_domain').value;
    var company_search_subdomain       = document.getElementById('dynamic_pannel_subdomain').value;
    var company_search_master_email    = document.getElementById('dynamic_pannel_master_email').value;

    if ( company_search_name != "") {
            url += "&company_search_name=" + encodeURIComponent(company_search_name);
    } else {url += "&company_search_name=-1";}
    if ( company_search_domain != "") {
            url += "&company_search_domain=" + encodeURIComponent(company_search_domain);
    } else {url += "&company_search_domain=-1";}
    if ( company_search_subdomain != "") {
            url += "&company_search_subdomain=" + encodeURIComponent(company_search_subdomain);
    } else {url += "&company_search_subdomain=-1";}
    if ( company_search_master_email != "") {
            url += "&company_search_master_email=" + encodeURIComponent(company_search_master_email);
    } else {url += "&company_search_master_email=-1";}

    $.ajax({
          url: 'ajax/Companies/Companies_CompaniesSearch_searchBy.php'+url,
          type: 'GET',
          success: function() {
            loading_div(reportType+'BodyCenter');
            if ( reportType.startsWith("Companies_") ) {
                CompaniesData(1,reportType);  ///  Call Function to reload report_data div.
            }
          }
    });
}




function Deliveries_createDelivery() {
    if (window.XMLHttpRequest)  {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        }
    else    {// code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }
    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
            document.getElementById('mainBody').innerHTML = xmlhttp.responseText;
        }
    }
var delivery_supplier_id    = document.getElementById('delivery_supplier_id').value ;
var url                     = "?createDelivery=1&supplier_id="+delivery_supplier_id;
if (delivery_supplier_id != 0) {
    xmlhttp.open("GET","ajax/Deliveries/Deliveries_createDelivery.php"+url,true);
    xmlhttp.send();
    }
    else {
        document.getElementById('delivery_supplier_id').className += " red";
    }
}
function add_deleteDeliveryItem(item_id){
    var valid = 'true';
    if (window.XMLHttpRequest)  {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        }
    else    {// code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }

    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
            document.getElementById('showItems_for_Delivery').innerHTML = xmlhttp.responseText;
            Deliveries_TotalsSummary();
        }
    }
quantity    = document.getElementById('delivery_NewItem_quantity_'+item_id).value ;
buy_price   = document.getElementById('delivery_NewItem_buy_price_'+item_id).value ;
sell_price  = document.getElementById('delivery_NewItem_sell_price_'+item_id).value ;

var url = "?item_id=" + item_id +
    "&quantity="+quantity+
    "&buy_price="+buy_price+
    "&sell_price="+sell_price;

if ( quantity == "" || quantity <= 0 ) {
    document.getElementById('delivery_NewItem_quantity_'+item_id).className += " bcTextBoxErr";
    valid = "false";
    }
else { document.getElementById('delivery_NewItem_quantity_'+item_id).className = "w20"; }

if ( buy_price == "" ) {
    document.getElementById('delivery_NewItem_buy_price_'+item_id).className += " bcTextBoxErr";
    valid = "false";
    }
else { document.getElementById('delivery_NewItem_buy_price_'+item_id).className = "w40"; }

if ( sell_price == "" ) {
    document.getElementById('delivery_NewItem_sell_price_'+item_id).className += " bcTextBoxErr";
    valid = "false";
    }
else { document.getElementById('delivery_NewItem_sell_price_'+item_id).className = "w40"; }

if ( valid != "false") {
    document.getElementById('delivery_NewItem_action_img_'+item_id).src     ='/common_includes/includes/images/checkbox_red_med.jpg';
    document.getElementById('delivery_NewItem_action_img_'+item_id).title   ="Add this item to delivery";
    xmlhttp.open("GET","ajax/Deliveries/add_deleteDeliveryItem.php"+url,true);
    xmlhttp.send();
    }
}
function createNewItemByStyleNumber(style_number){
    var valid = 'true';
    if (window.XMLHttpRequest)  {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        }
    else    {// code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }

    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
            document.getElementById('showItems_for_Delivery').innerHTML = xmlhttp.responseText;
            //reload the DIV showing the items of the particular style
            Deliveries_selectStyleNumber();
            //Deliveries_TotalsSummary();
        }
    }
if ( document.getElementById('delivery_CreateItem_quantity').value == "" ||
    document.getElementById('delivery_CreateItem_quantity').value <= 0 ) {
    document.getElementById('delivery_CreateItem_quantity').className += " bcTextBoxErr";
    valid = "false";
    }
else { document.getElementById('delivery_CreateItem_quantity').className = "w20"; }

if ( document.getElementById('delivery_CreateItem_buy_price').value == "" ) {
    document.getElementById('delivery_CreateItem_buy_price').className += " bcTextBoxErr";
    valid = "false";
    }
else { document.getElementById('delivery_CreateItem_buy_price').className = "w40"; }

if ( document.getElementById('delivery_CreateItem_sell_price').value == "" ) {
    document.getElementById('delivery_CreateItem_sell_price').className += " bcTextBoxErr";
    valid = "false";
    }
else { document.getElementById('delivery_CreateItem_sell_price').className = "w40"; }

if ( document.getElementById('delivery_CreateItem_attribute1').value == "" ) {
    document.getElementById('delivery_CreateItem_attribute1').className += " bcTextBoxErr";
    valid = "false";
    }
else { document.getElementById('delivery_CreateItem_attribute1').className = "w60"; }

if ( document.getElementById('delivery_CreateItem_attribute2').value == "" ) {
    document.getElementById('delivery_CreateItem_attribute2').className += " bcTextBoxErr";
    valid = "false";
    }
else { document.getElementById('delivery_CreateItem_attribute2').className = "w60"; }



if ( valid != "false") {
    var quantity    = document.getElementById('delivery_CreateItem_quantity').value ;
    var buy_price   = eval(document.getElementById('delivery_CreateItem_buy_price').value) ;
    var sell_price  = eval(document.getElementById('delivery_CreateItem_sell_price').value) ;
    var attribute1  = document.getElementById('delivery_CreateItem_attribute1').value ;
    var attribute2  = document.getElementById('delivery_CreateItem_attribute2').value ;
    buy_price       = buy_price.toFixed(2);
    sell_price      = sell_price.toFixed(2);

    var url = "?style_number=" + style_number +
    "&quantity="+quantity+
    "&buy_price="+buy_price+
    "&sell_price="+sell_price+
    "&attribute1="+attribute1+
    "&attribute2="+attribute2
    ;

    xmlhttp.open("GET","ajax/Deliveries/createNewItemByStyleNumber.php"+url,true);
    xmlhttp.send();
    }
}
function Deliveries_PendingItem_increase_decrease(action,item_id) {
    if (window.XMLHttpRequest)  {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        }
    else    {// code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }

    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
            document.getElementById('showItems_for_Delivery').innerHTML = xmlhttp.responseText;
            Deliveries_TotalsSummary();
        }
    }
        var url = "?item_id="+item_id+"&action="+action;
        xmlhttp.open("GET","ajax/Deliveries/Deliveries_increase_decreasePendingItem.php"+url,true);
        xmlhttp.send();
}
function Deliveries_deliveryDetails(delivery_id) {
    if (window.XMLHttpRequest)  {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        }
    else    {// code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }
    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
            document.getElementById('mainBody').innerHTML = xmlhttp.responseText;
        }
    }
    var url = "?delivery_id="+delivery_id;
    xmlhttp.open("GET","ajax/Deliveries/Deliveries_deliveryDetails.php"+url,true);
    xmlhttp.send();
}
function Deliveries_TotalsSummary(){
    //alert(delivery_id);
    if (window.XMLHttpRequest)  {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        }
    else    {// code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }
    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
            document.getElementById('Deliveries_TotalsSummary').innerHTML = xmlhttp.responseText;
        }
    }
    xmlhttp.open("GET","ajax/Deliveries/Deliveries_TotalsSummary.php",true);
    xmlhttp.send();
}
function Deliveries_selectStyleNumber() {
    var selectStyleNumber = document.getElementById('selectStyleNumber').value;
    if (window.XMLHttpRequest)  {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        }
    else    {// code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }

    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
            document.getElementById('Deliveries_ItemsPerCurrentStyleNumber').innerHTML = xmlhttp.responseText;
        }
    }
var url = "?style_number="+selectStyleNumber;
xmlhttp.open("GET","ajax/Deliveries/Deliveries_selectStyleNumber.php"+url,true);
xmlhttp.send();
}
function Deliveries_UpdateDeliveryInfo(PrepToSubmit,user_email){
var valid;
var varReturn = 0;
if (window.XMLHttpRequest)  {// code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp=new XMLHttpRequest();
    }
else    {// code for IE6, IE5
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }

xmlhttp.onreadystatechange=function() {
    if (xmlhttp.readyState==4 && xmlhttp.status==200) {
    Deliveries_TotalsSummary();
    mainDiv('Deliveries_AllDeliveries');
    }
}
var delivery_ordered        = document.getElementById('delivery_ordered').value ;
var delivery_invoice_no     = document.getElementById('delivery_invoice_no').value ;
var delivery_delivered_via  = document.getElementById('delivery_delivered_via').value ;
var delivery_shipped        = document.getElementById('delivery_shipped').value ;
var delivery_shipping_costs = document.getElementById('delivery_shipping_costs').value ;
var delivery_received       = document.getElementById('delivery_received').value ;
var delivery_purchase_order_no    = document.getElementById('delivery_purchase_order_no').value ;
var delivery_receiver_id    = document.getElementById('delivery_receiver_id').value ;


var url =  "?updateDeliveryInfo=" + 1 +
    "&ordered=" + delivery_ordered +
    "&invoice_no=" + delivery_invoice_no +
    "&delivered_via=" + delivery_delivered_via +
    "&shipped=" + delivery_shipped +
    "&shipping_costs=" + delivery_shipping_costs +
    "&received=" + delivery_received +
    "&purchase_order_no=" + delivery_purchase_order_no +
    "&receiver_id=" + delivery_receiver_id;

    //"&NU_phone_num=" + NU_phone_num;
// 1st Line //
if ( isValidDate(delivery_ordered,'yyyy/mm/dd')==false ) {
    document.getElementById('failed_delivery_ordered').className += " red";
    valid = "false";
    }
else { document.getElementById("failed_delivery_ordered").className = "left bold s08 pl10"; }
if ( delivery_invoice_no == "" ) {
    document.getElementById('failed_delivery_invoice_no').className += " red";
    valid = "false";
    }
else { document.getElementById("failed_delivery_invoice_no").className = "left bold s08"; }

// 2nd Line //
if ( delivery_delivered_via == "" ) {
    document.getElementById('failed_delivery_delivered_via').className += " red";
    valid = "false";
    }
else { document.getElementById("failed_delivery_delivered_via").className = "left bold s08"; }
if ( isValidDate(delivery_shipped,'yyyy/mm/dd')==false ) {
    document.getElementById('failed_delivery_shipped').className += " red";
    valid = "false";
    }
else { document.getElementById("failed_delivery_shipped").className = "left bold s08 pl10"; }

// 3rd Line //
if ( delivery_shipping_costs == "" || isValidCurrencyFormat(delivery_shipping_costs).returnStatus == 0 ) {
    document.getElementById('failed_delivery_shipping_costs').className += " red";
    valid = "false";
    }
else { document.getElementById("failed_delivery_shipping_costs").className = "left bold s08"; }

// 4th Line //
if ( isValidDate(delivery_received,'yyyy/mm/dd')==false ) {
    document.getElementById('failed_delivery_received').className += " red";
    valid = "false";
    }
else { document.getElementById("failed_delivery_received").className = "left bold s08 pl10"; }
if ( delivery_receiver_id == "0" ) {
    document.getElementById('failed_delivery_receiver_id').className += " red";
    valid = "false";
    }
else { document.getElementById("failed_delivery_receiver_id").className = "left bold s08"; }

// 5th Line //
if ( delivery_purchase_order_no == "" ) {
    document.getElementById('failed_delivery_purchase_order_no').className += " red";
    valid = "false";
    }
else { document.getElementById("failed_delivery_purchase_order_no").className = "left bold s08"; }


if ( valid != "false") {
    xmlhttp.open("GET","ajax/Deliveries/updateDeliveryInfo.php"+url,true);
    xmlhttp.send();
    varReturn = 1;
    }
    return(varReturn);
}
function Deliveries_Cancel_Delivery() {
    var cancel = 0;
    if (confirm("Do you really want to cancel this delivery?"))
        { cancel = 1; }

    //alert(page);
    if (window.XMLHttpRequest)  {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        }
    else    {// code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }

    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
            document.getElementById('mainBody').innerHTML = xmlhttp.responseText;

        }
    }
    if ( cancel == "1") {
        var url = "?cancelDelivery=1";
        xmlhttp.open("GET","ajax/Deliveries/Deliveries_Cancel_Delivery.php"+url,true);
        xmlhttp.send();
    }
}
function Deliveries_AddNewDelivery() {
    var add = 0;
    var InfoVerified = 0;
    InfoVerified = Deliveries_UpdateDeliveryInfo();
    if ( InfoVerified != 1 ) { alert('You must fill in the "Delivery Details" info first.');return}
    if (confirm("Are you serious? Is it done? Once you confirm, there is no way to edit it"))
        { add = 1; }

    //alert(page);
    if (window.XMLHttpRequest)  {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        }
    else    {// code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }

    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
            document.getElementById('mainBody').innerHTML = xmlhttp.responseText;

        }
    }

    if ( add == 1) {
        var url = "?createDelivery=1";
        xmlhttp.open("GET","ajax/Deliveries/Deliveries_AddNewDelivery.php"+url,true);
        xmlhttp.send();
    }
}


function editProfile_ServiceActivateDeActivate(service_id,login_id){
        ChooseService_service_id =  'ChooseService_'+service_id;
        var selectServiceData = {
                                  "updateServiceActivateDeActivate": 1,
                                  "service_id": service_id,
                                  "login_id": login_id
                                }
        $.ajax({
            type : 'Post',
            url : 'ajax/Profiles/editProfile_ServiceActivateDeActivate.php',
            data: selectServiceData,
            dataType : 'json',
            success:function(data){
                    if (data.status == 1) {
                        document.getElementById(ChooseService_service_id).className = "bcgreen white";
                        //$('#make_appointment_step1_profile_descriptions').html(data.html);
                    } else {
                        document.getElementById(ChooseService_service_id).className = "";
                        //$('#make_appointment_step1_profile_descriptions').html(data.html);
                    }
            },
            error : function(XMLHttpRequest, textStatus, errorThrown) {
                    //$('#Inventory_Items_SubmitNewItem_result').html('There was an error.').delay(2000);
            }
        });
}
function editProfile_employee_price(service_id,login_id){
        var login_service_default_price     = 'login_service_default_price_'+service_id;
        var login_service_employee_price    = 'login_service_employee_price_'+service_id;
        
        var ChooseService_service_id        = 'ChooseService_'+service_id;
        var employee_price                  = document.getElementById('employee_price_'+service_id).value;
        if(!$.isNumeric(employee_price)) {
            return;
        }
        var selectServiceData = { "updateServiceEmployeePrice": 1,
                                  "employee_price"  : employee_price,
                                  "service_id"      : service_id,
                                  "login_id"        : login_id
                                }                             
        $.ajax({
            type : 'Post',
            url : 'ajax/Profiles/editProfile_employee_price.php',
            data: selectServiceData,
            dataType : 'json',
            success:function(data){
                    if (data.status == 1) {
                        document.getElementById(ChooseService_service_id).className = "bcgreen white";
                        //$('#make_appointment_step1_profile_descriptions').html(data.html);
                    }
                    if (data.employee_price != 0) {
                        document.getElementById(login_service_default_price).classList.add("strikethrough");
                        $('#'+login_service_employee_price).html(data.employee_price);
                    } else if (data.employee_price == 0) {
                        document.getElementById(login_service_default_price).classList.remove("strikethrough");
                        $('#'+login_service_employee_price).html('&nbsp;');
                    }
                    
            },
            error : function(XMLHttpRequest, textStatus, errorThrown) {
                    //$('#Inventory_Items_SubmitNewItem_result').html('There was an error.').delay(2000);
            }
        });
}
function editProfile_AddAddressExistingUser(profiles_login_id){
    var valid;
    var NU_Address1   = document.getElementById('editProfile_employee_address1').value ;
    var NU_Address2   = document.getElementById('editProfile_employee_address2').value ;
    var NU_City       = document.getElementById('editProfile_employee_city').value ;
    var NU_State      = document.getElementById('editProfile_employee_state').value ;
    var NU_ZipCode    = document.getElementById('editProfile_employee_zipcode').value ;

    var url =  "?newAddress=" + 1 +
        "&NU_login_id=" + profiles_login_id +
        "&NU_Address1=" + encodeURIComponent(NU_Address1) +
        "&NU_Address2=" + encodeURIComponent(NU_Address2) +
        "&NU_City=" + encodeURIComponent(NU_City) +
        "&NU_State=" + encodeURIComponent(NU_State) +
        "&NU_ZipCode=" + encodeURIComponent(NU_ZipCode);
    if ( NU_Address1 == "" ) {
        document.getElementById('failed_register_message_NU_Address1').innerHTML = "<font color=red>street missing?</font>";
            valid = "false";
        }
        else { document.getElementById('failed_register_message_NU_Address1').innerHTML = "&nbsp;" }

    if ( NU_City == "" ) {
        document.getElementById('failed_register_message_NU_City').innerHTML = "<font color=red>city blank, u miss it?</font>";
            valid = "false";
        }
        else { document.getElementById('failed_register_message_NU_City').innerHTML = "&nbsp;" }

    if ( NU_ZipCode == "" || NU_ZipCode.length < 5 ) {
        document.getElementById('failed_register_message_NU_ZipCode').innerHTML = "<font color=red>zip code? 5 digits</font>";
            valid = "false";
        }
        else { document.getElementById('failed_register_message_NU_ZipCode').innerHTML = "&nbsp;" }


    if ( valid != "false") {
        $.fx.speeds.xslow = 3000; // 'xslow' means 3 seconds
        $("#mainBody").load("ajax/Profiles/editProfile_AddAddressExistingUser.php"+url).hide().fadeIn(2000);
    }
}
function editProfile_UpdateAddress(edit_address_address_id){
    var valid;
    var NU_Address1   = document.getElementById('editProfile_employee_address1').value ;
    var NU_Address2   = document.getElementById('editProfile_employee_address2').value ;
    var NU_City       = document.getElementById('editProfile_employee_city').value ;
    var NU_State      = document.getElementById('editProfile_employee_state').value ;
    var NU_ZipCode    = document.getElementById('editProfile_employee_zipcode').value ;

    var url =  "?editAddress=" + 1 +
        "&NU_edit_address_address_id=" + edit_address_address_id +
        "&NU_Address1=" + encodeURIComponent(NU_Address1) +
        "&NU_Address2=" + encodeURIComponent(NU_Address2) +
        "&NU_City=" + encodeURIComponent(NU_City) +
        "&NU_State=" + encodeURIComponent(NU_State) +
        "&NU_ZipCode=" + encodeURIComponent(NU_ZipCode);
    if ( NU_Address1 == "" ) {
        document.getElementById('failed_register_message_NU_Address1').innerHTML = "<font color=red>street missing?</font>";
            valid = "false";
        }
        else { document.getElementById('failed_register_message_NU_Address1').innerHTML = "&nbsp;" }

    if ( NU_City == "" ) {
        document.getElementById('failed_register_message_NU_City').innerHTML = "<font color=red>city blank, u miss it?</font>";
            valid = "false";
        }
        else { document.getElementById('failed_register_message_NU_City').innerHTML = "&nbsp;" }

    if ( NU_ZipCode == "" || NU_ZipCode.length < 5 ) {
        document.getElementById('failed_register_message_NU_ZipCode').innerHTML = "<font color=red>zip code? 5 digits</font>";
            valid = "false";
        }
        else { document.getElementById('failed_register_message_NU_ZipCode').innerHTML = "&nbsp;" }


    if ( valid != "false") {
        $.fx.speeds.xslow = 3000; // 'xslow' means 3 seconds
        $("#mainBody").load("ajax/Profiles/editProfile_UpdateAddress.php"+url).hide().fadeIn(2000);
    }
}
function editProfile_EditAddress_setAddressID(edit_address_address_id){
    $.fx.speeds.xslow = 3000; // 'xslow' means 3 seconds
    var url = "?edit_address_address_id="+edit_address_address_id;
    $("#mainBody").load("ajax/Profiles/editProfile_EditAddress_setAddressID.php"+url).hide().fadeIn(2000);
}
function editProfile_login(profiles_login_id){
    $.fx.speeds.xslow = 3000; // 'xslow' means 3 seconds
    var url = "?editProfile=1&profiles_login_id="+profiles_login_id;
    $("#mainBody").load("ajax/Profiles/editProfile_login.php"+url).hide().fadeIn(2000);
}
function editProfileLoginTabs(ActiveTab){
    $.fx.speeds.xslow = 3000; // 'xslow' means 3 seconds
    var url = "?ActiveTab="+ActiveTab;
    $("#mainBody").load("ajax/Profiles/editProfile_ActiveTab.php"+url).hide().fadeIn(2000);
}
function editProfile_UpdStatus(profiles_login_id,action){
var url = "?editProfile=1&profiles_login_id="+profiles_login_id+"&action="+action;
if (profiles_login_id != 0) {
    $("#mainBody").load("ajax/Profiles/editProfile_UpdStatus.php"+url).fadeIn(2000);
    }
}
function editProfile_UpdProfileAttributes(profiles_login_id){
    var valid;
    var varReturn =0;
    if (window.XMLHttpRequest)  {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        }
    else    {// code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }

    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
            document.getElementById('mainBody').innerHTML = xmlhttp.responseText;
        }
    }

var profile_login_firstname = document.getElementById('editProfile_login_firstname').value ;
var profile_login_lastname  = document.getElementById('editProfile_login_lastname').value ;
var profile_login_level     = document.getElementById('editProfile_login_level').value ;
var profile_employee_quote  = document.getElementById('editProfile_employee_quote').value ;
var profile_employee_bio    = document.getElementById('editProfile_employee_bio').value ;

var url = "?editProfile=ProfileAttributes&profiles_login_id="+profiles_login_id+
    "&firstname="     + profile_login_firstname +
    "&lastname="      + profile_login_lastname +
    "&employee_bio="  + profile_employee_bio +
    "&employee_quote="+ profile_employee_quote +
    "&level="         + profile_login_level ;

//#############
if ( profile_login_firstname == "" ) {
    document.getElementById('profileLoginSummary_firstname').className = "bcTextBoxErr f_left w130";
    valid = "false";
    }
else { document.getElementById("profileLoginSummary_firstname").className = "lightgray f_left w130"; }
//#############
if ( profile_login_lastname == "" ) {
    document.getElementById('profileLoginSummary_lastname').className = "bcTextBoxErr f_left w130";
    valid = "false";
    }
else { document.getElementById("profileLoginSummary_lastname").className = "lightgray f_left w130"; }

if ( valid != "false") {
    xmlhttp.open("GET","ajax/Profiles/editProfile_UpdProfileAttributes.php"+url,true);
    xmlhttp.send();
    varReturn = 1;
    }
    return(varReturn);
}
function editProfile_UpdProfilePassword(profiles_login_id){
    var valid;
    var varReturn =0;
    if (window.XMLHttpRequest)  {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        }
    else    {// code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }

    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
            document.getElementById('mainBody').innerHTML = xmlhttp.responseText;
            if (updateProfilePasswordStatus==1) {
                alert(message);
            }
        }
    }
var profile_login_password  = document.getElementById('editProfile_login_password').value ;
var profile_login_password2 = document.getElementById('editProfile_login_password2').value ;
var url = "?editProfile=ProfilePassword&profiles_login_id="+profiles_login_id+"&password=" + profile_login_password ;

if ( profile_login_password != "" || profile_login_password2 != "" ) {
    var validatePwdObj = validatePwd(profile_login_password,profile_login_password2);
    returnStatus    = validatePwdObj.returnStatus;
    message         = validatePwdObj.message;
    if (returnStatus == 0 ){
    document.getElementById('profileLoginSummary_password').className = "bcTextBoxErr f_left left w215 h20px textIndent15";
    document.getElementById('profileLoginSummary_password2').className= "bcTextBoxErr f_left left w215 h20px textIndent15";
        valid = "false";
        alert(message);
    }
    else {
        var updateProfilePasswordStatus = 1;
    }
}
if ( valid != "false") {
    xmlhttp.open("GET","ajax/Profiles/editProfile_UpdProfileAttributes.php"+url,true);
    xmlhttp.send();
    varReturn = 1;
    }
    return(varReturn);
}
function editProfile_UpdPhysicalAddress(profiles_login_id){
    //alert(profiles_login_id);
    var valid;
    var varReturn =0;
    if (window.XMLHttpRequest)  {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        }
    else    {// code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }

    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
            document.getElementById('mainBody').innerHTML = xmlhttp.responseText;
            editProfile_login(profiles_login_id);
        }
    }

var profile_login_email_address = document.getElementById('editProfile_login_email_address').value ;
var profile_login_gmail_username = document.getElementById('editProfile_login_gmail_username').value ;
var profile_login_gmail_password = document.getElementById('editProfile_login_gmail_password').value ;


var url = "?editProfile=ElectronicInfo&profiles_login_id="+profiles_login_id+
    "&email_address=" + profile_login_email_address +
    "&gmail_username="+ profile_login_gmail_username +
    "&gmail_password="+ profile_login_gmail_password ;

// Electronic Contact Info //
if ( profile_login_email_address == "" || validateEmail(profile_login_email_address) != "" ) {
    document.getElementById('profileLoginSummary_email_address').className = "bcTextBoxErr f_left left w430 h20px textIndent15";
    valid = "false";
    }
else { document.getElementById("profileLoginSummary_email_address").className = "f_left left bclightgray w430 h20px textIndent15"; }
if ( profile_login_gmail_username == "" ) {
    document.getElementById('profileLoginSummary_gmail_username').className = "bcTextBoxErr f_left left w430 h20px textIndent15";
    valid = "false";
    }
else { document.getElementById("profileLoginSummary_gmail_username").className = "f_left left bclightgray w430 h20px textIndent15"; }
if ( profile_login_gmail_password == "" ) {
    document.getElementById('profileLoginSummary_gmail_password').className = "bcTextBoxErr f_left left w430 h20px textIndent15";
    valid = "false";
    }
else { document.getElementById("profileLoginSummary_gmail_password").className = "f_left left bclightgray w430 h20px textIndent15"; }

if ( valid != "false") {
    xmlhttp.open("GET","ajax/Profiles/editProfile_UpdProfileAttributes.php"+url,true);
    xmlhttp.send();
    varReturn = 1;
    }
    return(varReturn);
}
function editProfile_UpdElectronicInfo(profiles_login_id){
    //alert(profiles_login_id);
    var valid;
    var varReturn =0;
    if (window.XMLHttpRequest)  {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        }
    else    {// code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }

    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
            document.getElementById('mainBody').innerHTML = xmlhttp.responseText;
            editProfile_login(profiles_login_id);
        }
    }

var profile_login_email_address = document.getElementById('editProfile_login_email_address').value ;
var profile_login_gmail_username = document.getElementById('editProfile_login_gmail_username').value ;
var profile_login_gmail_password = document.getElementById('editProfile_login_gmail_password').value ;


var url = "?editProfile=ElectronicInfo&profiles_login_id="+profiles_login_id+
    "&email_address=" + profile_login_email_address +
    "&gmail_username="+ profile_login_gmail_username +
    "&gmail_password="+ profile_login_gmail_password ;

// Electronic Contact Info //
if ( profile_login_email_address == "" || validateEmail(profile_login_email_address) != "" ) {
    document.getElementById('profileLoginSummary_email_address').className = "bcTextBoxErr f_left left w380 h20px textIndent15";
    valid = "false";
    }
else { document.getElementById("profileLoginSummary_email_address").className = "f_left left bclightgray w380 h20px textIndent15"; }

if ( profile_login_gmail_username == "" ) {
    document.getElementById('profileLoginSummary_gmail_username').className = "bcTextBoxErr f_left left w380 h20px textIndent15";
    valid = "false";
    }
else { document.getElementById("profileLoginSummary_gmail_username").className = "f_left left bclightgray w380 h20px textIndent15"; }

if ( profile_login_gmail_password == "" ) {
    document.getElementById('profileLoginSummary_gmail_password').className = "bcTextBoxErr f_left left w380 h20px textIndent15";
    valid = "false";
    }
else { document.getElementById("profileLoginSummary_gmail_password").className = "f_left left bclightgray w380 h20px textIndent15"; }

if ( valid != "false") {
    xmlhttp.open("GET","ajax/Profiles/editProfile_UpdProfileAttributes.php"+url,true);
    xmlhttp.send();
    varReturn = 1;
    }
    return(varReturn);
}
function editProfile_AddUser(){
    //alert(page);
    if (window.XMLHttpRequest)  {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        }
    else    {// code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }

    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
            document.getElementById('mainBody').innerHTML = xmlhttp.responseText;
        }
    }
var url = "?UserAdd=1";
    xmlhttp.open("GET","ajax/Profiles/editProfile_UserAdd.php"+url,true);
    xmlhttp.send();
}
function editProfile_AddNewUser() {
    var valid;
    if (window.XMLHttpRequest)  {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        }
    else    {// code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }
    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
            var xmlDoc = xmlhttp.responseXML;
            var responseElements = xmlDoc.getElementsByTagName("status")[0];
                // We know that the first child of show is title, and the second is rating
                    status  = xmlDoc.getElementsByTagName("status")[0].childNodes[0].nodeValue;
                    message = xmlDoc.getElementsByTagName("message")[0].childNodes[0].nodeValue;
                    if ( status == 1) {
                        document.getElementById('registerPanel').innerHTML =  message + "<br>Registration Successful, You may now login!<br>Thank You!";
                    }
                    else {
                        document.getElementById('failed_register_message_NU_login_name').innerHTML = message + ", " + document.getElementById('NU_login_name').value + "!";
                        //The password must be fine so make sure to clear the bad password message if it exists.
                        document.getElementById('failed_register_message_NU_password').innerHTML= '';
                        document.getElementById('div_NU_password').className        = "left w120";
                        document.getElementById('div_NU_password_confirm').className= "left w120";
                    }
            }
        }

    var NU_user_email = document.getElementById('NU_user_email').value ;
    var NU_first_name = document.getElementById('NU_first_name').value ;
    var NU_last_name  = document.getElementById('NU_last_name').value ;
    var NU_phone_num  = document.getElementById('NU_phone_num').value ;
    var NU_login_name = document.getElementById('NU_login_name').value ;
    var NU_password   = document.getElementById('NU_password').value ;
    var NU_password_confirm = document.getElementById('NU_password_confirm').value ;

    var NU_Address    = document.getElementById('NU_Address').value ;
    var NU_City       = document.getElementById('NU_City').value ;
    var NU_Country    = document.getElementById('NU_Country').value ;
    var NU_State      = document.getElementById('NU_State').value ;
    var NU_PostalCode = document.getElementById('NU_PostalCode').value ;

    var url =  "?newuser=" + 1 +
        "&NU_user_email=" + NU_user_email +
        "&NU_first_name=" + NU_first_name +
        "&NU_last_name=" + NU_last_name +
        "&NU_phone_num=" + NU_phone_num +
        "&NU_login_name=" + NU_login_name +
        "&NU_password=" + NU_password +
        "&NU_Address=" + NU_Address +
        "&NU_City=" + NU_City +
        "&NU_Country=" + NU_Country +
        "&NU_State=" + NU_State +
        "&NU_PostalCode=" + NU_PostalCode;

    if ( NU_first_name == "" ) {
        document.getElementById('failed_register_message_NU_first_name').innerHTML = "First Name: Something wrong, is it blank?";
            valid = "false";
        }
        else { document.getElementById('failed_register_message_NU_first_name').innerHTML = "" }
    if ( NU_last_name == "" ) {
        document.getElementById('failed_register_message_NU_last_name').innerHTML = "Last Name: Something wrong, is it blank?";
            valid = "false";
        }
        else { document.getElementById('failed_register_message_NU_last_name').innerHTML = "" }
    if ( NU_user_email == "" || validateEmail(NU_user_email) != "" ) {
        document.getElementById('failed_register_message_NU_user_email').innerHTML = "Something Wrong with email address.";
            valid = "false";
        }
        else { document.getElementById('failed_register_message_NU_user_email').innerHTML = "" }
    if ( NU_phone_num == "" ) {
        document.getElementById('failed_register_message_NU_phone_num').innerHTML = "Something Wrong the phone number.";
            valid = "false";
        }
        else { document.getElementById('failed_register_message_NU_phone_num').innerHTML = "" }
    if ( NU_login_name == "" ) {
        document.getElementById('failed_register_message_NU_login_name').innerHTML = "Login Name is Mandatory, did u miss it?";
            valid = "false";
        }
        else { document.getElementById('failed_register_message_NU_login_name').innerHTML = "" }

        var validatePwdObj = validatePwd(NU_password,NU_password_confirm);
        returnStatus    = validatePwdObj.returnStatus;
        message         = validatePwdObj.message;
        if (returnStatus == 0 ){
            document.getElementById('div_NU_password').className        += " bcTextBoxErr";
            document.getElementById('div_NU_password_confirm').className+= " bcTextBoxErr";
            document.getElementById('failed_register_message_NU_password').innerHTML= message;
            valid = "false";
            //alert(message);
        } else {
            document.getElementById('div_NU_password').className        = " f_left left wp20";
            document.getElementById('div_NU_password_confirm').className= " f_left left wp20 s07";
            document.getElementById('failed_register_message_NU_password').innerHTML= "";
        }



    if ( NU_Address == "" ) {
        document.getElementById('failed_register_message_NU_Address').innerHTML = "Don't leave street missing, did u miss it?";
            valid = "false";
        }
        else { document.getElementById('failed_register_message_NU_Address').innerHTML = "" }
    if ( NU_City == "" ) {
        document.getElementById('failed_register_message_NU_City').innerHTML = "Don't leave city blank, did u miss it?";
            valid = "false";
        }
        else { document.getElementById('failed_register_message_NU_City').innerHTML = "" }
    if ( NU_PostalCode == "" || NU_PostalCode.length < 5 ) {
        document.getElementById('failed_register_message_NU_PostalCode').innerHTML = "We need your zip code? it needs to be at least 5 digits";
            valid = "false";
        }
        else { document.getElementById('failed_register_message_NU_PostalCode').innerHTML = "" }


if ( valid != "false") {
    xmlhttp.open("GET","ajax/Profiles/editProfile_AddNewUser.php"+url,true);
    xmlhttp.send();
    }
}
function editProfile_UpdDefaultAppt(profiles_login_id,time,day_of_week,appointment_slot_interval,css){
        var url = "updateApptTime=1&"+"login_id="+profiles_login_id+"&time="+time+"&appointment_slot_interval="+appointment_slot_interval+"&day_of_week="+day_of_week;
        var divName = "appointmentTime_"+day_of_week+"_"+time;
        $.ajax({
            type: "GET",
            url: "ajax/Profiles/editProfile_UpdDefaultAppt.php",
            data: url,
            dataType: 'json',
            cache: true,
            success: function(data){
                // the response object is a json string.
                //alert(data.SQL);
                if (data.count == 0 || data.status == 0) {
                    document.getElementById(divName).className = css+' bcred'
                }
                else {
                    document.getElementById(divName).className = css+' bcgreen'
                }
              }
         });
}
function showProfiles_ShowByStatus(){
    $.fx.speeds.xslow = 3000; // 'xslow' means 3 seconds
    var url = "?action="+1;
    $("#mainBody").load("ajax/Profiles/showProfiles_ShowByStatus.php"+url).hide().fadeIn(2000);
}
function timeManagement_ClockInOrOut(login_id,action){
        var url = "?login_id="+login_id+"&action="+action;
        $("#mainBody").load("ajax/Profiles/timeManagement_ClockInOrOut.php"+url).hide().fadeIn(2000);
}
function editProfile_Appt_ActivateDeActivate(login_id,action){
        ChooseService_service_id =  'Appt_ActivateDeActivate';
        var Data = {
                                  "editProfile_Appt_ActivateDeActivate": 1,
                                  "login_id": login_id,
                                  "action": action
                                }
        $.ajax({
            type : 'Post',
            url : 'ajax/Profiles/editProfile_Appt_ActivateDeActivate.php',
            data: Data,
            dataType : 'json',
            success:function(data){
                    if (data.status == 1) {
                        document.getElementById('Appt_ActivateDeActivate').className = "f_left wp35 center ml10 bcgreen white";
                        document.getElementById('Appt_ActivateDeActivate').attributes['onclick'].value="editProfile_Appt_ActivateDeActivate("+login_id+",0)";
                    } else {
                        document.getElementById('Appt_ActivateDeActivate').className = "f_left wp35 center ml10 bcred black";
                        document.getElementById('Appt_ActivateDeActivate').attributes['onclick'].value="editProfile_Appt_ActivateDeActivate("+login_id+",1)";
                    }
            },
            error : function(XMLHttpRequest, textStatus, errorThrown) {
                    //$('#Inventory_Items_SubmitNewItem_result').html('There was an error.').delay(2000);
            }
        });
}
function editProfile_DaysOff_Set_UnSet(login_id,action,selected_date,appointment_slot_interval,appt_book_count){
        var image_div           = 'Calendar_Employees_Day_Available_Icon_' + login_id;
        var employees_hours_div = 'Calendar_Show_Employees_Hours_' + login_id;
        var Data        = {
                        "editProfile_DaysOff_Set_UnSet": 1,
                        "login_id": login_id,
                        "action": action,
                        "appointment_slot_interval": appointment_slot_interval,
                        "appt_book_count": appt_book_count,
                        "selected_date": selected_date
                        }
        loading_div(employees_hours_div);

        $.ajax({
            type : 'Post',
            url : 'ajax/Profiles/editProfile_DaysOff_Set_UnSet.php',
            data: Data,
            dataType : 'json',
            success:function(data){
                    if (data.status == 1) {
                       document.getElementById(image_div).innerHTML = '<img src="../common_includes/includes/images/Drink_BeerBottle.png"    height="12" width="12" alt="Day Off" title="Day Off">';
                       document.getElementById(image_div).setAttribute("onclick","editProfile_DaysOff_Set_UnSet("+login_id+",0,'"+selected_date+"',"+appointment_slot_interval+","+appt_book_count+");");
                    } else {
                       document.getElementById(image_div).innerHTML = '<img src="../common_includes/includes/images/work.png"                height="12" width="12" alt="Work Day" title="Work Day">';
                       document.getElementById(image_div).setAttribute("onclick","editProfile_DaysOff_Set_UnSet("+login_id+",1,'"+selected_date+"',"+appointment_slot_interval+","+appt_book_count+");");
                    }
                    document.getElementById(employees_hours_div).innerHTML = data.html;
            },
            error : function(XMLHttpRequest, textStatus, errorThrown) {
                    //$('#Inventory_Items_SubmitNewItem_result').html('There was an error.').delay(2000);
            }
        });
}
function logout(){
    $.fx.speeds.xslow = 3000; // 'xslow' means 3 seconds
    $.get("ajax/user_logout.php");
}
function Profiles_Search_searchBy(reportType){
    var url =  "?reportType=" + reportType ;
    var profiles_search_first_name          = document.getElementById('dynamic_pannel_first_name').value;
    var profiles_search_last_name           = document.getElementById('dynamic_pannel_last_name').value;
    var profiles_search_email               = document.getElementById('dynamic_pannel_email').value;
    var profiles_search_phone_number        = document.getElementById('dynamic_pannel_phone_number').value.replace(/[-' ']/g,'');
    var profiles_search_inactive_profiles   = document.getElementById('dynamic_pannel_inactive_profiles').checked;

    if ( document.getElementById('dynamic_pannel_inactive_profiles').checked) {
            url += "&profiles_search_inactive_profiles=1";
    } else {url += "&profiles_search_inactive_profiles=-1";}

    if ( profiles_search_first_name != "") {
            url += "&profiles_search_first_name=" + encodeURIComponent(profiles_search_first_name);
    } else {url += "&profiles_search_first_name=-1";}
    if ( profiles_search_last_name != "") {
            url += "&profiles_search_last_name=" + encodeURIComponent(profiles_search_last_name);
    } else {url += "&profiles_search_last_name=-1";}
    if ( profiles_search_email != "") {
            url += "&profiles_search_email=" + encodeURIComponent(profiles_search_email);
    } else {url += "&profiles_search_email=-1";}
    if ( profiles_search_phone_number != "") {
            url += "&profiles_search_phone_number=" + encodeURIComponent(profiles_search_phone_number);
    } else {url += "&profiles_search_phone_number=-1";}
    if ( profiles_search_inactive_profiles) {
            url += "&profiles_search_inactive_profiles=1";
    } else {url += "&profiles_search_inactive_profiles=-1";}

    $.ajax({
          url: 'ajax/Profiles/Profiles_Search_searchBy.php'+url,
          type: 'GET',
          success: function() {
            if ( reportType.startsWith("Profiles") ) {
                loading_div(reportType+'BodyCenter');
                ProfilesData(1,reportType);  ///  Call Function to reload report_data div.
            }
          }
    });
}


function Inventory_Categories_AddNewCategory(category_id){
    var url = "?category_id="+category_id;
    $("#InventoryMgmtBodyCenter").load("ajax/Inventory_Run/Inventory_Categories_AddNewCategory.php"+url).hide().fadeIn(2000);
}
function Inventory_Categories_SubmitNewCategory(company_id){
    var valid;
    var new_itemcategory_name = document.getElementById('new_itemcategory_name').value;
    var new_attribute1 = document.getElementById('new_attribute1').value;
    var new_attribute2 = document.getElementById('new_attribute2').value;

    var url =  "?addNewCategory=" + 1 +
        "&company_id=" + company_id +
        "&new_itemcategory_name=" + encodeURIComponent(new_itemcategory_name) +
        "&new_attribute1=" + encodeURIComponent(new_attribute1) +
        "&new_attribute2=" + encodeURIComponent(new_attribute2);
        if ( new_itemcategory_name == "" || new_itemcategory_name.length < 2 ) {
                //document.getElementById('failed_new_itemcategory_name').innerHTML = "<font color=red>street missing?</font>";
                document.getElementById('failed_new_itemcategory_name').className        += " bcTextBoxErr";
                valid = "false";
            }
        else {
                //document.getElementById('failed_new_itemcategory_name').innerHTML = "&nbsp;"
                document.getElementById('failed_new_itemcategory_name').className        = " report_data_cell_28";
            }
        if ( new_attribute1 == "" ) {
                //document.getElementById('failed_new_attribute1').innerHTML = "<font color=red>street missing?</font>";
                document.getElementById('failed_new_attribute1').className        += " bcTextBoxErr";
                valid = "false";
            }
        else {
                //document.getElementById('failed_new_attribute1').innerHTML = "&nbsp;"
                document.getElementById('failed_new_attribute1').className        = " report_data_cell_19";
            }
        if ( new_attribute2 == "" ) {
                //document.getElementById('failed_new_attribute2').innerHTML = "<font color=red>street missing?</font>";
                document.getElementById('failed_new_attribute2').className        += " bcTextBoxErr";
                valid = "false";
            }
        else {
                //document.getElementById('failed_new_attribute2').innerHTML = "&nbsp;"
                document.getElementById('failed_new_attribute2').className        = " report_data_cell_19";
            }

    if ( valid != "false") {
        $("#InventoryMgmtBodyCenter").load("ajax/Inventory_Run/Inventory_Categories_SubmitNewCategory.php"+url).hide().fadeIn(2000);
    }
}
function Inventory_Categories_SubmitNewSubCategory(company_id,parent_category_id){
    var valid;
    var new_itemcategory_name = document.getElementById('new_itemcategory_name').value;
    var new_attribute1 = document.getElementById('new_attribute1').value;
    var new_attribute2 = document.getElementById('new_attribute2').value;

    var url =  "?addNewCategory=" + 1 +
        "&company_id=" + company_id +
        "&parent_category_id=" + parent_category_id +
        "&new_itemcategory_name=" + encodeURIComponent(new_itemcategory_name) +
        "&new_attribute1=" + encodeURIComponent(new_attribute1) +
        "&new_attribute2=" + encodeURIComponent(new_attribute2);
        if ( new_itemcategory_name == "" || new_itemcategory_name.length < 2 ) {
                //document.getElementById('failed_new_itemcategory_name').innerHTML = "<font color=red>street missing?</font>";
                document.getElementById('failed_new_itemcategory_name').className        += " bcTextBoxErr";
                valid = "false";
            }
        else {
                //document.getElementById('failed_new_itemcategory_name').innerHTML = "&nbsp;"
                document.getElementById('failed_new_itemcategory_name').className        = " report_data_cell_28";
            }
        if ( new_attribute1 == "" ) {
                //document.getElementById('failed_new_attribute1').innerHTML = "<font color=red>street missing?</font>";
                document.getElementById('failed_new_attribute1').className        += " bcTextBoxErr";
                valid = "false";
            }
        else {
                //document.getElementById('failed_new_attribute1').innerHTML = "&nbsp;"
                document.getElementById('failed_new_attribute1').className        = " report_data_cell_19";
            }
        if ( new_attribute2 == "" ) {
                //document.getElementById('failed_new_attribute2').innerHTML = "<font color=red>street missing?</font>";
                document.getElementById('failed_new_attribute2').className        += " bcTextBoxErr";
                valid = "false";
            }
        else {
                //document.getElementById('failed_new_attribute2').innerHTML = "&nbsp;"
                document.getElementById('failed_new_attribute2').className        = " report_data_cell_19";
            }

    if ( valid != "false") {
        $("#InventoryMgmtBodyCenter").load("ajax/Inventory_Run/Inventory_Categories_SubmitNewSubCategory.php"+url).hide().fadeIn(2000);
    }
}
function Inventory_Categories_UpdCategory(category_id){
    var valid;
    var upd_itemcategory_name = document.getElementById('upd_itemcategory_name_'+category_id).value;
    var upd_attribute1 = document.getElementById('upd_attribute1_'+category_id).value;
    var upd_attribute2 = document.getElementById('upd_attribute2_'+category_id).value;

    var url =  "?updCategory=" + 1 +
        "&category_id=" + category_id +
        "&upd_itemcategory_name=" + encodeURIComponent(upd_itemcategory_name) +
        "&upd_attribute1=" + encodeURIComponent(upd_attribute1) +
        "&upd_attribute2=" + encodeURIComponent(upd_attribute2);
        if ( upd_itemcategory_name == "" || upd_itemcategory_name.length < 2 ) {
                //document.getElementById('failed_new_itemcategory_name').innerHTML = "<font color=red>street missing?</font>";
                document.getElementById('failed_upd_itemcategory_name_'+category_id).className        += " bcTextBoxErr";
                valid = "false";
            }
        else {
                //document.getElementById('failed_upd_itemcategory_name').innerHTML = "&nbsp;"
                document.getElementById('failed_upd_itemcategory_name_'+category_id).className        = " report_data_cell_17";
            }
        if ( upd_attribute1 == "" ) {
                //document.getElementById('failed_new_attribute1').innerHTML = "<font color=red>street missing?</font>";
                document.getElementById('failed_upd_attribute1_'+category_id).className        += " bcTextBoxErr";
                valid = "false";
            }
        else {
                //document.getElementById('failed_upd_attribute1').innerHTML = "&nbsp;"
                document.getElementById('failed_upd_attribute1_'+category_id).className        = " report_data_cell_17";
            }
        if ( upd_attribute2 == "" ) {
                //document.getElementById('failed_new_attribute2').innerHTML = "<font color=red>street missing?</font>";
                document.getElementById('failed_upd_attribute2_'+category_id).className        += " bcTextBoxErr";
                valid = "false";
            }
        else {
                //document.getElementById('failed_new_attribute2').innerHTML = "&nbsp;"
                document.getElementById('failed_upd_attribute2_'+category_id).className        = " report_data_cell_17";
            }

    if ( valid != "false") {
        $("#InventoryMgmtBodyCenter").load("ajax/Inventory_Run/Inventory_Categories_UpdCategory.php"+url).hide().fadeIn(2000);
    }
}
function Inventory_Categories_SelectCatID(category_id){
    var url = "?category_id="+category_id;
    $("#InventoryMgmtBodyCenter").load("ajax/Inventory_Run/Inventory_Categories_SelectCatID.php"+url).hide().fadeIn(2000);  
}
function Inventory_ItemSearch_searchBy(reportType){
    var url =  "?reportType=" + reportType ;
    var item_search_category        = document.getElementById('dynamic_pannel_category').value;
    var item_search_brand           = document.getElementById('dynamic_pannel_brand').value;
    var item_search_supplier        = document.getElementById('dynamic_pannel_supplier').value;
    var item_search_department      = document.getElementById('dynamic_pannel_department').value;
    var item_search_styleNumber     = document.getElementById('dynamic_pannel_styleNumber').value;

    var item_search_item_keyword    = document.getElementById('dynamic_pannel_item_keyword').value;
    var item_search_item_barcode    = document.getElementById('dynamic_pannel_item_barcode').value.replace(/[-' ']/g,'');
    var item_search_item_name       = document.getElementById('dynamic_pannel_item_name').value;

    if ( item_search_category != "") {
            url += "&item_search_category=" + encodeURIComponent(item_search_category);
    } else {url += "&item_search_category=-1";}
    if ( item_search_brand != "") {
            url += "&item_search_brand=" + encodeURIComponent(item_search_brand);
    } else {url += "&item_search_brand=-1";}
    if ( item_search_supplier != "") {
            url += "&item_search_supplier=" + encodeURIComponent(item_search_supplier);
    } else {url += "&item_search_supplier=-1";}
    if ( item_search_department != "") {
            url += "&item_search_department=" + encodeURIComponent(item_search_department);
    } else {url += "&item_search_department=-1";}
    if ( item_search_styleNumber != "") {
            url += "&item_search_styleNumber=" + encodeURIComponent(item_search_styleNumber);
    } else {url += "&item_search_styleNumber=-1";}

    if ( item_search_item_keyword != "") {
            url += "&item_search_item_keyword=" + encodeURIComponent(item_search_item_keyword);
    } else {url += "&item_search_item_keyword=-1";}
    
    if ( item_search_item_barcode != "") {
            url += "&item_search_item_barcode=" + encodeURIComponent(item_search_item_barcode);
    } else {url += "&item_search_item_barcode=-1";}
    if ( item_search_item_name != "") {
            url += "&item_search_item_name=" + encodeURIComponent(item_search_item_name);
    } else {url += "&item_search_item_name=-1";}


    if ( document.getElementById('dynamic_pannel_exclude_qty_zero').checked) {
        url += "&item_search_exclude_qty_zero=1";
    } else {
        url += "&item_search_exclude_qty_zero=-1";
    }
    
    if ( document.getElementById('dynamic_pannel_exclude_services').checked) {
        url += "&item_search_exclude_services=-1";
    } else {
        url += "&item_search_exclude_services=1";
    }
    
    if ( document.getElementById('dynamic_pannel_exclude_items').checked) {
        url += "&item_search_exclude_items=-1";
    } else {
        url += "&item_search_exclude_items=1";
    }
    
    $.ajax({
          url: 'ajax/ItemSearch/Inventory_ItemSearch_searchBy.php'+url,
          type: 'GET',
          dataType: 'json',         
          success: function(data) {
            if ( reportType.startsWith("item_") ) {
                loading_div(reportType+'BodyCenter');
                InventoryData(1,reportType);  ///  Call Function to reload report_data div.
                $('#item_search_item_supplier').html(data.html_item_search_item_supplier);
                $('#item_search_item_styleNumber').html(data.html_item_search_item_styleNumber);
                $('#item_search_item_department').html(data.html_item_search_item_department);
                $('#item_search_item_category').html(data.html_item_search_item_category);
                $('#item_search_item_brand').html(data.html_item_search_item_brand);
            }
          }
    });
}
function Inventory_Create_Run() {
    //alert(page);
    if (window.XMLHttpRequest)  {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        }
    else    {// code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }

    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
            document.getElementById('mainBody').innerHTML = xmlhttp.responseText;

        }
    }
var url                     = "?createInventory_Run=1";

    xmlhttp.open("GET","ajax/Inventory_Run/Inventory_Create_Run.php"+url,true);
    xmlhttp.send();
}
function Inventory_AddNewInventory_Run() {
    var add = 0;
    var InfoVerified = 0;
    InfoVerified = Inventory_UpdateRunInfo();
    if ( InfoVerified != 1 ) { alert('You must fill in the "Delivery Details" info first.');return}
    if (confirm("Are you serious? Is it done? Once you confirm, there is no way to edit it"))
        { add = 1; }

    //alert(page);
    if (window.XMLHttpRequest)  {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        }
    else    {// code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }

    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
            document.getElementById('mainBody').innerHTML = xmlhttp.responseText;
        }
    }

    if ( add == 1) {
        var url = "?createInventory_Run=1";
        xmlhttp.open("GET","ajax/Inventory_Run/Inventory_AddNewInventory_Run.php"+url,true);
        xmlhttp.send();
    }
}
function Inventory_UpdateRunInfo(){
var valid;
var varReturn = 0;
if (window.XMLHttpRequest)  {// code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp=new XMLHttpRequest();
    }
else    {// code for IE6, IE5
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }

xmlhttp.onreadystatechange=function() {
    if (xmlhttp.readyState==4 && xmlhttp.status==200) {
        Inventory_TotalsSummary();
        mainDiv('Inventory_AllInventoryRuns');
    }
}
var inventory_run_login_id          = document.getElementById('inventory_run_login_id').value ;
var inventory_run_notes             = document.getElementById('inventory_run_notes').value ;
var url =  "?Inventory_RunInfo=" + 1 +
    "&inventory_run_login_id=" + inventory_run_login_id +
    "&inventory_run_notes=" + inventory_run_notes ;
// 1st Line //
if ( inventory_run_login_id == 0 ) {
    document.getElementById('failed_inventory_run_login_id').className += " red";
    valid = "false";
    }
else { document.getElementById("failed_inventory_run_login_id").className = "right s11 pl10"; }

if ( valid != "false") {
    xmlhttp.open("GET","ajax/Inventory_Run/Inventory_UpdateRunInfo.php"+url,true);
    xmlhttp.send();
    varReturn = 1;
    }
return(varReturn);
}
function Inventory_Cancel_Inventory_Run(){
    var cancel = 0;
    if (confirm("Do you really want to cancel this Inventory Run?"))
        { cancel = 1; }

    //alert(page);
    if (window.XMLHttpRequest)  {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        }
    else    {// code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }

    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
            document.getElementById('mainBody').innerHTML = xmlhttp.responseText;
        }
    }
    if ( cancel == "1") {
        var url = "?cancelInventory_Run=1";
        xmlhttp.open("GET","ajax/Inventory_Run/Inventory_Cancel_Inventory_Run.php"+url,true);
        xmlhttp.send();
    }
}
function Inventory_TotalsSummary(){
    //alert(delivery_id);
    if (window.XMLHttpRequest)  {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        }
    else    {// code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }
    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
            document.getElementById('Inventory_TotalsSummary').innerHTML = xmlhttp.responseText;
        }
    }
    xmlhttp.open("GET","ajax/Inventory_Run/Inventory_TotalsSummary.php",true);
    xmlhttp.send();
}
function Inventory_selectStyleNumber() {
    var selectStyleNumber = document.getElementById('selectStyleNumber').value;
    if (window.XMLHttpRequest)  {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        }
    else    {// code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }

    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
            document.getElementById('Inventory_ItemsPerCurrentStyleNumber').innerHTML = xmlhttp.responseText;

        }
    }
var url = "?style_number="+selectStyleNumber;
xmlhttp.open("GET","ajax/Inventory_Run/Inventory_selectStyleNumber.php"+url,true);
xmlhttp.send();
}
function Inventory_selectCategory() {
    var selectCategory = document.getElementById('selectCategory').value;
    if (window.XMLHttpRequest)  {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        }
    else    {// code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }

    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
            document.getElementById('Inventory_ItemsPerCurrentStyleNumber').innerHTML = xmlhttp.responseText;

        }
    }
var url = "?category_id="+selectCategory;
xmlhttp.open("GET","ajax/Inventory_Run/Inventory_selectCategory.php"+url,true);
xmlhttp.send();
}
function Inventory_BarcodeSpecify(){
    var Barcode = document.getElementById('Input_Inventory_BarcodeSpecify').value;
    if (window.XMLHttpRequest)  {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        }
    else    {// code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }
    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
            document.getElementById('Input_Inventory_BarcodeSpecify').value = '';
            document.getElementById('Inventory_ItemsPerCurrentStyleNumber').innerHTML = xmlhttp.responseText;
        }
    }
var url = "?Barcode="+Barcode;
xmlhttp.open("GET","ajax/Inventory_Run/Inventory_BarcodeSpecify.php"+url,true);
xmlhttp.send();
}
function Inventory_PendingItem_increase_decrease(action,item_id) {
    if (window.XMLHttpRequest)  {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        }
    else    {// code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }

    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
            document.getElementById('showItems_for_Inventory').innerHTML = xmlhttp.responseText;
            //Deliveries_TotalsSummary();
        }
    }
        var url = "?item_id="+item_id+"&action="+action;
        xmlhttp.open("GET","ajax/Inventory_Run/Inventory_increase_decreasePendingItem.php"+url,true);
        xmlhttp.send();
}
function Inventory_ItemUpdateQuantity(item_id,iri_id) {
    var quantity = document.getElementById("Inventory_item_quantity_" + item_id).innerHTML ;
    if(!confirm("You are about to change the quantity for this item to \"" + quantity + "\", are you sure?")) return false;
    if (window.XMLHttpRequest)  {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        }
    else    {// code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }

    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
            document.getElementById('showItems_for_Inventory').innerHTML = xmlhttp.responseText;
        }
    }
        var url = "?item_id="+item_id+"&quantity="+quantity+"&iri_id="+iri_id;
        xmlhttp.open("GET","ajax/Inventory_Run/Inventory_ItemUpdateQuantity.php"+url,true);
        xmlhttp.send();
        return 1;
}
function Inventory_InventoryRun_SettleItem(item_id,iri_id) {
    var quantity = document.getElementById("Inventory_item_quantity_" + item_id).innerHTML ;
    if(!confirm("You are about to confirm the quantity for this item is \"" + quantity + "\", are you sure?")) return false;
    if (window.XMLHttpRequest)  {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        }
    else    {// code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }

    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
            document.getElementById('showItems_for_Inventory').innerHTML = xmlhttp.responseText;
        }
    }
        var url = "?item_id="+item_id+"&quantity="+quantity+"&iri_id="+iri_id;
        xmlhttp.open("GET","ajax/Inventory_Run/Inventory_InventoryRun_SettleItem.php"+url,true);
        xmlhttp.send();
        return 1;
}
function Inventory_add_delete_Item(item_id){
    var valid = 'true';
    if (window.XMLHttpRequest)  {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        }
    else    {// code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }

    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
            document.getElementById('showItems_for_Inventory').innerHTML = xmlhttp.responseText;
            Inventory_TotalsSummary();
        }
    }
quantity    = document.getElementById('Inventory_NewItem_quantity_'+item_id).value ;

var url = "?item_id=" + item_id + "&quantity="+quantity;
if ( quantity == "" ) {
    document.getElementById('div_Inventory_NewItem_quantity_'+item_id).className = "f_left wp05 bcTextBoxErr";
    valid = "false";
    }
else { document.getElementById('div_Inventory_NewItem_quantity_'+item_id).className = "f_left wp05"; }

if ( valid != "false") {
    document.getElementById('Inventory_NewItem_action_img_'+item_id).src     ='/common_includes/includes/images/checkbox_red_med.jpg';
    document.getElementById('Inventory_NewItem_action_img_'+item_id).title   ="Item already Added, edit in Box Above.";
    document.getElementById('div_Inventory_NewItem_row_'+item_id).className   += "bclightpink";
    xmlhttp.open("GET","ajax/Inventory_Run/Inventory_add_delete_Item.php"+url,true);
    xmlhttp.send();
    }
}
function Inventory_TotalsSummary(){
    //alert(delivery_id);
    if (window.XMLHttpRequest)  {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        }
    else    {// code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }
    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
            document.getElementById('Inventory_TotalsSummary').innerHTML = xmlhttp.responseText;
        }
    }
    xmlhttp.open("GET","ajax/Inventory_Run/Inventory_TotalsSummary.php",true);
    xmlhttp.send();
}
function Inventory_InventoryRun_Details(inventory_run_id){
    if (window.XMLHttpRequest)  {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        }
    else    {// code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }
    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
            document.getElementById('mainBody').innerHTML = xmlhttp.responseText;
        }
    }
    var url = "?inventory_run_id="+inventory_run_id;
    xmlhttp.open("GET","ajax/Inventory_Run/Inventory_InventoryRunDetails.php"+url,true);
    xmlhttp.send();
}
function Inventory_AutoCreateItems(item_count){
    if (window.XMLHttpRequest)  {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        }
    else    {// code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }

    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
            document.getElementById('showItems_for_Inventory').innerHTML = xmlhttp.responseText;
            Inventory_TotalsSummary();
        }
    }

var url = "?item_count=" + item_count;
    xmlhttp.open("GET","ajax/Inventory_Run/Inventory_AutoCreateItems.php"+url,true);
    xmlhttp.send();
}
function Inventory_Items_Edit_Item(item_id){
    var url = "?item_id="+item_id;
    $("#mainBody").load("ajax/ItemSearch/Inventory_Items_Edit_Item.php"+url).hide().fadeIn(2000);
}
function Inventory_Items_Edit_ActiveTab(ActiveTab){
    $.fx.speeds.xslow = 3000; // 'xslow' means 3 seconds
    var url = "?ActiveTab="+ActiveTab;
    $('#Inventory_Items_SubmitNewItem_result').html("\u00a0");
    $("#InventoryMgmtBodyCenter").load("ajax/ItemSearch/Inventory_Items_Edit_ActiveTab.php"+url).hide().fadeIn(2000);
}
function Inventory_Items_Edit_Service(service_id){
    var url = "?service_id="+service_id;
    $("#mainBody").load("ajax/ItemSearch/Inventory_Items_Edit_Service.php"+url).hide().fadeIn(2000);
}
function Inventory_Items_Edit_ActiveTab_Service(ActiveTab){
    $.fx.speeds.xslow = 3000; // 'xslow' means 3 seconds
    var url = "?ActiveTab="+ActiveTab;
    $('#Inventory_Items_SubmitNewItem_result').html("\u00a0");
    $("#InventoryMgmtBodyCenter").load("ajax/ItemSearch/Inventory_Items_Edit_ActiveTab_Service.php"+url).hide().fadeIn(2000);
}
function Inventory_Items_Edit_or_Add_CategoryChange(){
    var category_id = document.getElementById('dynamic_pannel_category_id').value;
    var Data = {
        "changeCategory": 1,
        "category_id" : category_id
    };
        $.ajax({
            type : 'Post',
            url : 'ajax/ItemSearch/Inventory_Items_Edit_or_Add_CategoryChange.php',
            data: Data,
            dataType : 'json',
            success:function(data){
                        if (data.attribute1 === null ) { data.attribute1 = 'Attribute1'}
                        if (data.attribute2 === null ) { data.attribute2 = 'Attribute2'}

                        $('#Inventory_Items_attribute1').html(data.attribute1+':');
                        $('#Inventory_Items_attribute2').html(data.attribute2+':');
            },
            error : function(XMLHttpRequest, textStatus, errorThrown) {
            }
        });
}
function Inventory_Items_Edit_or_Add_clearValues(serviceORitem_session){
    $.fx.speeds.xslow = 3000; // 'xslow' means 3 seconds
    var url = "?serviceORitem_session="+serviceORitem_session;
    $("#InventoryMgmtBodyCenter").load("ajax/ItemSearch/Inventory_Items_Edit_or_Add_clearValues.php"+url).hide().fadeIn(2000);
}
function Inventory_Items_Edit_or_Add(item_id,type){
this.valid = "true";
var AttributesData, category_id, supplier_id,brand_id,department_id,tax_group_id,name,number,style,attribute1,attribute2,price,buy_price,discount,location,reorder_limit1,reorder_limit2,archived,online_active;
//ADD ADD ADD ADD new SERVICE/ITEM
//ADD ADD ADD ADD new SERVICE/ITEM
if (item_id == 0 ) {
        if (type == 'item') {
                category_id             = validateAttribute(["dropdown"],'category_id','keep');
                name                    = validateAttribute(["length_lt_2"],'name','keep');
                buy_price               = validateAttribute(["money"],'buy_price','keep');
                price                   = validateAttribute(["money"],'price','keep');
                barcode                 = validateAttribute(["filled_and_number","filled_and_length_lt_5","filled_and_barcode"],'barcode','keep');
                number                  = validateAttribute(["length_lt_3"],'number','keep');
                style                   = validateAttribute(["length_lt_3"],'style','keep');

                supplier_id             = validateAttribute([],'supplier_id','keep');
                brand_id                = validateAttribute([],'brand_id','keep');
                department_id           = validateAttribute([],'department_id','keep');
                tax_group_id            = validateAttribute(["dropdown"],'tax_group_id','keep');
                attribute1              = validateAttribute([],'attribute1','keep');
                attribute2              = validateAttribute([],'attribute2','keep');
                discount                = validateAttribute([],'discount','keep');
                location                = validateAttribute([],'location','keep');
                reorder_limit1          = validateAttribute(["filled_and_number"],'reorder_limit1','keep');
                reorder_limit2          = validateAttribute(["filled_and_number"],'reorder_limit2','keep');
                online_active           = validateAttribute(["dropdown"],'online_active','keep');

                AttributesData          = {
                "action"                : 'AddItem',
                "serviceORitem_session" : "Items_CreateNewItem",

                "category_id"           : category_id[0],       "keep_category_id": category_id[1],
                "name"                  : name[0],              "keep_name" : name[1],
                "buy_price"             : buy_price[0],         "keep_buy_price" : buy_price[1],
                "price"                 : price[0],             "keep_price" : price[1],
                "barcode"               : barcode[0],           "keep_barcode" : barcode[1],
                "number"                : number[0],            "keep_number" : number[1],
                "style"                 : style[0],             "keep_style" : style[1],

                "supplier_id"           : supplier_id[0],       "keep_supplier_id": supplier_id[1],
                "brand_id"              : brand_id[0],          "keep_brand_id": brand_id[1],
                "department_id"         : department_id[0],     "keep_department_id": department_id[1],
                "tax_group_id"          : tax_group_id[0],      "keep_tax_group_id" : tax_group_id[1],
                "attribute1"            : attribute1[0],        "keep_attribute1" : attribute1[1],
                "attribute2"            : attribute2[0],        "keep_attribute2" : attribute2[1],
                "discount"              : discount[0],          "keep_discount" : discount[1],
                "location"              : location[0],          "keep_location" : location[1],
                "reorder_limit1"        : reorder_limit1[0],    "keep_reorder_limit1" : reorder_limit1[1],
                "reorder_limit2"        : reorder_limit2[0],    "keep_reorder_limit2" : reorder_limit2[1],
                "online_active"         : online_active[0],     "keep_online_active"  : online_active[1]
            };
        } else if (type == 'service'){
                category_id             = validateAttribute(["dropdown"],'category_id','keep');
                name                    = validateAttribute(["length_lt_2"],'name','keep');
                buy_price               = validateAttribute(["money"],'buy_price','keep');
                price                   = validateAttribute(["money"],'price','keep');
                est_time_mins           = validateAttribute(["number"],'est_time_mins','keep');
                barcode                 = validateAttribute(["filled_and_number","filled_and_length_lt_5","filled_and_barcode"],'barcode','no_keep');
                style                   = validateAttribute(["length_lt_3"],'style','keep');

                department_id           = validateAttribute([],'department_id','keep');
                tax_group_id            = validateAttribute([],'tax_group_id','keep');
                attribute1              = validateAttribute([],'attribute1','keep');
                attribute2              = validateAttribute([],'attribute2','keep');
                discount                = validateAttribute([],'discount','keep');
                location                = validateAttribute([],'location','keep');

                AttributesData          = {
                "action"                : 'AddService',
                "serviceORitem_session" : "Items_CreateNewService",

                "category_id"           : category_id[0],       "keep_category_id": category_id[1],
                "name"                  : name[0],              "keep_name" : name[1],
                "buy_price"             : buy_price[0],         "keep_buy_price" : buy_price[1],
                "price"                 : price[0],             "keep_price" : price[1],
                "est_time_mins"         : est_time_mins[0],     "keep_est_time_mins" : est_time_mins[1],
                "barcode"               : barcode[0],           "keep_barcode" : barcode[1],
                "style"                 : style[0],             "keep_style" : style[1],

                "department_id"         : department_id[0],     "keep_department_id": department_id[1],
                "tax_group_id"          : tax_group_id[0],      "keep_tax_group_id" : tax_group_id[1],
                "attribute1"            : attribute1[0],        "keep_attribute1" : attribute1[1],
                "attribute2"            : attribute2[0],        "keep_attribute2" : attribute2[1],
                "discount"              : discount[0],          "keep_discount" : discount[1],
                "location"              : location[0],          "keep_location" : location[1]
                };
        }
}


//EDIT EDIT EDIT EDIT SERVICE/ITEM
//EDIT EDIT EDIT EDIT SERVICE/ITEM
else if (item_id != 0 ) {
        if (type == 'item') {
                category_id             = validateAttribute(["dropdown"],'category_id','keep');
                name                    = validateAttribute(["length_lt_2"],'name','keep');
                buy_price               = validateAttribute(["money"],'buy_price','keep');
                price                   = validateAttribute(["money"],'price','keep');
                // Quantity not updated
                // Barcode not updated
                number                  = validateAttribute(["length_lt_3"],'number','keep');
                style                   = validateAttribute(["length_lt_3"],'style','keep');

                supplier_id             = validateAttribute([],'supplier_id','keep');
                brand_id                = validateAttribute([],'brand_id','keep');
                department_id           = validateAttribute([],'department_id','keep');
                tax_group_id            = validateAttribute([],'tax_group_id','keep');
                attribute1              = validateAttribute([],'attribute1','keep');
                attribute2              = validateAttribute([],'attribute2','keep');
                discount                = validateAttribute([],'discount','keep');
                location                = validateAttribute([],'location','keep');
                reorder_limit1          = validateAttribute(["filled_and_number"],'reorder_limit1','keep');
                reorder_limit2          = validateAttribute(["filled_and_number"],'reorder_limit2','keep');
                archived                = validateAttribute([],'archived','na');
                online_active           = validateAttribute(["dropdown"],'online_active','keep');

                AttributesData          = {
                "action"                : 'EditItem',
                "serviceORitem_session" : "",
                "item_id"               : item_id,

                "category_id"           : category_id[0],       "keep_category": category_id[1],
                "name"                  : name[0],              "keep_name" : name[1],
                "buy_price"             : buy_price[0],         "keep_buy_price" : buy_price[1],
                "price"                 : price[0],             "keep_price" : price[1],
                // Quantity not updated
                // Barcode not updated
                "number"                : number[0],            "keep_number" : number[1],
                "style"                 : style[0],             "keep_style" : style[1],

                "supplier_id"           : supplier_id[0],       "keep_supplier": supplier_id[1],
                "brand_id"              : brand_id[0],          "keep_brand": brand_id[1],
                "department_id"         : department_id[0],     "keep_department": department_id[1],
                "tax group_id"          : tax_group_id[0],      "keep_taxgroup" : tax_group_id[1],
                "attribute1"            : attribute1[0],        "keep_attribute1" : attribute1[1],
                "attribute2"            : attribute2[0],        "keep_attribute2" : attribute2[1],
                "discount"              : discount[0],          "keep_discount" : discount[1],
                "location"              : location[0],          "keep_location" : location[1],
                "reorder_limit1"        : reorder_limit1[0],    "keep_reorder_limit1" : reorder_limit1[1],
                "reorder_limit2"        : reorder_limit2[0],    "keep_reorder_limit2" : reorder_limit2[1],
                "archived"              : archived[0],          "keep_archived" : archived[1],
                "online_active"         : online_active[0],     "keep_online_active" : online_active[1]
            };
        }
        else if(type == 'service') {
                category_id             = validateAttribute(["dropdown"],   'category_id','keep');
                name                    = validateAttribute(["length_lt_2"],'name','keep');
                buy_price               = validateAttribute(["money"],      'buy_price','keep');
                price                   = validateAttribute(["money"],      'price','keep');
                est_time_mins           = validateAttribute(["number"],'est_time_mins','keep');
                // Barcode not updated
                style                   = validateAttribute(["length_lt_3"],'style','keep');

                department_id           = validateAttribute([],'department_id','keep');
                tax_group_id            = validateAttribute([],'tax_group_id','keep');
                attribute1              = validateAttribute([],'attribute1','keep');
                attribute2              = validateAttribute([],'attribute2','keep');
                discount                = validateAttribute([],'discount',  'keep');
                location                = validateAttribute([],'location',  'keep');
                archived                = validateAttribute([],'archived',  'na');

                AttributesData = {
                "action"                : 'EditService',
                "serviceORitem_session" : "",
                "item_id"               : item_id,

                "category_id"           : category_id[0],       "keep_category_id": category_id[1],
                "name"                  : name[0],              "keep_name" : name[1],
                "buy_price"             : buy_price[0],         "keep_buy_price" : buy_price[1],
                "price"                 : price[0],             "keep_price" : price[1],
                "est_time_mins"         : est_time_mins[0],     "keep_est_time_mins" : est_time_mins[1],
                // Barcode not updated
                "style"                 : style[0],             "keep_style" : style[1],

                "department_id"         : department_id[0],     "keep_department_id": department_id[1],
                "tax_group_id"          : tax_group_id[0],      "keep_tax_group_id" : tax_group_id[1],
                "attribute1"            : attribute1[0],        "keep_attribute1" : attribute1[1],
                "attribute2"            : attribute2[0],        "keep_attribute2" : attribute2[1],
                "discount"              : discount[0],          "keep_discount" : discount[1],
                "location"              : location[0],          "keep_location" : location[1],
                "archived"              : archived[0],          "keep_archived" : archived[1]
            };
    }
}

if ( this.valid != "false") {
        $.ajax({
            type : 'Post',
            url : 'ajax/ItemSearch/Inventory_Items_SubmitEdit_or_Add.php',
            data: AttributesData,
            dataType : 'json',
            success:function(data){
                    if (data.returnCode == 0) {
                        $('#InventoryMgmtBodyCenter').attr("style", "display:none");
                        $('#InventoryMgmtBodyCenter').html(data.message).fadeIn(5000);

                        $('#Inventory_Items_SubmitEdit_or_Add_result').html('There was an error.').fadeOut(400);
                        $('#Inventory_Items_SubmitEdit_or_Add_result').addClass('red');
                    }
                    else {
                        $('#InventoryMgmtBodyCenter').attr("style", "display:none");
                        $('#InventoryMgmtBodyCenter').html(data.message).fadeIn(5000);

                        $('#Inventory_Items_SubmitEdit_or_Add_result').addClass('green s11');
                        $('#Inventory_Items_SubmitEdit_or_Add_result').html(data.itemType + ' Successfully '+data.itemAction+'.').fadeIn(5000);
                        setTimeout(function(){ $('#Inventory_Items_SubmitEdit_or_Add_result').html('').fadeOut(1000); },3500);
                    }
            },
            error : function(data,XMLHttpRequest, textStatus, errorThrown) {
                    $('#Inventory_Items_SubmitEdit_or_Add_result').html('There was an ERROR.\n').delay(2000);
            }
        });
    }
}
    function validateAttribute(what_toCheck_for_array,attribute,keep_or_not) {
    var function_false = 'true'; var dynamic_input_pannel_keep_data ; var dynamic_pannel_css ;
    var dynamic_input_pannel_data   = document.getElementById('dynamic_pannel_' + attribute).value;
    var strPattern                  = '^\[0-9]+(\.\?[0-9]+)?$';
    var attribute_pannel            = 'dynamic_pannel_' + attribute;
    var attribute_pannel_error      = 'dynamic_pannel_' + attribute + '_error';

    var dynamic_pannel_data_element = document.getElementById('dynamic_pannel_' + attribute);
    if ( typeof(dynamic_pannel_data_element) != 'undefined' && dynamic_pannel_data_element === null) { alert(attribute+" is missing or not defined"); return false; }

    if (keep_or_not == 'keep') {
        dynamic_input_pannel_keep_data      = document.getElementById('dynamic_pannel_' + attribute + '_keep').checked;
    } else {    dynamic_input_pannel_keep_data  = 0; }

    var dynamic_pannel_css_element = document.getElementById('dynamic_pannel_css_' + attribute);
    if ( typeof(dynamic_pannel_css_element) != 'undefined' && dynamic_pannel_css_element === null) {
        dynamic_pannel_css          = '';
    } else {    dynamic_pannel_css              = document.getElementById('dynamic_pannel_css_' + attribute).value }

    for(rule in what_toCheck_for_array) {
        if ( what_toCheck_for_array[rule] == 'dropdown') {
            if ( dynamic_input_pannel_data == -1 ) {
                document.getElementById(attribute_pannel).className        += " bcTextBoxErr";
                $("#"+attribute_pannel_error).text('Must choose something.');
                this.valid = "false";  function_false = "false";
            } else {
                if (dynamic_pannel_css != '' ) { document.getElementById(attribute_pannel).className        = dynamic_pannel_css; }
                $("#"+attribute_pannel_error).text("\u00a0");
            }
        }
        if ( what_toCheck_for_array[rule] == 'length_lt_5') {
            if ( dynamic_input_pannel_data.length <= (5-1) ) {
                document.getElementById(attribute_pannel).className        += " bcTextBoxErr";
                this.valid = "false"; function_false = "false";
                $("#"+attribute_pannel_error).text('5 characters minimum.');
            } else {
                if (dynamic_pannel_css != '' ) { document.getElementById(attribute_pannel).className        = dynamic_pannel_css; }
                $("#"+attribute_pannel_error).text("\u00a0");
            }
        }
       if ( what_toCheck_for_array[rule] == 'filled_and_length_lt_5') {
            if (dynamic_input_pannel_data.length >+ 0 ) {
                if ( dynamic_input_pannel_data.length <= (5-1) ) {
                    document.getElementById(attribute_pannel).className        += " bcTextBoxErr";
                    this.valid = "false"; function_false = "false";
                    $("#"+attribute_pannel_error).text('5 characters minimum.');
                } else {
                if (dynamic_pannel_css != '' ) { document.getElementById(attribute_pannel).className        = dynamic_pannel_css; }
                    $("#"+attribute_pannel_error).text("\u00a0");
                }
            }
        }
        if ( what_toCheck_for_array[rule] == 'length_lt_4') {
            if ( dynamic_input_pannel_data.length <= (4-1) ) {
                document.getElementById(attribute_pannel).className        += " bcTextBoxErr";
                this.valid = "false"; function_false = "false";
                $("#"+attribute_pannel_error).text('4 characters minimum.');
            } else {
                if (dynamic_pannel_css != '' ) { document.getElementById(attribute_pannel).className        = dynamic_pannel_css; }
                $("#"+attribute_pannel_error).text("\u00a0");
            }
        }
        if ( what_toCheck_for_array[rule] == 'length_lt_3') {
            if ( dynamic_input_pannel_data.length <= (3-1) ) {
                document.getElementById(attribute_pannel).className        += " bcTextBoxErr";
                this.valid = "false"; function_false = "false";
                $("#"+attribute_pannel_error).text('3 characters minimum.');
            } else {
                if (dynamic_pannel_css != '' ) { document.getElementById(attribute_pannel).className        = dynamic_pannel_css; }
                $("#"+attribute_pannel_error).text("\u00a0");
            }
        }
        if ( what_toCheck_for_array[rule] == 'length_lt_2') {
            if ( dynamic_input_pannel_data.length <= (2-1) ) {
                document.getElementById(attribute_pannel).className        += " bcTextBoxErr";
                this.valid = "false"; function_false = "false";
                $("#"+attribute_pannel_error).text('2 characters minimum.');
            } else {
                if (dynamic_pannel_css != '' ) { document.getElementById(attribute_pannel).className        = dynamic_pannel_css; }
                $("#"+attribute_pannel_error).text("\u00a0");
            }
        }
        if ( what_toCheck_for_array[rule] == 'length_lt_1') {
            if ( dynamic_input_pannel_data.length <= (1-1) ) {
                document.getElementById(attribute_pannel).className        += " bcTextBoxErr";
                this.valid = "false"; function_false = "false";
                $("#"+attribute_pannel_error).text('1 character minimum.');
            } else {
                if (dynamic_pannel_css != '' ) { document.getElementById(attribute_pannel).className        = dynamic_pannel_css; }
                $("#"+attribute_pannel_error).text("\u00a0");
            }
        }
        if ( what_toCheck_for_array[rule] == 'money') {
            if ( dynamic_input_pannel_data.search(strPattern)== -1 ) {
                document.getElementById(attribute_pannel).className        += " bcTextBoxErr";
                this.valid = "false"; function_false = "false";
                $("#"+attribute_pannel_error).text('Must be money.');
            } else {
                if (dynamic_pannel_css != '' ) { document.getElementById(attribute_pannel).className        = dynamic_pannel_css; }
                $("#"+attribute_pannel_error).text("\u00a0");
            }
        }
       if ( what_toCheck_for_array[rule] == 'number') {
            if ( IsNumeric(dynamic_input_pannel_data) == 0 ) {
                document.getElementById(attribute_pannel).className        += " bcTextBoxErr";
                this.valid = "false"; function_false = "false";
                $("#"+attribute_pannel_error).text('Must be a number.');
            } else {
                if (dynamic_pannel_css != '' ) { document.getElementById(attribute_pannel).className        = dynamic_pannel_css; }
                $("#"+attribute_pannel_error).text("\u00a0");
            }
        }
       if ( what_toCheck_for_array[rule] == 'filled_and_number') {
            if (dynamic_input_pannel_data.length >+ 0 ) {
                if ( IsNumeric(dynamic_input_pannel_data) == 0 ) {
                    document.getElementById(attribute_pannel).className        += " bcTextBoxErr";
                    this.valid = "false"; function_false = "false";
                    $("#"+attribute_pannel_error).text('Must be a number.');
                } else {
                    if (dynamic_pannel_css != '' ) { document.getElementById(attribute_pannel).className        = dynamic_pannel_css; }
                    $("#"+attribute_pannel_error).text("\u00a0");
                }
            }
            else {
                    if (dynamic_pannel_css != '' ) { document.getElementById(attribute_pannel).className        = dynamic_pannel_css; }
                    $("#"+attribute_pannel_error).text("\u00a0");
            }
        }
       if ( what_toCheck_for_array[rule] == 'barcode') {
            if ( existingBarcode(dynamic_input_pannel_data) == 0 ) {
                document.getElementById(attribute_pannel).className        += " bcTextBoxErr";
                this.valid = "false"; function_false = "false";
                $("#"+attribute_pannel_error).text('Barcode already used.');
            } else {
                if (dynamic_pannel_css != '' ) { document.getElementById(attribute_pannel).className        = dynamic_pannel_css; }
                $("#"+attribute_pannel_error).text("\u00a0");
            }
        }
       if ( what_toCheck_for_array[rule] == 'validEmailAddress') {
            if ( existing_ValidateVaildEmailAddress(dynamic_input_pannel_data) == 0 ) {
                document.getElementById(attribute_pannel).className        += " bcTextBoxErr wp100";
                this.valid = "false"; function_false = "false";
                $("#"+attribute_pannel_error).text('Email Address is invalid.');
            } else {
                if (dynamic_pannel_css != '' ) { document.getElementById(attribute_pannel).className        = dynamic_pannel_css; }
                $("#"+attribute_pannel_error).text("\u00a0");
            }
        }
       if ( what_toCheck_for_array[rule] == 'ExistingEmailAddress') {
            if (dynamic_input_pannel_data.length >+ 0 ) {
                if ( existing_EmailAddress(dynamic_input_pannel_data) == 0 ) {
                    document.getElementById(attribute_pannel).className        += " bcTextBoxErr";
                    this.valid = "false"; function_false = "false";
                    $("#"+attribute_pannel_error).text('Email already used, not unique.');
                } else {
                    if (dynamic_pannel_css != '' ) { document.getElementById(attribute_pannel).className        = dynamic_pannel_css; }
                    $("#"+attribute_pannel_error).text("\u00a0");
                }
            }
       }
       if ( what_toCheck_for_array[rule] == 'existingCompany') {
            if ( existingCompany(dynamic_input_pannel_data) == 0 ) {
                document.getElementById(attribute_pannel).className        += " bcTextBoxErr";
                this.valid = "false"; function_false = "false";
                $("#"+attribute_pannel_error).text('This Company Name already exists.');
            } else {
                if (dynamic_pannel_css != '' ) { document.getElementById(attribute_pannel).className        = dynamic_pannel_css; }
                $("#"+attribute_pannel_error).text("\u00a0");
            }
        }
       if ( what_toCheck_for_array[rule] == 'existingDomainName') {
            if ( existingDomainName(dynamic_input_pannel_data) == 0 ) {
                document.getElementById(attribute_pannel).className        += " bcTextBoxErr";
                this.valid = "false"; function_false = "false";
                $("#"+attribute_pannel_error).text('This Domain Name already exists.');
            } else {
                if (dynamic_pannel_css != '' ) { document.getElementById(attribute_pannel).className        = dynamic_pannel_css; }
                $("#"+attribute_pannel_error).text("\u00a0");
            }
        }
       if ( what_toCheck_for_array[rule] == 'existingSubDomain') {
            if ( existingSubDomain(dynamic_input_pannel_data) == 0 ) {
                document.getElementById(attribute_pannel).className        += " bcTextBoxErr";
                this.valid = "false"; function_false = "false";
                $("#"+attribute_pannel_error).text('This Host Name already exists.');
            } else {
                if (dynamic_pannel_css != '' ) { document.getElementById(attribute_pannel).className        = dynamic_pannel_css; }
                $("#"+attribute_pannel_error).text("\u00a0");
            }
        }
       if ( what_toCheck_for_array[rule] == 'filled_and_barcode') {
            if (dynamic_input_pannel_data.length >+ 0 ) {
                if ( existingBarcode(dynamic_input_pannel_data) == 0 ) {
                    document.getElementById(attribute_pannel).className        += " bcTextBoxErr";
                    this.valid = "false"; function_false = "false";
                    $("#"+attribute_pannel_error).text('Barcode already used.');
                } else {
                    if (dynamic_pannel_css != '' ) { document.getElementById(attribute_pannel).className        = dynamic_pannel_css; }
                    $("#"+attribute_pannel_error).text("\u00a0");
                }
            }
       }
       if ( what_toCheck_for_array[rule] == 'ExistingTemplateTabGroupName') {
            if (dynamic_input_pannel_data.length >+ 0 ) {
                if ( existing_templateTabGroupName(dynamic_input_pannel_data) == 0 ) {
                    document.getElementById(attribute_pannel).className        += " bcTextBoxErr";
                    this.valid = "false"; function_false = "false";
                    $("#"+attribute_pannel_error).text('TemplateTabGroupName already used.');
                } else {
                    if (dynamic_pannel_css != '' ) { document.getElementById(attribute_pannel).className        = dynamic_pannel_css; }
                    $("#"+attribute_pannel_error).text("\u00a0");
                }
            }
       }
        if (function_false == "false") { break; }
    }
    if ( dynamic_input_pannel_keep_data ){      attributeReturnDataKeep = encodeURIComponent(1); }
    else {                                      attributeReturnDataKeep = encodeURIComponent(0); }

    if (this.valid != "false") {                attributeReturnData     = encodeURIComponent(dynamic_input_pannel_data);}
    else {                                      attributeReturnData     = ""; }

    return [attributeReturnData,attributeReturnDataKeep];
}
        function existingBarcode(barcode){
            var url = "Barcode="+barcode;
            var response;
            $.ajax( {
                type:'Get',
                data: url,
                url:'ajax/Inventory_Run/Inventory_CheckBarcode.php',
                dataType: 'json',
                cache: true,
                async: false,
                success:function(data) {
                    if (data.BarcodeExistResponse == 0 ) {
                        response = 0;
                    }
                    else {
                        response = 1;
                    }
                }
            }
            );
                return response;
        }
        function existing_EmailAddress(email_address){
            var url = "email_address="+email_address;
            var response;
            $.ajax( {
                type:'Get',
                data: url,
                url:'ajax/Customers/existing_EmailAddress.php',
                dataType: 'json',
                cache: true,
                async: false,
                success:function(data) {
                    if (data.ExistResponse == 0 ) {
                        response = 0;
                    }
                    else {
                        response = 1;
                    }
                }
            }
            );
                return response;
        }
        function existingCompany(companyName){
            var url = "companyName="+companyName;
            var response;
            $.ajax( {
                type:'Get',
                data: url,
                url:'ajax/Companies/Companies_Check_existingCompany.php',
                dataType: 'json',
                cache: true,
                async: false,
                success:function(data) {
                    if (data.existingCompanyResponse == 0 ) {
                        response = 0;
                    }
                    else {
                        response = 1;
                    }
                }
            }
            );
                return response;
        }
        function existingDomainName(DomainName){
            var url = "DomainName="+DomainName;
            var response;
            $.ajax( {
                type:'Get',
                data: url,
                url:'ajax/Companies/Companies_Check_existingDomainName.php',
                dataType: 'json',
                cache: true,
                async: false,
                success:function(data) {
                    if (data.existingDomainNameResponse == 0 ) {
                        response = 0;
                    }
                    else {
                        response = 1;
                    }
                }
            }
            );
                return response;
        }
        function existingSubDomain(SubDomain){
            var url = "SubDomain="+SubDomain;
            var response;
            $.ajax( {
                type:'Get',
                data: url,
                url:'ajax/Companies/Companies_Check_existingSubDomain.php',
                dataType: 'json',
                cache: true,
                async: false,
                success:function(data) {
                    if (data.existingSubDomainResponse == 0 ) {
                        response = 0;
                    }
                    else {
                        response = 1;
                    }
                }
            }
            );
                return response;
        }
        function existing_ValidateVaildEmailAddress(email_address){
            var response=1;
            var tfld = trim(email_address);                        // value of field with whitespace trimmed off
            var emailFilter = /^[^@]+@[^@.]+\.[^@]*\w\w$/ ;
            var illegalChars= /[\(\)\<\>\,\;\:\\\"\[\]]/ ;

            if (email_address == "") {
                        response = 0;
            } else if (!emailFilter.test(tfld)) {              //test email for illegal characters
                        response = 0;
            } else if (email_address.match(illegalChars)) {
                        response = 0;
            } else {
                //email_address.style.background = 'White';
            }
            return response;
        }
        function existing_templateTabGroupName(templateTabGroupName){
            var url = "templateTabGroupName="+templateTabGroupName;
            var response;
            $.ajax( {
                type:'Get',
                data: url,
                url:'ajax/Companies/existing_templateTabGroupName.php',
                dataType: 'json',
                cache: true,
                async: false,
                success:function(data) {
                    if (data.templateTabGroupNameExistResponse == 0 ) {
                        response = 0;
                    }
                    else {
                        response = 1;
                    }
                }
            }
            );
                return response;
        }


function preferences_UpdAttribute(company_id,column){
    var myValue = document.getElementById(column).value;
    var valid;
    var url =  "?company_id=" + company_id +
        "&column=" + encodeURIComponent(column) +
        "&value=" + encodeURIComponent(myValue);
    if ( myValue == "" || myValue.length < 1 ) {
            valid = "false";
        }
    if ( valid != "false") {
        $.fx.speeds.xslow = 3000; // 'xslow' means 3 seconds
        $("#mainBody").load("ajax/Preferences/preferences_UpdAttribute.php"+url).hide().fadeIn(2000);
    }
}
function preferences_AddAddressExistingCompany(company_id){
    var valid;
    var NU_Address1   = document.getElementById('preferences_address1').value ;
    var NU_Address2   = document.getElementById('preferences_address2').value ;
    var NU_City       = document.getElementById('preferences_city').value ;
    var NU_State      = document.getElementById('preferences_state').value ;
    var NU_ZipCode    = document.getElementById('preferences_zipcode').value ;

    var url =  "?newAddress="   + 1 +
        "&NU_company_id="       + company_id +
        "&NU_Address1="         + encodeURIComponent(NU_Address1) +
        "&NU_Address2="         + encodeURIComponent(NU_Address2) +
        "&NU_City="             + encodeURIComponent(NU_City) +
        "&NU_State="            + encodeURIComponent(NU_State) +
        "&NU_ZipCode="          + encodeURIComponent(NU_ZipCode);
    if ( NU_Address1 == "" ) {
        document.getElementById('failed_register_message_NU_Address1').innerHTML = "<font color=red>Street is missing?</font>";
            valid = "false";
        }
        else { document.getElementById('failed_register_message_NU_Address1').innerHTML = "&nbsp;" }

    if ( NU_City == "" ) {
        document.getElementById('failed_register_message_NU_City').innerHTML = "<font color=red>The City is blank, u miss it?</font>";
            valid = "false";
        }
        else { document.getElementById('failed_register_message_NU_City').innerHTML = "&nbsp;" }

    if ( NU_ZipCode == "" || NU_ZipCode.length < 5 ) {
        document.getElementById('failed_register_message_NU_zipcode').innerHTML = "<font color=red>Zip code? Must be 5 digits.</font>";
            valid = "false";
        }
        else { document.getElementById('failed_register_message_NU_zipcode').innerHTML = "&nbsp;" }


    if ( valid != "false") {
        $.fx.speeds.xslow = 3000; // 'xslow' means 3 seconds
        $("#mainBody").load("ajax/Preferences/preferences_AddAddressExistingCompany.php"+url).hide().fadeIn(2000);
    }
}
function preferences_UpdateAddress(edit_address_address_id){
    var valid;
    var NU_Address1         = document.getElementById('preferences_address1').value ;
    var NU_Address2         = document.getElementById('preferences_address2').value ;
    var NU_City             = document.getElementById('preferences_city').value ;
    var NU_State            = document.getElementById('preferences_state').value ;
    var NU_ZipCode          = document.getElementById('preferences_zipcode').value ;
    var NU_google_map_url   = document.getElementById('preferences_google_map_url').value ;

    var url =  "?editAddress=" + 1 +
        "&NU_edit_address_address_id=" + edit_address_address_id +
        "&NU_Address1=" + encodeURIComponent(NU_Address1) +
        "&NU_Address2=" + encodeURIComponent(NU_Address2) +
        "&NU_City=" + encodeURIComponent(NU_City) +
        "&NU_State=" + encodeURIComponent(NU_State) +
        "&NU_ZipCode=" + encodeURIComponent(NU_ZipCode) +
        "&NU_google_map_url=" + encodeURIComponent(NU_google_map_url);
    if ( NU_Address1 == "" ) {
        document.getElementById('failed_register_message_NU_Address1').innerHTML = "<font color=red>Street is missing?</font>";
            valid = "false";
        }
        else { document.getElementById('failed_register_message_NU_Address1').innerHTML = "&nbsp;" }

    if ( NU_City == "" ) {
        document.getElementById('failed_register_message_NU_City').innerHTML = "<font color=red>The City is blank, u miss it?</font>";
            valid = "false";
        }
        else { document.getElementById('failed_register_message_NU_City').innerHTML = "&nbsp;" }

    if ( NU_ZipCode == "" || NU_ZipCode.length < 5 ) {
        document.getElementById('failed_register_message_NU_zipcode').innerHTML = "<font color=red>Zip code? Must be 5 digits.</font>";
            valid = "false";
        }
        else { document.getElementById('failed_register_message_NU_zipcode').innerHTML = "&nbsp;" }

    if ( valid != "false") {
        $.fx.speeds.xslow = 3000; // 'xslow' means 3 seconds
        $("#mainBody").load("ajax/Preferences/preferences_UpdateAddress.php"+url).hide().fadeIn(2000);
    }
}
function preferences_EditAddress_setAddressID(edit_address_address_id){
    $.fx.speeds.xslow = 3000; // 'xslow' means 3 seconds
    var url = "?edit_address_address_id="+edit_address_address_id;
    $("#mainBody").load("ajax/Preferences/preferences_EditAddress_setAddressID.php"+url).hide().fadeIn(2000);
}
function editPreferencesTabs(ActiveTab){
    $.fx.speeds.xslow = 3000; // 'xslow' means 3 seconds
    var url = "?ActiveTab="+ActiveTab;
    $("#mainBody").load("ajax/Preferences/editPreferences_ActiveTab.php"+url).hide().fadeIn(2000);
}


function generateReport(reportType) {
    $.fx.speeds.xslow = 3000; // 'xslow' means 3 seconds
    var url = "?reportType="+reportType;
    $("#mainBody").load("ajax/Reports/generateReport.php"+url).hide().fadeIn(2000);
}
function ReportData(paging,reportType){
//alert(page);
var exempt = 0;
var valid,url,dynamic_pannel_start_date,dynamic_pannel_end_date,dynamic_pannel_id_search,dynamic_pannel_style_number_search,dynamic_pannel_barcode_search;
var dynamic_pannel_supplier,dynamic_pannel_brand,dynamic_pannel_department, dynamic_pannel_category;
var dynamic_pannel_customer_search,dynamic_pannel_employee_username,dynamic_pannel_register_id,dynamic_pannel_taxcat_name;
    if (window.XMLHttpRequest)  {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        }
    else    {// code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }
    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
            document.getElementById(reportType+'BodyCenter').innerHTML = xmlhttp.responseText;
            showPaging();
        }
    }
if ( paging == 1) {
        url =  "?ReportDataPagingIteration=" + 1;
}
else {
        url =  "?ReportDataRefreshQuery=" + 1 + "&reportType=" + reportType;
        dynamic_pannel_start_date       = document.getElementById('dynamic_pannel_start_date').value ;
        dynamic_pannel_end_date         = document.getElementById('dynamic_pannel_end_date').value ;
        dynamic_pannel_id_search        = document.getElementById('dynamic_pannel_id_search').value ;
        url = url + "&dynamic_pannel_start_date="       + dynamic_pannel_start_date ;
        url = url + "&dynamic_pannel_end_date="         + dynamic_pannel_end_date ;
        url = url + "&dynamic_pannel_id_search="        + dynamic_pannel_id_search ;

    if      (reportType == 'ItemsReport_BestSellers') {
        //###  best Sellers Variables
    }
    else if (reportType == 'ItemsReport_Category') {
        dynamic_pannel_supplier         = document.getElementById('dynamic_pannel_supplier').value ;
        dynamic_pannel_brand            = document.getElementById('dynamic_pannel_brand').value ;
        dynamic_pannel_department       = document.getElementById('dynamic_pannel_department').value ;
        dynamic_pannel_style_number_search = document.getElementById('dynamic_pannel_style_number_search').value ;
        dynamic_pannel_barcode_search   = document.getElementById('dynamic_pannel_barcode_search').value ;
        url = url + "&dynamic_pannel_supplier="         + dynamic_pannel_supplier ;
        url = url + "&dynamic_pannel_brand="            + dynamic_pannel_brand ;
        url = url + "&dynamic_pannel_department="       + dynamic_pannel_department;
        url = url + "&dynamic_pannel_style_number_search=" + dynamic_pannel_style_number_search ;
        url = url + "&dynamic_pannel_barcode_search="        + dynamic_pannel_barcode_search ;
    }
    else if (reportType == 'ItemsReport_Department') {
        dynamic_pannel_supplier         = document.getElementById('dynamic_pannel_supplier').value ;
        dynamic_pannel_brand            = document.getElementById('dynamic_pannel_brand').value ;
        dynamic_pannel_category         = document.getElementById('dynamic_pannel_category').value ;
        dynamic_pannel_style_number_search = document.getElementById('dynamic_pannel_style_number_search').value ;
        dynamic_pannel_barcode_search   = document.getElementById('dynamic_pannel_barcode_search').value ;
        url = url + "&dynamic_pannel_supplier="         + dynamic_pannel_supplier ;
        url = url + "&dynamic_pannel_brand="            + dynamic_pannel_brand ;
        url = url + "&dynamic_pannel_category="         + dynamic_pannel_category;
        url = url + "&dynamic_pannel_style_number_search=" + dynamic_pannel_style_number_search ;
        url = url + "&dynamic_pannel_barcode_search="        + dynamic_pannel_barcode_search ;
    }
    else if (reportType == 'ItemsReport_Vendor') {
        dynamic_pannel_supplier         = document.getElementById('dynamic_pannel_supplier').value ;
        dynamic_pannel_brand            = document.getElementById('dynamic_pannel_brand').value ;
        dynamic_pannel_category         = document.getElementById('dynamic_pannel_category').value ;

        url = url + "&dynamic_pannel_supplier="         + dynamic_pannel_supplier ;
        url = url + "&dynamic_pannel_brand="            + dynamic_pannel_brand ;
        url = url + "&dynamic_pannel_category="         + dynamic_pannel_category;
    }
    else if (reportType == 'ItemsReport_SoldOut') {
        dynamic_pannel_supplier         = document.getElementById('dynamic_pannel_supplier').value ;
        dynamic_pannel_brand            = document.getElementById('dynamic_pannel_brand').value ;
        dynamic_pannel_category         = document.getElementById('dynamic_pannel_category').value ;
        dynamic_pannel_department       = document.getElementById('dynamic_pannel_department').value ;
        dynamic_pannel_style_number_search = document.getElementById('dynamic_pannel_style_number_search').value ;
        dynamic_pannel_barcode_search   = document.getElementById('dynamic_pannel_barcode_search').value ;
        url = url + "&dynamic_pannel_supplier="         + dynamic_pannel_supplier ;
        url = url + "&dynamic_pannel_brand="            + dynamic_pannel_brand ;
        url = url + "&dynamic_pannel_category="         + dynamic_pannel_category;
        url = url + "&dynamic_pannel_department="       + dynamic_pannel_department;
        url = url + "&dynamic_pannel_style_number_search=" + dynamic_pannel_style_number_search ;
        url = url + "&dynamic_pannel_barcode_search="        + dynamic_pannel_barcode_search ;
    }
    else if (reportType == 'ItemsReport_AllItems') {
        dynamic_pannel_supplier         = document.getElementById('dynamic_pannel_supplier').value ;
        dynamic_pannel_brand            = document.getElementById('dynamic_pannel_brand').value ;
        dynamic_pannel_category         = document.getElementById('dynamic_pannel_category').value ;
        dynamic_pannel_department       = document.getElementById('dynamic_pannel_department').value ;
        dynamic_pannel_style_number_search = document.getElementById('dynamic_pannel_style_number_search').value ;
        dynamic_pannel_barcode_search   = document.getElementById('dynamic_pannel_barcode_search').value ;

        url = url + "&dynamic_pannel_id_search="        + dynamic_pannel_id_search ;
        url = url + "&dynamic_pannel_supplier="         + dynamic_pannel_supplier ;
        url = url + "&dynamic_pannel_brand="            + dynamic_pannel_brand ;
        url = url + "&dynamic_pannel_category="         + dynamic_pannel_category;
        url = url + "&dynamic_pannel_department="       + dynamic_pannel_department;
        url = url + "&dynamic_pannel_style_number_search=" + dynamic_pannel_style_number_search ;
        url = url + "&dynamic_pannel_barcode_search="        + dynamic_pannel_barcode_search ;
    }
    else if (reportType == 'Profiles_AllProfiles') {
        url =  "?ReportDataRefreshQuery=" + 1 + "&reportType=" + reportType;
        exempt = 1;
    }
    if ( dynamic_pannel_start_date == ""  && exempt == 0 ) {
            valid = "false";
        }
    if ( dynamic_pannel_end_date == "" && exempt == 0) {
            valid = "false";
        }
}


    if ( valid != "false") {
        xmlhttp.open("GET","ajax/Reports/Reports_Paging.php"+url,true);
        xmlhttp.send();
    }
}
function SalesData(paging,reportType){
//alert(page);
var exempt = 0;
var valid,url,dynamic_pannel_start_date,dynamic_pannel_end_date,dynamic_pannel_id_search
    if (window.XMLHttpRequest)  {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        }
    else    {// code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }
    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
            document.getElementById(reportType+'BodyCenter').innerHTML = xmlhttp.responseText;
            showPaging();
        }
    }
if ( paging == 1) {
        url =  "?ReportDataPagingIteration=" + 1;
}
else {
        url =  "?ReportDataRefreshQuery=" + 1 + "&reportType=" + reportType;
        dynamic_pannel_start_date       = document.getElementById('dynamic_pannel_start_date').value ;
        dynamic_pannel_end_date         = document.getElementById('dynamic_pannel_end_date').value ;
        url = url + "&dynamic_pannel_start_date="       + dynamic_pannel_start_date ;
        url = url + "&dynamic_pannel_end_date="         + dynamic_pannel_end_date ;
        if ( (dynamic_pannel_start_date == ""  && exempt == 0) || ( dynamic_pannel_end_date == "" && exempt == 0)  ) {
                valid = "false";
            }
}
    if ( valid != "false") {
        xmlhttp.open("GET","ajax/Reports/Reports_Paging.php"+url,true);
        xmlhttp.send();
    }
}
function ProfilesData(paging,reportType){
    var valid,url;
    if (window.XMLHttpRequest)  {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        }
    else    {// code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }
    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
            document.getElementById(reportType+'BodyCenter').innerHTML = xmlhttp.responseText;
            showPaging();
        }
    }
if ( paging == 1) {
        url =  "?ReportDataPagingIteration=" + 1;
}
else {
        url =  "?ReportDataRefreshQuery=" + 1 + "&reportType=" + reportType;

    if (reportType == 'Profiles_AllProfiles') {
        url =  "?ReportDataRefreshQuery=" + 1 + "&reportType=" + reportType;
        exempt = 1;
    }

    if ( dynamic_pannel_start_date == "" ) {
            valid = "false";
        }
    if ( dynamic_pannel_end_date == "" ) {
            valid = "false";
        }
}

    if ( valid != "false") {
        xmlhttp.open("GET","ajax/Profiles/editProfile_showLogins.php"+url,true);
        xmlhttp.send();
    }
}
function CustomersData(paging,reportType){
    var valid,url;
    if (window.XMLHttpRequest)  {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        }
    else    {// code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }
    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
            document.getElementById(reportType+'BodyCenter').innerHTML = xmlhttp.responseText;
            showPaging();
        }
    }
if ( paging == 1) {
        url =  "?ReportDataPagingIteration=" + 1;
}
else {
        url =  "?ReportDataRefreshQuery=" + 1 + "&reportType=" + reportType;

    if (reportType == 'Customers_AllCustomers') {
        url =  "?ReportDataRefreshQuery=" + 1 + "&reportType=" + reportType;
        exempt = 1;
    }

    if ( dynamic_pannel_start_date == "" ) {
            valid = "false";
        }
    if ( dynamic_pannel_end_date == "" ) {
            valid = "false";
        }
}

    if ( valid != "false") {
        xmlhttp.open("GET","ajax/Customers/Customers_showLogins.php"+url,true);
        xmlhttp.send();
    }
}
function JobsData(paging,reportType){
    var valid,url;
    if (window.XMLHttpRequest)  {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        }
    else    {// code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }
    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
            document.getElementById(reportType+'BodyCenter').innerHTML = xmlhttp.responseText;
            showPaging();
        }
    }
if ( paging == 1) {
        url =  "?ReportDataPagingIteration=" + 1;
}
else {
        url =  "?ReportDataRefreshQuery=" + 1 + "&reportType=" + reportType;

    if (reportType == 'Jobs_AllJobs') {
        url =  "?ReportDataRefreshQuery=" + 1 + "&reportType=" + reportType;
        exempt = 1;
    }

    if ( dynamic_pannel_start_date == "" ) {
            valid = "false";
        }
    if ( dynamic_pannel_end_date == "" ) {
            valid = "false";
        }
}

    if ( valid != "false") {
        xmlhttp.open("GET","ajax/Jobs/Jobs_showJobs.php"+url,true);
        xmlhttp.send();
    }
}
function InventoryData(paging,reportType){
    var valid,url;
    if (window.XMLHttpRequest)  {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        }
    else    {// code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }
    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
            if      (reportType == 'Inventory_Categories') {
                document.getElementById('InventoryMgmtBodyCenter').innerHTML = xmlhttp.responseText;
            }
            else if (reportType == 'Deliveries_AllDeliveries') {
                document.getElementById(reportType+'BodyCenter').innerHTML = xmlhttp.responseText;
            }
            else if (reportType == 'Inventory_AllInventoryRuns') {
                document.getElementById(reportType+'BodyCenter').innerHTML = xmlhttp.responseText;
            }
            else if (reportType == 'item_search') {
                document.getElementById(reportType+'BodyCenter').innerHTML = xmlhttp.responseText;
            }
            showPaging();
        }
    }
if ( paging == 1) {
        url =  "?ReportDataPagingIteration="    + 1;
}
else {
        url =  "?ReportDataRefreshQuery="       + 1 + "&reportType=" + reportType;
        if ( dynamic_pannel_start_date == "" ) { valid = "false";}
        if ( dynamic_pannel_end_date == ""   ) { valid = "false";}
}
    if ( valid != "false") {
        xmlhttp.open("GET","ajax/Inventory_Run/Inventory_Paging.php"+url,true);
        xmlhttp.send();
    }
}
function CompaniesData(paging,reportType){
    var valid,url;
    if (window.XMLHttpRequest)  {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        }
    else    {// code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }
    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
            document.getElementById(reportType+'BodyCenter').innerHTML = xmlhttp.responseText;
            showPaging();
        }
    }
if ( paging == 1) {
        url =  "?ReportDataPagingIteration=" + 1;
}
else {
        url =  "?ReportDataRefreshQuery=" + 1 + "&reportType=" + reportType;

    if (reportType == 'Companies_AllCompanies') {
        url =  "?ReportDataRefreshQuery=" + 1 + "&reportType=" + reportType;
        exempt = 1;
    }

    if ( dynamic_pannel_start_date == "" ) {
            valid = "false";
        }
    if ( dynamic_pannel_end_date == "" ) {
            valid = "false";
        }
}

    if ( valid != "false") {
        xmlhttp.open("GET","ajax/Companies/Companies.php"+url,true);
        xmlhttp.send();
    }
}
function MailerData(paging,reportType){
var url;
if ( paging == 1) {
        url =  "?ReportDataPagingIteration=" + 1;
}
    $.fx.speeds.xslow = 3000; // 'xslow' means 3 seconds
    url += "?reportType="+reportType;
    $('#'+reportType+'BodyCenter').load("ajax/Mailer/Mailer_Paging.php"+url).hide().fadeIn(2000);
}

function Daily_Ledger(woptions,start_day,end_day){
url =  "?start_day=" + start_day + "&end_day=" + end_day;
window.open("daily_ledger.php"+url, "_blank", woptions);
}
function sale_print_receipt(receipt_id,woptions,receipt_type){
url =  "?receipt_id=" + receipt_id + "&receipt_type=" + receipt_type;
window.open("receipt.php"+url, "_blank", woptions);
}
function Reports_AppointmentsPerHourReport_searchBy(reportType){
    var url =  "?reportType=" + reportType ;
    var start_date      = document.getElementById('dynamic_pannel_start_date').value;
    var end_date        = document.getElementById('dynamic_pannel_end_date').value;
    var staff_id        = document.getElementById('dynamic_pannel_staff_id').value;

    if ( start_date != "") {
            url += "&start_date=" + encodeURIComponent(start_date);
    } else {url += "&start_date=-1";}
    if ( end_date != "") {
            url += "&end_date=" + encodeURIComponent(end_date);
    } else {url += "&end_date=-1";}
    if ( staff_id != -1) {
            url += "&staff_id=" + encodeURIComponent(staff_id);
    } else {url += "&staff_id=-1";}

    $.ajax({
          url: 'ajax/Reports/Reports_AllReports_searchBy.php'+url,
          type: 'GET',
          success: function() {
                loading_div(reportType+'BodyCenter');
                SalesData(1,reportType);  ///  Call Function to reload report_data div.
          }
    });
}
function Reports_AppointmentsPerMonthReport_searchBy(reportType){
    var url =  "?reportType=" + reportType ;
    var start_date      = document.getElementById('dynamic_pannel_start_date').value;
    var end_date        = document.getElementById('dynamic_pannel_end_date').value;
    var staff_id        = document.getElementById('dynamic_pannel_staff_id').value;

    if ( start_date != "") {
            url += "&start_date=" + encodeURIComponent(start_date);
    } else {url += "&start_date=-1";}
    if ( end_date != "") {
            url += "&end_date=" + encodeURIComponent(end_date);
    } else {url += "&end_date=-1";}
    if ( staff_id != -1) {
            url += "&staff_id=" + encodeURIComponent(staff_id);
    } else {url += "&staff_id=-1";}

    $.ajax({
          url: 'ajax/Reports/Reports_AllReports_searchBy.php'+url,
          type: 'GET',
          success: function() {
                loading_div(reportType+'BodyCenter');
                SalesData(1,reportType);  ///  Call Function to reload report_data div.
          }
    });
}
function Reports_SalesReport_searchBy(reportType){
    var url =  "?reportType=" + reportType ;
    var start_date      = document.getElementById('dynamic_pannel_start_date').value;
    var end_date        = document.getElementById('dynamic_pannel_end_date').value;
    var staff_id        = document.getElementById('dynamic_pannel_staff_id').value;

    if ( start_date != "") {
            url += "&start_date=" + encodeURIComponent(start_date);
    } else {url += "&start_date=-1";}
    if ( end_date != "") {
            url += "&end_date=" + encodeURIComponent(end_date);
    } else {url += "&end_date=-1";}
    if ( staff_id != -1) {
            url += "&staff_id=" + encodeURIComponent(staff_id);
    } else {url += "&staff_id=-1";}

    $.ajax({
          url: 'ajax/Reports/Reports_AllReports_searchBy.php'+url,
          type: 'GET',
          success: function() {
                loading_div(reportType+'BodyCenter');
                SalesData(1,reportType);  ///  Call Function to reload report_data div.
          }
    });
}
function Reports_SalesPerHourReport_searchBy(reportType){
    var url =  "?reportType=" + reportType ;
    var start_date      = document.getElementById('dynamic_pannel_start_date').value;
    var end_date        = document.getElementById('dynamic_pannel_end_date').value;
    var staff_id        = document.getElementById('dynamic_pannel_staff_id').value;

    if ( start_date != "") {
            url += "&start_date=" + encodeURIComponent(start_date);
    } else {url += "&start_date=-1";}
    if ( end_date != "") {
            url += "&end_date=" + encodeURIComponent(end_date);
    } else {url += "&end_date=-1";}
    if ( staff_id != -1) {
            url += "&staff_id=" + encodeURIComponent(staff_id);
    } else {url += "&staff_id=-1";}

    $.ajax({
          url: 'ajax/Reports/Reports_AllReports_searchBy.php'+url,
          type: 'GET',
          success: function() {
                loading_div(reportType+'BodyCenter');
                SalesData(1,reportType);  ///  Call Function to reload report_data div.
          }
    });
}
function Reports_SalesPerMonthReport_searchBy(reportType){
    var url =  "?reportType=" + reportType ;
    var start_date      = document.getElementById('dynamic_pannel_start_date').value;
    var end_date        = document.getElementById('dynamic_pannel_end_date').value;
    var staff_id        = document.getElementById('dynamic_pannel_staff_id').value;

    if ( start_date != "") {
            url += "&start_date=" + encodeURIComponent(start_date);
    } else {url += "&start_date=-1";}
    if ( end_date != "") {
            url += "&end_date=" + encodeURIComponent(end_date);
    } else {url += "&end_date=-1";}
    if ( staff_id != -1) {
            url += "&staff_id=" + encodeURIComponent(staff_id);
    } else {url += "&staff_id=-1";}

    $.ajax({
          url: 'ajax/Reports/Reports_AllReports_searchBy.php'+url,
          type: 'GET',
          success: function() {
                loading_div(reportType+'BodyCenter')
                SalesData(1,reportType);  ///  Call Function to reload report_data div.
          }
    });
}
function Reports_DailyInventoryReport_searchBy(reportType){
    var url =  "?reportType=" + reportType ;
    var start_date      = document.getElementById('dynamic_pannel_start_date').value;
    var end_date        = document.getElementById('dynamic_pannel_end_date').value;

    if ( start_date != "") {
            url += "&start_date=" + encodeURIComponent(start_date);
    } else {url += "&start_date=-1";}
    if ( end_date != "") {
            url += "&end_date=" + encodeURIComponent(end_date);
    } else {url += "&end_date=-1";}
    $.ajax({
          url: 'ajax/Reports/Reports_AllReports_searchBy.php'+url,
          type: 'GET',
          success: function() {
                loading_div(reportType+'BodyCenter')
                SalesData(1,reportType);  ///  Call Function to reload report_data div.
          }
    });
}
function Reports_ItemsReport_BestSellers_searchBy(reportType){
    var url =  "?reportType=" + reportType ;
    var start_date      = document.getElementById('dynamic_pannel_start_date').value;
    var end_date        = document.getElementById('dynamic_pannel_end_date').value;

    if ( start_date != "") {
            url += "&start_date=" + encodeURIComponent(start_date);
    } else {url += "&start_date=-1";}
    if ( end_date != "") {
            url += "&end_date=" + encodeURIComponent(end_date);
    } else {url += "&end_date=-1";}
    $.ajax({
          url: 'ajax/Reports/Reports_AllReports_searchBy.php'+url,
          type: 'GET',
          success: function() {
                loading_div(reportType+'BodyCenter')
                ReportData(1,reportType);  ///  Call Function to reload report_data div.
          }
    });
}

function uploadImage(column_name_arg){
    var userfile            = column_name_arg + '_image';
    var column_name         = column_name_arg + '_column_name';
    var source_record_id    = column_name_arg + '_source_record_id';
    var message_div         = column_name_arg + '_message';

    var newImageData =  {
                    "userfile":         document.getElementById(userfile).value,
                    "column_name":      document.getElementById(column_name).value,
                    "source_record_id": document.getElementById(source_record_id).value
                    };
    $.ajax({
    type : 'Post',
    url : 'ajax/uploadFile.php',
    data: newImageData,
    dataType : 'json',
    success:function(data){      
                $('#'+message_div).html('<font color=red>'+ data.message +'</font>');
    },
    error : function(XMLHttpRequest, textStatus, errorThrown) {
            //$('#Inventory_Items_SubmitNewItem_result').html('There was an error.').delay(2000);
    }
});
}
function editImage_UpdTypeSetDefaultImageID(image_id,profile_id,column_name,image_db_id,reload_div){
    var url = "?image_id="+image_id+"&profile_id="+profile_id+"&column_name="+column_name+"&image_db_id="+image_db_id;
    $("#"+reload_div).load("ajax/ImageManagement/editImage_UpdTypeSetDefaultImageID.php"+url).hide().fadeIn(2000);
}
function editImage_UpdTypeSetDefaultGroupImageID(image_id,profile_id,column_name,image_db_id,reload_div,style_number,company_id){
    var url = "?image_id="+image_id+"&profile_id="+profile_id+"&column_name="+column_name+"&image_db_id="+image_db_id+"&style_number="+style_number+"&company_id="+company_id;
    $("#"+reload_div).load("ajax/ImageManagement/editImage_UpdTypeSetDefaultGroupImageID.php"+url).hide().fadeIn(2000);
}
function editImage_UpdTypeDeleteImageByImageID(image_id,profile_id,column_name,image_db_id,reload_div){
    var url = "?image_id="+image_id+"&column_name="+column_name+"&image_db_id="+image_db_id;
    $("#"+reload_div).load("ajax/ImageManagement/editImage_UpdTypeDeleteImageByImageID.php"+url).hide().fadeIn(2000);
}

String.prototype.startsWith = function(str)
{return (this.match("^"+str)==str)}



