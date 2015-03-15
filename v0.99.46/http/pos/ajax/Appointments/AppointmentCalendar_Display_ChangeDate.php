<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/calendar_functions.php');

if ( $_GET['date']) {
    $_SESSION['calendar']['display_date'] = $_GET['date'];
    calendar();
}
?>