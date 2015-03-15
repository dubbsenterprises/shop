function mailer_Create_Run() {
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
var url                     = "?createmailer_Run=1";

    xmlhttp.open("GET","ajax/Mailer/Mailer_Create_Run.php"+url,true);
    xmlhttp.send();
} 
function Mailer_UpdateRunInfo(){
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
            document.getElementById('mainBody').innerHTML = xmlhttp.responseText;
    }
}
var mailer_run_login_id     = document.getElementById('mailer_run_login_id').value ;
var mailer_run_template_id  = document.getElementById('mailer_run_template_id').value ;
var url                  = "?mailer_RunInfo=" + 1 ;
    url                 += "&mailer_run_login_id=" + mailer_run_login_id ;
    url                 += "&mailer_run_template_id=" + mailer_run_template_id ;

if ( valid != "false") {
    xmlhttp.open("GET","ajax/Mailer/Mailer_UpdateRunInfo.php"+url,true);
    xmlhttp.send();
    varReturn = 1;
    }
return(varReturn);
}
function Mailer_Cancel_Mailer_Run(){
    var cancel = 0;
    if (confirm("Do you really want to cancel this Mailer Run?"))
        { cancel = 1; }
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
        var url = "?cancelMailer_Run=1";
        xmlhttp.open("GET","ajax/Mailer/Mailer_Cancel_Mailer_Run.php"+url,true);
        xmlhttp.send();
    }
}
function Mailer_CreateMailer_Run(company_id,assigned_login_id,created_by_login_id,mailer_run_template_id) {
    var add = 0;
    if (confirm("Are you serious? Create this mailer?")) { add = 1; }
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
        var url  = "?createMailer_Run=1";
            url += "&company_id="+company_id;
            url += "&assigned_login_id="+assigned_login_id;
            url += "&created_by_login_id="+created_by_login_id;
            url += "&mailer_run_template_id="+mailer_run_template_id;
        xmlhttp.open("GET","ajax/Mailer/Mailer_CreateMailer_Run.php"+url,true);
        xmlhttp.send();
    }
}

function Mailer_MailerRun_Details(mailer_run_id){
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
    var url = "?mailer_run_id="+mailer_run_id;
    xmlhttp.open("GET","ajax/Mailer/Mailer_MailerRun_Details.php"+url,true);
    xmlhttp.send();
}
function Mailer_Show_Completed_Mailer_Run(status){
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
        var url = "?Mailer_Show_Completed_Mailer_Run="+status;
        xmlhttp.open("GET","ajax/Mailer/Mailer_Show_Completed_Mailer_Run.php"+url,true);
        xmlhttp.send();
}

function mailer_Mailer_Run_ShowSettings(company_id){
    if (window.XMLHttpRequest)  {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        }
    else    {// code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }
    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
            document.getElementById('showItems_for_mailer').innerHTML = xmlhttp.responseText;
        }
    }
        var url = "?mailer_Mailer_Run_ShowSettings="+1+"&company_id="+company_id;
        xmlhttp.open("GET","ajax/Mailer/mailer_Mailer_Run_ShowSettings.php"+url,true);
        xmlhttp.send();
}

function Mailer_SendMail(mailer_run_items_id){
    var row_div             = 'Mailer_customer_mailer_info_row_'+mailer_run_items_id;
    var status_div          = 'Mailer_customer_mailer_info_row_status_'+mailer_run_items_id;
    var complete_date_div   = 'Mailer_customer_mailer_info_row_complete_date_'+mailer_run_items_id;

    var completed_mailers   = Number(document.getElementById('completed_mailers').innerHTML);
    var remaining_mailers   = Number(document.getElementById('remaining_mailers').innerHTML);

    var Data = {
        "action"                : 'SendMail_By_mailer_run_item_id',
        "mailer_run_items_id"   : mailer_run_items_id
    };
        $.ajax({
            type : 'Post',
            url : 'ajax/Mailer/Mailer_SendMail.php',
            data: Data,
            dataType : 'json',
            async: true,
            success:function(data){
                if (data.returnCode == 1) {
                        $('#completed_mailers').html(completed_mailers + 1);
                        $('#remaining_mailers').html(remaining_mailers - 1); 
                        $('#'+status_div).html('YES');
                        $('#'+complete_date_div).html('Just now.');
                        $('#'+row_div).addClass('bclightgreen');
                }
            },
            error : function(XMLHttpRequest, textStatus, errorThrown) {
            }
        });
}
function Mailer_SendMassMail(mailer_run_id){
    var mailer_run_item_ids = document.getElementById('mailer_run_item_ids').value ;
    mailer_run_item_ids     = mailer_run_item_ids.split(',');
    for (var current_mailer_run_item_id in mailer_run_item_ids) {
        if (mailer_run_item_ids[current_mailer_run_item_id] != -1) {
            //console.log('Sending Mail for ID:'+mailer_run_item_ids[current_mailer_run_item_id]);
            Mailer_SendMail(mailer_run_item_ids[current_mailer_run_item_id]);
        } else {
            //console.log('Invalid mailer_run_item_id sent:'+mailer_run_item_ids[current_mailer_run_item_id]);
        }
    }
    Mailer_MailerRun_Details(mailer_run_id)
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