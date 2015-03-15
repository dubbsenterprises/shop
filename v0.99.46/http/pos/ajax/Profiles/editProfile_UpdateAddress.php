<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/profiles_functions.php');
session_start();
?>
<?php
$dal        = new GENERAL_DAL();
$update_dal = new InsertUpdateDelete_DAL();

if ( isset($_GET['editAddress']) && $_GET['editAddress'] == 1 ) {
    $sql="update addresses
        set address_line1='" . $_GET['NU_Address1']  . "',
            address_line2='" . $_GET['NU_Address2']  . "',
            city='" . $_GET['NU_City']  . "',
            state='" . $_GET['NU_State']  . "',
            last_updated=convert_tz(now(), \"utc\", \"america/chicago\"),
            zipcode='" . $_GET['NU_ZipCode']  . "'
            where address_id = " . $_GET['NU_edit_address_address_id'];
            //print $sql;
        $id = $update_dal->insert_query($sql);
    }
profiles();
?>