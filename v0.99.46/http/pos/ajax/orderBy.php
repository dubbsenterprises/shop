<?php
#include    ('../../../includes/functions.php');
require_once('../../../includes/general_functions.php');
require_once('../../../includes/reports_functions.php');

if ( isset($_GET['column']) ) {
    $_SESSION['search_data']['paging_page'] = 1;
    $_SESSION['search_data']['column'] = $_GET['column'];

    if ( !(isset($_SESSION['search_data']['asc_desc'])  == 'desc') || $_SESSION['search_data']['asc_desc']  == 'desc') {
            $_SESSION['search_data']['asc_desc'] = 'asc';
    }
    elseif ( $_SESSION['search_data']['asc_desc']  == 'asc' ) {
            $_SESSION['search_data']['asc_desc'] = 'desc';
    }
}
?>
