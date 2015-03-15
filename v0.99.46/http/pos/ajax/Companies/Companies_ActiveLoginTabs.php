<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/companies_functions.php');

if ( $_GET['ActiveTab']) {
    $_SESSION['edit_companies']['ActiveTab'] = $_GET['ActiveTab'];
    companies();
}
?>