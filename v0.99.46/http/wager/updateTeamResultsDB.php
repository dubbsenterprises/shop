<?php
include_once 'wager_functions.php';
set_coversTeamID();

if (isset($_GET['season'])) {
    $season = $_GET['season'];
    print ("Getting data for ".$season."<br>");
    foreach( $TeamInfo as $team => $value1){
        $TeamInfo[$team]['coversID'];
        #print "Loading $team and $season ...";
        update_team_results_DB($team,$season);
        #print "Loaded $team and $season <br>";
    }
    print ("DONE!");
} else {
    print ("?season=20xx season Is not passed in.");
}
?>