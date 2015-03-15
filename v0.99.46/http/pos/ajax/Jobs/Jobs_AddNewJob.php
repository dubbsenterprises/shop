<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/jobs_functions.php');

$dal        = new GENERAL_DAL();
$insert_dal = new InsertUpdateDelete_DAL();
if ( isset($_POST['action']) && $_POST['action'] == 'Jobs_CreateNewJob' ) {
    $sql="insert into jobs_master (company_id,name,company_name,sub_desc,location_city,location_state,salary,status,added)
            values (
            ".$_POST['company_id'] .",
            ".quoteSmart(urldecode($_POST['name'])).",
            ".quoteSmart(urldecode($_POST['company_name']))  .",
            ".quoteSmart(urldecode($_POST['sub_desc'])).",
            ".quoteSmart(urldecode($_POST['location_city'])).",
            ".quoteSmart(urldecode($_POST['location_state'])).",
            ".quoteSmart(urldecode($_POST['salary'])).
            ",1,";
            $sql .= "now() )";
            $job_id = $insert_dal->insert_query($sql);

    if ($job_id) {
        //$_SESSION['edit_jobs']['job_id'] = $job_id;
        $response_array['returnCode']   = 1;
        $message = "New Job addition worked!";
    }
    else {
        $response_array['returnCode']   = 0;
        $message = "Something went wrong.";
    }
    
    unset($_SESSION['edit_jobs']['JobAdd']);
    ob_start();
    jobs();
    $html       = ob_get_clean();

    $response_array['html']         = $html;
    $response_array['message']      = $message;
    $response_array['sql']          = $sql;
}

echo json_encode($response_array);