function Customer_login(profiles_login_id){
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
var url = "?editProfile=1&profiles_login_id="+profiles_login_id;
if (profiles_login_id != 0) {
    xmlhttp.open("GET","ajax/Profiles/editProfile_login.php"+url,true);
    xmlhttp.send();
    }
}
function CustomerLoginTabs(ActiveTab){
    //alert(ActiveTab);
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
var url = "?ActiveTab="+ActiveTab;
    xmlhttp.open("GET","ajax/Profiles/editProfile_ActiveTab.php"+url,true);
    xmlhttp.send();
}
function Customer_UpdStatus(customer_id,action){
var url = "?editCustomer=1&customer_id="+customer_id+"&action="+action;
if (customer_id != 0) {
    $("#mainBody").load("ajax/Customers/Customers_UpdStatus.php"+url).fadeIn(2000);
    }
}
function Customer_UpdProfileAttributes(customers_customer_id){
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
        }
    }

var customer_login_firstname = document.getElementById('editCustomer_firstname').value ;
var customer_login_lastname  = document.getElementById('editCustomer_lastname').value ;



var url = "?editCustomer=CustomerAttributes&customers_customer_id="+customers_customer_id+
    "&firstname="     + customer_login_firstname +
    "&lastname="      + customer_login_lastname  ;
// 1st Line //
//#############
if ( customer_login_firstname == "" ) {
    document.getElementById('customerLoginSummary_firstname').className = "bcTextBoxErr f_left w130";
    valid = "false";
    }
else { document.getElementById("customerLoginSummary_firstname").className = "lightgray f_left w130"; }
//#############
if ( customer_login_lastname == "" ) {
    document.getElementById('customerLoginSummary_lastname').className = "bcTextBoxErr f_left w130";
    valid = "false";
    }
else { document.getElementById("customerLoginSummary_lastname").className = "lightgray f_left w130"; }

if ( valid != "false") {
    xmlhttp.open("GET","ajax/Customers/editCustomer_UpdProfileAttributes.php"+url,true);
    xmlhttp.send();
    varReturn = 1;
    }
    return(varReturn);
}
function Customer_UpdPhysicalAddress(profiles_login_id){
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
function Customer_UpdElectronicInfo(profiles_login_id,attribute){
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


var url = "?editProfile=ElectronicInfo&profiles_login_id="+profiles_login_id;
url =  url +   "&email_address=" + profile_login_email_address
url =  url +   "&gmail_username="+ profile_login_gmail_username
url =  url +   "&gmail_password="+ profile_login_gmail_password

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
function Customer_AddCustomer(){
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
var url = "?CustomerAdd=1";
    xmlhttp.open("GET","ajax/Customers/Customers_CustomerAdd.php"+url,true);
    xmlhttp.send();
}
function Customer_AddNewCustomer() {
    var valid;
    if (window.XMLHttpRequest)  {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        }
    else    {// code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }
    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
                document.getElementById('registerPanel').innerHTML = "New Customer Registration Successful!<br>Thank You!";
                //document.getElementById('registerPanel').innerHTML = xmlhttp.responseText;
            }
        }

    var NC_user_email = document.getElementById('NC_user_email').value ;
    var NC_first_name = document.getElementById('NC_first_name').value ;
    var NC_last_name  = document.getElementById('NC_last_name').value ;
    var NC_phone_num  = document.getElementById('NC_phone_num').value ;


    var NC_Address    = document.getElementById('NC_Address').value ;
    var NC_City       = document.getElementById('NC_City').value ;
    var NC_Country    = document.getElementById('NC_Country').value ;
    var NC_State      = document.getElementById('NC_State').value ;
    var NC_PostalCode = document.getElementById('NC_PostalCode').value ;

    var url =  "?newCustomer="  + 1 +
        "&NC_user_email="   + NC_user_email +
        "&NC_first_name="   + NC_first_name +
        "&NC_last_name="    + NC_last_name +
        "&NC_phone_num="    + NC_phone_num +

        "&NC_Address="      + NC_Address +
        "&NC_City="         + NC_City +
        "&NC_Country="      + NC_Country +
        "&NC_State="        + NC_State +
        "&NC_PostalCode="   + NC_PostalCode;

    if ( NC_first_name == "" ) {
        document.getElementById('failed_register_message_NC_first_name').innerHTML = "First Name: Something wrong, is it blank?";
            valid = "false";
        }
        else { document.getElementById('failed_register_message_NC_first_name').innerHTML = "" }
    if ( NC_last_name == "" ) {
        document.getElementById('failed_register_message_NC_last_name').innerHTML = "Last Name: Something wrong, is it blank?";
            valid = "false";
        }
        else { document.getElementById('failed_register_message_NC_last_name').innerHTML = "" }
    if ( NC_user_email == "" || validateEmail(NC_user_email) != "" ) {
        document.getElementById('failed_register_message_NC_user_email').innerHTML = "Something Wrong with email address.";
            valid = "false";
        }
        else { document.getElementById('failed_register_message_NC_user_email').innerHTML = "" }
    if ( NC_phone_num == "" ) {
        document.getElementById('failed_register_message_NC_phone_num').innerHTML = "Something Wrong the phone number.";
            valid = "false";
        }
        else { document.getElementById('failed_register_message_NC_phone_num').innerHTML = "" }



    if ( NC_Address == "" ) {
        document.getElementById('failed_register_message_NC_Address').innerHTML = "Don't leave street missing, did u miss it?";
            valid = "false";
        }
        else { document.getElementById('failed_register_message_NC_Address').innerHTML = "" }
    if ( NC_City == "" ) {
        document.getElementById('failed_register_message_NC_City').innerHTML = "Don't leave city blank, did u miss it?";
            valid = "false";
        }
        else { document.getElementById('failed_register_message_NC_City').innerHTML = "" }
    if ( NC_PostalCode == "" || NC_PostalCode.length < 5 ) {
        document.getElementById('failed_register_message_NC_PostalCode').innerHTML = "We need your zip code? it needs to be at least 5 digits("+NC_PostalCode.length+")";
            valid = "false";
        }
        else { document.getElementById('failed_register_message_NC_PostalCode').innerHTML = "" }


if ( valid != "false") {
    xmlhttp.open("GET","ajax/Customers/Customers_AddNewCustomer.php"+url,true);
    xmlhttp.send();
    }
}
function Customer_editProfile(customer_id){
var url = "?edit_customers=1&customer_id="+customer_id;
    $("#mainBody").load("ajax/Customers/Customers_editProfile.php"+url).hide().fadeIn(2000);
}
function Customer_ActiveLoginTabs(ActiveTab){
    $.fx.speeds.xslow = 3000; // 'xslow' means 3 seconds
    var url = "?ActiveTab="+ActiveTab;
    $("#mainBody").load("ajax/Customers/Customers_ActiveLoginTabs.php"+url).hide().fadeIn(2000);
}
function Customer_AddAddressExistingUser(customer_id){
    var valid;
    var NU_Address1   = document.getElementById('Customer_address1').value ;
    var NU_Address2   = document.getElementById('Customer_address2').value ;
    var NU_City       = document.getElementById('Customer_city').value ;
    var NU_State      = document.getElementById('Customer_state').value ;
    var NU_ZipCode    = document.getElementById('Customer_zipcode').value ;

    var url =  "?newAddress=" + 1 +
        "&NU_customer_id=" + customer_id +
        "&NU_Address1=" + encodeURIComponent(NU_Address1) +
        "&NU_Address2=" + encodeURIComponent(NU_Address2) +
        "&NU_City=" + encodeURIComponent(NU_City) +
        "&NU_State=" + encodeURIComponent(NU_State) +
        "&NU_ZipCode=" + encodeURIComponent(NU_ZipCode);
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
        document.getElementById('failed_register_message_NU_ZipCode').innerHTML = "<font color=red>Zip code? Must be 5 digits.</font>";
            valid = "false";
        }
        else { document.getElementById('failed_register_message_NU_ZipCode').innerHTML = "&nbsp;" }


    if ( valid != "false") {
        $.fx.speeds.xslow = 3000; // 'xslow' means 3 seconds
        $("#mainBody").load("ajax/Customers/Customers_AddAddressExistingUser.php"+url).hide().fadeIn(2000);
    }
}
function Customer_UpdateAddress(edit_address_address_id){
    var valid;
    var NU_Address1   = document.getElementById('Customer_address1').value ;
    var NU_Address2   = document.getElementById('Customer_address2').value ;
    var NU_City       = document.getElementById('Customer_city').value ;
    var NU_State      = document.getElementById('Customer_state').value ;
    var NU_ZipCode    = document.getElementById('Customer_zipcode').value ;

    var url =  "?editAddress=" + 1 +
        "&NU_edit_address_address_id=" + edit_address_address_id +
        "&NU_Address1=" + encodeURIComponent(NU_Address1) +
        "&NU_Address2=" + encodeURIComponent(NU_Address2) +
        "&NU_City=" + encodeURIComponent(NU_City) +
        "&NU_State=" + encodeURIComponent(NU_State) +
        "&NU_ZipCode=" + encodeURIComponent(NU_ZipCode);
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
        document.getElementById('failed_register_message_NU_ZipCode').innerHTML = "<font color=red>Zip code? Must be 5 digits.</font>";
            valid = "false";
        }
        else { document.getElementById('failed_register_message_NU_ZipCode').innerHTML = "&nbsp;" }


    if ( valid != "false") {
        $.fx.speeds.xslow = 3000; // 'xslow' means 3 seconds
        $("#mainBody").load("ajax/Customers/Customers_UpdateAddress.php"+url).hide().fadeIn(2000);
    }
}
function Customer_EditAddress_setAddressID(edit_address_address_id){
    $.fx.speeds.xslow = 3000; // 'xslow' means 3 seconds
    var url = "?edit_address_address_id="+edit_address_address_id;
    $("#mainBody").load("ajax/Customers/Customers_EditAddress_setAddressID.php"+url).hide().fadeIn(2000);
}
function Customer_Employee_Call_Customer(customer_id,to,from){
    var divName = 'Customers_Employee_Call_Customer_'+customer_id;
    CallDataSet      = {
        "Customers_Employee_Call_Customer":1,
        "customer_id":customer_id,
        "to"                : to,
        "from"              : from
    };
        $.ajax({
            type: "Post",
            url: "ajax/Customers/Customers_Employee_Call_Customer.php",
            data: CallDataSet,
            dataType: 'json',
            cache: true,
            success: function(data){
                    //document.getElementById(divName).innerHTML = data.to;
                    $('#'+divName).html(data.to).fadeIn(5000);
                    alert(data.error)
                },
            error : function(data) {
                    alert(data.error)
            }
        });
}
function Customer_editCustomer_UpdElectronicInfo(customer_id,field){
    this.valid = "true";
    if (field == 'email') {
        var email                   = validateAttribute(["ExistingEmailAddress","validEmailAddress"],'email','nokeep');
        JSONData          = {
            "field"                 : field,
            "customer_id"           : customer_id,
            "email"                 : email[0],         "keep_email": email[1]
        };
    } else if (field == 'phone_num') {
        var phone_num               = validateAttribute(["length_lt_5"],'phone_num','nokeep');
        JSONData          = {
            "customer_id"           : customer_id,
            "field"                 : field,
            "phone_num"             : phone_num[0],     "keep_phone_num": phone_num[1]
        };
    } else if (field == 'email_promotions') {
        var email_promotions        = validateAttribute([],'email_promotions','nokeep');
        JSONData          = {
            "customer_id"           : customer_id,
            "field"                 : field,
            "email_promotions"      : email_promotions[0],     "keep_email_promotions": email_promotions[1]
        };
    }
    if ( this.valid != "false") {
        $.ajax({
            type : 'Post',
            url : 'ajax/Customers/Customer_editCustomer_UpdElectronicInfo.php',
            data: JSONData,
            dataType : 'json',
            success:function(data){
                        $('#mainBody').html(data.html).fadeIn(2000);
            },
            error : function(data,XMLHttpRequest, textStatus, errorThrown) {
                    $('#XXXXXXXXXXXXXXXX').html('There was an ERROR.\n').delay(2000);
            }
        });
    }
}
function Customer_Search_searchBy(reportType){
    var url =  "?reportType=" + reportType ;
    var customer_search_first_name      = document.getElementById('dynamic_pannel_first_name').value;
    var customer_search_last_name       = document.getElementById('dynamic_pannel_last_name').value;
    var customer_search_email           = document.getElementById('dynamic_pannel_email').value;
    var customer_search_phone_number    = document.getElementById('dynamic_pannel_phone_number').value.replace(/[-' ']/g,'');;


    if ( document.getElementById('dynamic_pannel_inactive_customers').checked) {
            url += "&customer_search_inactive_customers=1";
    } else {url += "&customer_search_inactive_customers=-1";}
    if ( customer_search_first_name != "") {
            url += "&customer_search_first_name=" + encodeURIComponent(customer_search_first_name);
    } else {url += "&customer_search_first_name=-1";}
    if ( customer_search_last_name != "") {
            url += "&customer_search_last_name=" + encodeURIComponent(customer_search_last_name);
    } else {url += "&customer_search_last_name=-1";}
    if ( customer_search_email != "") {
            url += "&customer_search_email=" + encodeURIComponent(customer_search_email);
    } else {url += "&customer_search_email=-1";}
    if ( customer_search_phone_number != "") {
            url += "&customer_search_phone_number=" + encodeURIComponent(customer_search_phone_number);
    } else {url += "&customer_search_phone_number=-1";}

    $.ajax({
          url: 'ajax/Customers/Customers_Search_searchBy.php'+url,
          type: 'GET',
          success: function() {
            if ( reportType.startsWith("Customers") ) {
                loading_div(reportType+'BodyCenter');
                CustomersData(1,reportType);  ///  Call Function to reload report_data div.
            }
          }
    });
}
function Customers_Send_Email(customer_id,type,email_response_div){
    if (confirm("Send Email? Type:"+type) ) {
        valid = false
        AttributesData = {
        "action"                : 'Send_Email',
        "type"                  : type,
        "customer_id"           : customer_id
        };
        if ( this.valid != "false") {
            $.ajax({
                type    : 'Post',
                url     : 'ajax/Customers/Customers_Send_Email.php',
                data    : AttributesData,
                dataType: 'json',
                success:function(data){
                        if (data.returnCode == 0) {
                            $('#'+email_response_div).addClass('bcyellow');
                        }
                        else {
                            $('#'+email_response_div).addClass('bcgreen');
                        }
                },
                error : function(data,XMLHttpRequest, textStatus, errorThrown) {
                }
            });
        }
    }
}
function Customers_Send_Appointment_Email(appointment_id,type,email_response_div){
    if ( confirm("Send Email? Type:"+type) ) {
        valid = false
        AttributesData = {
        "action"                : 'Send_Email',
        "type"                  : type,
        "appointment_id"        : appointment_id
        };
        if ( this.valid != "false") {
            $.ajax({
                type    : 'Post',
                url     : 'ajax/Customers/Customers_Send_Appointment_Email.php',
                data    : AttributesData,
                dataType: 'json',
                success:function(data){
                        if (data.returnCode == 0) {
                            $('#'+email_response_div).addClass('bcyellow');
                        }
                        else {
                            $('#'+email_response_div).addClass('bcgreen');
                        }
                },
                error : function(data,XMLHttpRequest, textStatus, errorThrown) {
                }
            });
        }
    }
}