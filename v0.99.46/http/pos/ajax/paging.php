<?php
session_start();
require_once('../../../includes/general_functions.php');

if ( isset($_GET['action']) ) {
    paging_first_next_last($_GET['action'],0);
}
?>
