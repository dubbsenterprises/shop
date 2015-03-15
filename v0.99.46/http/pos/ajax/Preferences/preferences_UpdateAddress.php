<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/preferences_functions.php');

$dal        = new GENERAL_DAL();
$update_dal = new InsertUpdateDelete_DAL();

if ( isset($_GET['editAddress']) && $_GET['editAddress'] == 1 ) {
    $sql="update addresses
        set address_line1='" . $_GET['NU_Address1']  . "',
            address_line2='" . $_GET['NU_Address2']  . "',
            city='" . $_GET['NU_City']  . "',
            state='" . $_GET['NU_State']  . "',
            last_updated=now(),
            zipcode='" . $_GET['NU_ZipCode']  . "',
            google_map_url=" . quoteSmart($_GET['NU_google_map_url'])  . "
            where address_id = " . $_GET['NU_edit_address_address_id'];
            #print $sql;
        $id = $update_dal->insert_query($sql);
    }
preferences();
?>