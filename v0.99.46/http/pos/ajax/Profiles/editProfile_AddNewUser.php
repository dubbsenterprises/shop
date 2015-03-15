<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/profiles_functions.php');
session_start();

$dal        = new GENERAL_DAL();
$insert_dal = new InsertUpdateDelete_DAL();

if ( isset($_GET['newuser']) && $_GET['newuser'] == 1 ) {
    $login_check_result = $dal->check_loginExists($_GET['NU_login_name'],$_SESSION['settings']['company_id']);
    if ( $login_check_result[0]->count == 0 ) {
    $sql="insert into logins (company_id,username,email_address,firstname,lastname,phone_num,password,added)
            values (
             ".$_SESSION['settings']['company_id'] . ",
            '" . $_GET['NU_login_name']  . "',
            '" . $_GET['NU_user_email']  . "',
            '" . $_GET['NU_first_name']  . "',
            '" . $_GET['NU_last_name']   . "',
            '" . $_GET['NU_phone_num']   . "'," ;
            if (strlen($_GET['NU_password']) >=8 ) {
            $sql .= "'" . md5($_GET['NU_password']) ."', ";
            }
            $sql .= "now() )";
            #echo $sql;
            $login_id = $insert_dal->insert_query($sql);

   $sql_address_table="insert into addresses (address_line1,city,state,zipcode,country,login_id,added)
            values (
            '" . $_GET['NU_Address']   . "',
            '" . $_GET['NU_City']   . "',
            '" . $_GET['NU_State']  . "',
            '" . $_GET['NU_PostalCode']  . "',
            '" . $_GET['NU_Country'] . "',
            '" . $login_id . "', ";
            $sql_address_table .= "now() )";
            #echo $sql_address_table;
            $address_id = $insert_dal->insert_query($sql_address_table);

        if ($login_id) {
            $_SESSION['edit_profiles']['login_id'] = $login_id;
            $success = 1;
            $message = "New Employee Addition worked!";
        }
        else {
            $success = 0 ;
            $message = "The usernname doesn't exist, but something went wrong.";
        }
    }
    elseif ( $login_check_result[0]->count >= 1 ) {
        $success = 0;
        $message = "Username Already Exists!";
    }
    header("Content-Type: text/xml");
    echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n"; ?>
    <data>
    <NewUserResponse>
    <status><? echo "$success"; ?></status>
    <message><? echo "$message"; ?></message>
    </NewUserResponse>
    </data>
<? }