<?

include_once("functions.php");
// Create DOM from URL?>
<head>
	<title>Williams Wager Analysis</title>
        <link   type="text/css"         rel="stylesheet" href="includes/wagerStyle.css"/>
	<script type="text/javascript"  src="includes/common.js"></script>
</head>
<?
$Percentages_html = file_get_contents('http://www.covers.com/pageLoader/pageLoader.aspx?page=/data/nba/statistics/2011-2012/ats_regular.html');
$Matchups_html    = file_get_contents('http://www.covers.com/sports/nba/basketball-matchups.aspx');
set_coversTeamID();

#set_percentages($Percentages_html);
#show_matchups($Matchups_html);
show_game_days();

?>
