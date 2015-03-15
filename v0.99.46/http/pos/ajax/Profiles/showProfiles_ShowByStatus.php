<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/profiles_functions.php');
session_start();

if ( $_GET['action']) {
    if ( !(isset($_SESSION['search_data']['Profiles_AllProfiles']['profiles_search_inactive_profiles'])) || $_SESSION['search_data']['Profiles_AllProfiles']['profiles_search_inactive_profiles'] == 1 ) {
        $_SESSION['search_data']['Profiles_AllProfiles']['profiles_search_inactive_profiles'] = 0;
    }
    elseif ($_SESSION['search_data']['Profiles_AllProfiles']['profiles_search_inactive_profiles'] == 0){
        $_SESSION['search_data']['Profiles_AllProfiles']['profiles_search_inactive_profiles'] = 1;
    }
    profiles();
}
?>