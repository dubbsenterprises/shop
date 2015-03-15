<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/jobs_functions.php');

$dal        = new GENERAL_DAL();
$insert_dal = new InsertUpdateDelete_DAL();
if ( isset($_POST['action']) && $_POST['action'] == 'Job_UpdJobAttributes' ) {
    $sql="UPDATE jobs_master set
                name            = ".quoteSmart(urldecode($_POST['name'])).",
                company_name    = ".quoteSmart(urldecode($_POST['company_name'])).",
                sub_desc        = ".quoteSmart(urldecode($_POST['sub_desc'])).",
                location_city   = ".quoteSmart(urldecode($_POST['location_city'])).",
                location_state   = ".quoteSmart(urldecode($_POST['location_state'])).",
                salary          = ".quoteSmart(urldecode($_POST['salary'])).",
                updated         = now()
                where id        = ".$_POST['job_id'] ."
            ";
            #echo $sql;
            $insert_dal->query($sql);
    ob_start();
    jobs();
    $html   = ob_get_clean();

    $response_array['returnCode']   = 1;
    $response_array['html']         = $html;
    $response_array['sql']          = $sql;
    $response_array['message']      = "<font color=green>Job was successfully updated.</font>";
    }
echo json_encode($response_array);
?>