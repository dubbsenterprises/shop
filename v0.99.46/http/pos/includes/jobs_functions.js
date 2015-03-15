function Jobs_UpdStatus(job_id,action){
var url = "?editJobs=1&job_id="+job_id+"&action="+action;
if (job_id != 0) {
    $("#mainBody").load("ajax/Jobs/Jobs_UpdStatus.php"+url).fadeIn(2000);
    }
}
function Jobs_Search_searchBy(reportType){
    var url =  "?reportType=" + reportType ;
    var jobs_search_name                = document.getElementById('dynamic_pannel_name').value;
    var jobs_search_sub_desc            = document.getElementById('dynamic_pannel_sub_desc').value;
    var jobs_search_company_name        = document.getElementById('dynamic_pannel_company_name').value;
    var jobs_search_location_state      = document.getElementById('dynamic_pannel_location_state').value;
    var jobs_search_location_city       = document.getElementById('dynamic_pannel_location_city').value;

    if ( document.getElementById('dynamic_pannel_inactive_jobs').checked) {
            url += "&jobs_search_inactive_jobs=1";
    } else {url += "&jobs_search_inactive_jobs=-1";}

    if ( jobs_search_name != "") {
            url += "&jobs_search_name=" + encodeURIComponent(jobs_search_name);
    } else {url += "&jobs_search_name=-1";}

    if ( jobs_search_sub_desc != "") {
            url += "&jobs_search_sub_desc=" + encodeURIComponent(jobs_search_sub_desc);
    } else {url += "&jobs_search_sub_desc=-1";}

    if ( jobs_search_company_name != "") {
            url += "&jobs_search_company_name=" + encodeURIComponent(jobs_search_company_name);
    } else {url += "&jobs_search_company_name=-1";}

    if ( jobs_search_location_state != "") {
            url += "&jobs_search_location_state=" + encodeURIComponent(jobs_search_location_state);
    } else {url += "&jobs_search_location_state=-1";}

    if ( jobs_search_location_city != "") {
            url += "&jobs_search_location_city=" + encodeURIComponent(jobs_search_location_city);
    } else {url += "&jobs_search_location_city=-1";}

    $.ajax({
          url: 'ajax/Jobs/Jobs_Search_searchBy.php'+url,
          type: 'GET',
          success: function() {
            if ( reportType.startsWith("Jobs") ) {
                loading_div(reportType+'BodyCenter');
                JobsData(1,reportType);  ///  Call Function to reload report_data div.
            }
          }
    });
}
function Jobs_AddJob(){
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
    var url = "?JobAdd=1";
    xmlhttp.open("GET","ajax/Jobs/Jobs_AddJob.php"+url,true);
    xmlhttp.send();
}
function Jobs_editJob(job_id){
var url = "?edit_Jobs=1&job_id="+job_id;
    $("#mainBody").load("ajax/Jobs/Jobs_editJob.php"+url).hide().fadeIn(2000);
}
function Jobs_ActiveJobsTabs(ActiveTab){
    $.fx.speeds.xslow = 3000; // 'xslow' means 3 seconds
    var url = "?ActiveTab="+ActiveTab;
    $("#mainBody").load("ajax/Jobs/Jobs_ActiveJobsTabs.php"+url).hide().fadeIn(2000);
}

function Jobs_AddNewJob(company_id) {
this.valid = "true";
    var name                = validateAttribute(["length_lt_2"],'name','no_keep');
    var company_name        = validateAttribute(["length_lt_2"],'company_name','no_keep');
    var sub_desc            = validateAttribute(["length_lt_3"],'sub_desc','no_keep');
    var location_city       = validateAttribute(["length_lt_2"],'location_city','no_keep');
    var location_state      = validateAttribute(["dropdown"],   'location_state','no_keep');
    var salary              = validateAttribute(["number"],     'salary','no_keep');

    Data          = {
    "action"            : "Jobs_CreateNewJob",
    "company_id"        : company_id,
    "name"              : name[0],
    "company_name"      : company_name[0],
    "sub_desc"          : sub_desc[0],
    "location_city"     : location_city[0],
    "location_state"    : location_state[0],
    "salary"            : salary[0]
    };

    if ( this.valid != "false") {
        $.ajax({
            type : 'Post',
            url : 'ajax/Jobs/Jobs_AddNewJob.php',
            data: Data,
            dataType : 'json',
            success:function(data){
                    if (data.returnCode == 1) {
                        $("#mainBody").html(data.html);
                    }
            },
            error : function(data,XMLHttpRequest, textStatus, errorThrown) {
                    $('#Inventory_Items_SubmitEdit_or_Add_result').html('There was an ERROR.\n').delay(2000);
            }
        });
    }
}
function Job_UpdJobAttributes(job_id){
this.valid = "true";
    var name                = validateAttribute(["length_lt_2"],'name','no_keep');
    var company_name        = validateAttribute(["length_lt_2"],'company_name','no_keep');
    var sub_desc            = validateAttribute(["length_lt_3"],'sub_desc','no_keep');
    var location_city       = validateAttribute(["length_lt_2"],'location_city','no_keep');
    var location_state      = validateAttribute(["dropdown"],   'location_state','no_keep');
    var salary              = validateAttribute(["number"],     'salary','no_keep');

    Data          = {
    "action"            : "Job_UpdJobAttributes",
    "job_id"            : job_id,
    "name"              : name[0],
    "company_name"      : company_name[0],
    "sub_desc"          : sub_desc[0],
    "location_city"     : location_city[0],
    "location_state"    : location_state[0],
    "salary"            : salary[0]
    };

    if ( this.valid != "false") {
        $.ajax({
            type : 'Post',
            url : 'ajax/Jobs/Job_UpdJobAttributes.php',
            data: Data,
            dataType : 'json',
            success:function(data){
                    if (data.returnCode == 1) {
                        $('#update_add_job_status_results').html(data.message);
                        doPulsate('update_add_job_status_results',2)
                    }
            },
            error : function(data,XMLHttpRequest, textStatus, errorThrown) {
                    $('#XXX').html('There was an ERROR.\n').delay(3000);
            }
        });
    }
}