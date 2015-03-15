templateType = 'BarberShop';

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
       document.getElementById(img_id).src='/common_includes/includes/images/red.gif';
       for (menu in menuLinks)
       {
           var current_image_id = menuLinks[menu] + '_img';
           if (str != menuLinks[menu]) { document.getElementById(current_image_id).src='/common_includes/includes/images/black.gif'; }
       }
        $.fx.speeds.xslow = 3000; // 'xslow' means 3 seconds
	var url = "?part="+str;
	$("#"+div).load("/common_includes/ajax/getData.php"+url).fadeIn(2000);
}

function load_registerNewUser(target_div)
{
    var div     = target_div;
    var action  = 'register';

        $.fx.speeds.xslow = 3000; // 'xslow' means 3 seconds
	var url = "?part="+action;
	$("#"+div).load("/common_includes/ajax/getData.php"+url).fadeIn(2000);
}
function loadContent(elementSelector, url, sourceUrl) {
$("#"+elementSelector+"").html('<iframe scrolling="no" height="100%" width=100% frameborder="0" src="http://wager.is-a-chef.com/massage/ajax/getData.php?part=appointments"></iframe>');
}

function appointmentProcessStepOne()
{
    $("#body_div").load("/common_includes/ajax/appointment.php?step=1").fadeIn(2000);
}
function appointmentProcessStepTwo(){
    var url = '';
    if (window.XMLHttpRequest)  {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        }
    else    {// code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }
    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
            document.getElementById('body_div').innerHTML = xmlhttp.responseText;
        }
    }
    
 if (appointmentProcessStepTwo.arguments.length == 0 ) {
    if ($("input[@name='appointBook_Staff_Selection']:checked").val()){
        staff_id = $("input[@name='appointBook_Staff_Selection']:checked").val();
        url =  "&staff_id=" + staff_id  ;
    } else {
        setTimeout(
            function(){
                $('#appointmentProcessStepTwoError').html('<font color=red>Error:</font> Please make your staff selection above. ').fadeOut(6000);
            },200);
        return false;
    }
}

    
xmlhttp.open("GET","/common_includes/ajax/appointment.php?step=2"+url,true);
xmlhttp.send();
return true;
}
    function appointmentProcessSelectService(service_id,data_cell_css){
        ChooseService_service_id =  'ChooseService_'+service_id;
        var selectServiceData = { "service_id": service_id }
        $.ajax({
            type : 'Post',
            url : '/common_includes/ajax/appointmentProcessSelectService.php',
            data: selectServiceData,
            dataType : 'json',
            success:function(data){
                    if (data.returnCode == 1) {
                        document.getElementById(ChooseService_service_id).className += " white bcgreen center";
                        $('#ChooseServices_est_time_total').html(data.total_services_times+" mins.").fadeIn(5000);
                        $('#ChooseServices_total_services_price').html("Value: $"+data.total_services_price).fadeIn(5000);
                        $('#make_appointment_step1_profile_descriptions').html(data.html);
                        doPulsate('ChooseServices_est_time_total',2);
                        doPulsate('ChooseServices_total_services_price',2);
                    } else {
                        document.getElementById(ChooseService_service_id).className = data_cell_css+" ";
                        $('#ChooseServices_est_time_total').html(data.total_services_times+" mins.").fadeIn(5000);
                        $('#ChooseServices_total_services_price').html("Value: $"+data.total_services_price).fadeIn(5000);
                        $('#make_appointment_step1_profile_descriptions').html(data.html);
                        doPulsate('ChooseServices_est_time_total',2);
                        doPulsate('ChooseServices_total_services_price',2);
                    }
            },
            error : function(XMLHttpRequest, textStatus, errorThrown) {
                    $('#Inventory_Items_SubmitNewItem_result').html('There was an error.').delay(2000);
            }
        });
}
function appointmentProcessStepThree(selected_apt_time)
{
    if (window.XMLHttpRequest)  {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        }
    else    {// code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }

    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
            document.getElementById('body_div').innerHTML = xmlhttp.responseText;
        }
    }
if (appointmentProcessStepThree.arguments.length == 1) {
    var url =  "&selected_apt_time=" + selected_apt_time ;
}
else {
    var url = "";
}

xmlhttp.open("GET","/common_includes/ajax/appointment.php?step=3"+url,true);
xmlhttp.send();
}
function appointmentProcessStepFour()
{
    $("#body_div").load("/common_includes/ajax/appointment.php?step=4").fadeIn(2000);
}

function show_calendar(year,month) {
    if (window.XMLHttpRequest)  {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        }
    else    {// code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }

    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
            document.getElementById('calendar').innerHTML = xmlhttp.responseText;

        }
    }
var url =  "?year=" + year + "&month=" + month ;
xmlhttp.open("GET","/common_includes/ajax/changeCal.php"+url,true);
xmlhttp.send();
}

function ValidateUser() {
    var ValidateUserData =  {  "validateUser": 1,
                               "email": document.getElementById('user_email').value
                            }
        $.ajax({
            type : 'Post',
            data: ValidateUserData,
            url : '/common_includes/ajax/validateUser.php',
            dataType: 'json',
            cache: true,
            async: false,
            success:function(data){
                if (data.count == 1) {
                        appointmentProcessStepThree();
                }
                else {
                        document.getElementById('failed_login_message').innerHTML = "User \"" + document.getElementById('user_email').value + "\" not found, it looks like you are not yet registered.";
                        LoadNewUserDiv(document.getElementById('user_email').value);
                }
            },
            error : function(XMLHttpRequest, textStatus, errorThrown) {
            //            $('#Inventory_Items_SubmitNewItem_result').html('There was an error.').delay(2000);
            }
    });
}
function ValidateUserQuickCheck() {
    var ValidateUserData =  {  "validateUserQuickCheck": 1,
                               "email": document.getElementById('user_email_quick_check').value
                            }
        var response;
        $.ajax( {
            type:'Post',
            data: ValidateUserData,
            url: '/common_includes/ajax/validateUser.php',
            dataType: 'json',
            cache: true,
            async: false,
            success:function(data) {
                if (data.count > 0 ) {
                    document.getElementById('loginPanel').innerHTML =
                    "<font color=white style=\"font-size:12;\"> " +
                    "Welcome Back "  + document.getElementById('user_email_quick_check').value + " !" +
                    "</font>";
                    doPulsate('scheduleAnAppointment',5);
                }
                else {
                    document.getElementById('loginPanel_Error').innerHTML =
                    "<font color=red> " +
                    "User "  + document.getElementById('user_email_quick_check').value + " not found." +
                    "</font>";
                    //NewUser('PrepToSubmit',email);
                }
            }
        });
        return response;
}
function NewUser(action) {
    var valid;  
    var NewUserData =   {"newuser": 1,
                        "NU_user_email": document.getElementById('NU_user_email').value,
                        "NU_first_name": document.getElementById('NU_first_name').value,
                        "NU_last_name":  document.getElementById('NU_last_name').value,
                        "NU_phone_num":  document.getElementById('NU_phone_num').value
                        }
    if ( document.getElementById('NU_first_name').value == "" ) {
        document.getElementById('failed_register_message_NU_first_name').innerHTML = "<font color=red><- is first name blank?</font>";
            valid = "false";
        }
        else { document.getElementById('failed_register_message_NU_first_name').innerHTML = "" }

    if ( document.getElementById('NU_last_name').value == "" ) {
        document.getElementById('failed_register_message_NU_last_name').innerHTML = "<font color=red><- is last name blank?</font>";
            valid = "false";
        }
        else { document.getElementById('failed_register_message_NU_last_name').innerHTML = "" }

    if ( document.getElementById('NU_user_email').value == "" || validateEmail(document.getElementById('NU_user_email').value) != "" ) {
        document.getElementById('failed_register_message_NU_user_email').innerHTML = "<font color=red><- is email blank?</font>";
            valid = "false";
        }
        else { document.getElementById('failed_register_message_NU_user_email').innerHTML = "" }

    if ( document.getElementById('NU_phone_num').value == "" ) {
        document.getElementById('failed_register_message_NU_phone_num').innerHTML = "<font color=red><- is Phone # blank?</font>";
            valid = "false";
        }
        else { document.getElementById('failed_register_message_NU_phone_num').innerHTML = "" }

if ( valid != "false") {
            $.ajax({
            type : 'Post',
            url : '/common_includes/ajax/NewUser.php',
            data: NewUserData,
            dataType : 'json',
            success:function(data){
                if (action == 'register_only') {
                    if (data.returnCode == 1) {
                        document.getElementById('register_pannel').innerHTML = "Thank you for registering!  Select \"Schedule and Appointment\" to book now!";
                    } else {
                          document.getElementById('failed_register_message_NU_user_email').innerHTML = "<font color=red>" +data.message+"</font>";
                    }
                 }
                if (action == 'register_and_book') {
                    if (data.returnCode == 1) {
                        appointmentProcessStepThree();
                    } else {
                          document.getElementById('failed_register_message_NU_user_email').innerHTML = "<font color=red>" +data.message+"</font>";
                    }
                 }

            },
            error : function(XMLHttpRequest, textStatus, errorThrown) {
            }
        });
    }
}
function LoadNewUserDiv() {
            $.ajax({
            type : 'Post',
            url : '/common_includes/ajax/LoadNewUserDiv.php',
            dataType : 'json',
            success:function(data){
                  $('#registerPanel').html(data.html);
            },
            error : function(XMLHttpRequest, textStatus, errorThrown) {
            }
        });
}

function Logout(session_name,div_name) {
    var eventData = { "session_name": session_name }
            $.ajax({
            type : 'Post',
            url : '/common_includes/ajax/DestroySession.php',
            data: eventData,
            dataType : 'json',
            success:function(data){
                if (div_name == 'body_div'){
                    $('#login_horizontal_div').html(data.html);
                    appointmentProcessStepOne();
                } else if (div_name == 'login_horizontal_div') {
                    $('#'+div_name).html(data.html);
                }
            },
            error : function(XMLHttpRequest, textStatus, errorThrown) {
            }
        });
}
function deleteEvent(event_id){
        var eventData = { "event_id": event_id }
        $.ajax({
            type : 'Post',
            url : '/common_includes/ajax/deleteEvent.php',
            data: eventData,
            dataType : 'json',
            success:function(data){
                    if (data.returnCode == 1) {
                        $('#appointmentHistoryPanel').html(data.html);
                    }
            },
            error : function(XMLHttpRequest, textStatus, errorThrown) {
                    $('#appointmentHistoryPanel').html('There was an error deleting the event.').delay(2000);
            }
        });
}
function changeCalendarYearWidget(year) {

    if (window.XMLHttpRequest)  {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        }
    else    {// code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }

    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
            document.getElementById('make_appointment_step2_calendarYearWidget').innerHTML = xmlhttp.responseText;
            changeCal(year,'01');   //  after page has reloaded
        }
    }
var url =  "?year=" + year ;
xmlhttp.open("GET","/common_includes/ajax/changeCalWidget.php"+url,true);
xmlhttp.send();
}

function selectAppointmentDate(selected_date,date_cell_css) {
    if (window.XMLHttpRequest)  {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        }
    else    {// code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }

    xmlhttp.onreadystatechange=function() {

        if (xmlhttp.readyState==1 ) {
                    //document.getElementById("calendarLoading").innerHTML="<font color=red>Loading...</font>";
                    document.getElementById("make_appointment_step2_choose_apt").innerHTML="<font color=red>Loading Appointment Data...</font>";
        }
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {

            var current_selected_date_ID = "DateTD_" + current_selected_date ;
            document.getElementById(current_selected_date_ID).className = "dateNotSelected "+date_cell_css;

            var selected_date_ID = "DateTD_" + selected_date ;
            document.getElementById(selected_date_ID).className = "dateSelected "+date_cell_css;

            document.getElementById('current_selected_date').value = selected_date ;
            //document.getElementById("calendarLoading").innerHTML="&nbsp;";
            document.getElementById('make_appointment_step2_choose_apt').innerHTML = xmlhttp.responseText;
        }
    }
var current_selected_date = document.getElementById("current_selected_date").value;
var url =  "?selected_date=" + selected_date ;
xmlhttp.open("GET","/common_includes/ajax/selectAppointmentDate.php"+url,true);
xmlhttp.send();
}
function selectAppointmentTime(selected_time) {
    if (window.XMLHttpRequest)  {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        }
    else    {// code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }

    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
            document.getElementById('make_appointment_step2_choose_apt').innerHTML = xmlhttp.responseText;

        }
    }
var url =  "?selected_date=" + selected_date ;
xmlhttp.open("GET","/common_includes/ajax/selectAppointment_chooseTime.php"+url,true);
xmlhttp.send();
}

function trim(s)
{
  return s.replace(/^\s+|\s+$/, '');
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
function AppointmentAlreadyBooked(){
    alert('You still have an open appointment which hasen\'t been completed yet.  You can cancel it on the right-hand panel on this page.');
}

function doPulsate(div_id,count) {
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
