function Sales_ProcessVariables( form_name ){
    $.ajax({
            type: "Post",
            url: "ajax/Sales/Sales_processVariables.php",
            data:  $('form[name="'+form_name+'"]').serialize(),
            dataType:  'json',
            success: function(data){
                $('#mainBody').html(data.html);
                showNotification({
                    message: data.message,
                    type: "success",
                    autoClose: 'true',
                    duration: 2
                });
            }
        });
   return false;        
}

function Sale_CustomerLink() {
    var valid;
    if (window.XMLHttpRequest)  {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        }
    else    {// code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }
    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
                //document.getElementById('registerPanel').innerHTML = "New Customer Registration Successful!<br>Thank You!";
                document.getElementById('sales_choose_customer').innerHTML = xmlhttp.responseText;
            }
        }

    var customer_id = document.getElementById('customer_id').value ;

    var url =  "?LinkCustomer="  + 1 + "&customer_id=" +  customer_id;

    if ( customer_id == 0 ) {
        //document.getElementById('failed_customer_id').innerHTML = "Select Sombody Fool?";
            valid = "false";
        }
        else { //document.getElementById('failed_register_message_NC_first_name').innerHTML = ""
        }


if ( valid != "false") {
    xmlhttp.open("GET","ajax/Sales/Sales_CustomerLink.php"+url,true);
    xmlhttp.send();
    }
}
function Sale_AddNewCustomer() {
    var valid;
    if (window.XMLHttpRequest)  {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        }
    else    {// code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }
    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
                //document.getElementById('registerPanel').innerHTML = "New Customer Registration Successful!<br>Thank You!";
                document.getElementById('sales_choose_customer').innerHTML = xmlhttp.responseText;
            }
        }

if ( valid != "false") {
    xmlhttp.open("GET","ajax/Sales/Sale_AddNewCustomer.php",true);
    xmlhttp.send();
    }
}
function Sale_CancelNewCustomer() {
    var valid;
    if (window.XMLHttpRequest)  {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        }
    else    {// code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }
    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
                //document.getElementById('registerPanel').innerHTML = "New Customer Registration Successful!<br>Thank You!";
                document.getElementById('sales_choose_customer').innerHTML = xmlhttp.responseText;
            }
        }

if ( valid != "false") {
    xmlhttp.open("GET","ajax/Sales/Sale_CancelNewCustomer.php",true);
    xmlhttp.send();
    }
}
function Sales_QuickAddNewCustomer(){
    var valid;
    if (window.XMLHttpRequest)  {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        }
    else    {// code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }
    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
                //document.getElementById('registerPanel').innerHTML = "New Customer Registration Successful!<br>Thank You!";
                document.getElementById('sales_choose_customer').innerHTML = xmlhttp.responseText;
            }
        }

    var NC_user_email = document.getElementById('NC_user_email').value ;
    var NC_first_name = document.getElementById('NC_first_name').value ;
    var NC_last_name  = document.getElementById('NC_last_name').value ;

    //var NC_phone_num  = document.getElementById('NC_phone_num').value ;
    //var NC_Address    = document.getElementById('NC_Address').value ;
    //var NC_City       = document.getElementById('NC_City').value ;
    //var NC_Country    = document.getElementById('NC_Country').value ;
    //var NC_State      = document.getElementById('NC_State').value ;
    //var NC_PostalCode = document.getElementById('NC_PostalCode').value ;


    var url =  "?newCustomer="  + 1 +
        "&NC_user_email="   + NC_user_email +
        "&NC_first_name="   + NC_first_name +
        "&NC_last_name="    + NC_last_name ;

    if ( NC_first_name == "" ) {
        document.getElementById('failed_register_message_NC_first_name').style.color="red";
            valid = "false";
        }
        else { document.getElementById('failed_register_message_NC_first_name').style.color="black"; }

    if ( NC_last_name == "" ) {
        document.getElementById('failed_register_message_NC_last_name').style.color="red";
            valid = "false";
        }
        else { document.getElementById('failed_register_message_NC_last_name').style.color="black"; }
    if ( NC_user_email == "" || validateEmail(NC_user_email) != "" ) {
        document.getElementById('failed_register_message_NC_user_email').style.color="red";
            valid = "false";
        }
        else { document.getElementById('failed_register_message_NC_user_email').style.color="black"; }

if ( valid != "false") {
    xmlhttp.open("GET","ajax/Sales/Sales_QuickAddNewCustomer.php"+url,true);
    xmlhttp.send();
    }
}
function Sales_AllItems_Discount(discount_percentage){
    Pulsate_div = 'Sales_AllItems_Discount_'+discount_percentage;
    AttributesData = {
    "action"                            : 'Sales_AllItems_Discount',
    "discount_percentage"               : discount_percentage
};

    $.ajax({
        type    : 'Post',
        url     : 'ajax/Sales/Sales_AllItems_Discount.php',
        data    : AttributesData,
        dataType: 'json',
        success: function(data){
            $('#mainBody').html(data.html);
        }
    });
}
function Sales_Add_Item(item_id){
    valid = false
    AttributesData = {
    "action"                : 'Sales_Add_Item',
    "item_id"               : item_id
};
    if ( this.valid != "false") {
        $.ajax({
            type    : 'Post',
            url     : 'ajax/Sales/Sales_Add_Item.php',
            data    : AttributesData,
            dataType: 'json',
            success:function(data){
                    if (data.returnCode == 0) {
                        //$('#InventoryMgmtBodyCenter').attr("style", "display:none");
                    }
                    else {
                        //location.reload();
                        $('#item_search_messages').addClass('green s11');
                        $('#item_search_messages').html('Item Successfully Added to Sale!').fadeIn(5000);
                        $('#Item_Search_Add_Item_'+item_id).attr('src', '/common_includes/includes/images/added_to_cart.png');
                        setTimeout(function(){ $('#item_search_messages').html('').fadeOut(1000); },3500);
                    }
            },
            error : function(data,XMLHttpRequest, textStatus, errorThrown) {
                    $('#Inventory_Items_SubmitEdit_or_Add_result').html('There was an ERROR.\n').delay(2000);
            }
        });
    }
}
function Sales_Add_Appointment_Services(appointment_id){
    valid = false
    AttributesData = {
    "action"                : 'Sales_Add_Appointment_Services',
    "appointment_id"        : appointment_id
};
    if ( this.valid != "false") {
        $.ajax({
            type    : 'Post',
            url     : 'ajax/Sales/Sales_Add_Appointment_Services.php',
            data    : AttributesData,
            dataType: 'json',
            success:function(data){
                    if (data.returnCode == 0) {
                        //$('#InventoryMgmtBodyCenter').attr("style", "display:none");
                    }
                    else {
                        //location.reload();
                        page = 'new_sale'
                        $.fx.speeds.xslow = 3000; // 'xslow' means 3 seconds
                        loading_div('mainBody');
                        var url = "?page="+page;
                        $('#mainBody').load("ajax/mainDiv.php"+url);
                    }
            },
            error : function(data,XMLHttpRequest, textStatus, errorThrown) {
                    $('#Inventory_Items_SubmitEdit_or_Add_result').html('There was an ERROR.\n').delay(2000);
            }
        });
    }
}