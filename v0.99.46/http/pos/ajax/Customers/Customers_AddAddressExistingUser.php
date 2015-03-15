<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/customers_functions.php');
session_start();
?>
<?php
$dal        = new GENERAL_DAL();
$insert_dal = new InsertUpdateDelete_DAL();

if ( isset($_GET['newAddress']) && $_GET['newAddress'] == 1 ) {
    $sql="insert into addresses
        (address_line1,address_line2,city,state,zipcode,country,default_address,customer_id,added)
        values
        (
            '" . $_GET['NU_Address1']  . "',
            '" . $_GET['NU_Address2']  . "',
            '" . $_GET['NU_City']  . "',
            '" . $_GET['NU_State']   . "',
            '" . $_GET['NU_ZipCode']   . "',
            'USA',
            '1',
            " . $_GET['NU_customer_id']  . "," ;
            $sql .= "convert_tz(now(), 'utc', 'america/chicago') )";
            #print $sql;
        $id = $insert_dal->insert_query($sql);
    }
customers();
?>