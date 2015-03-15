<?php
if(!isset($_SESSION)){ session_start(); }
include_once('general_functions.php');
 
list($host,$domain) = setup_path_general();
$_SESSION['MYSQL_HOST']     = 'system3';
$_SESSION['MYSQL_USER']     = 'dubbsenterprises';
$_SESSION['MYSQL_PASS']     = 'W3bW3bW3b';
$_SESSION['MYSQL_DATABASE'] = 'wager';
class WAGER_DAL{
  public function __construct(){}

  public function get_unique_game_days($start_date,$end_date){
    $sql = "SELECT distinct(date) from game_results where date(date) > '$start_date' and date < '$end_date' ;";
    #print $sql . "<br>\n";
    return $this->query($sql);
  }
  public function get_unique_games_by_day($date){
    $sql = "SELECT id, 
                   date,
                   HomeGame,
                   team as home_team,
                   verses as away_team,
                   HomeScore,
                   AwayScore,
                   Spread,
                   SpreadMargin 
        from game_results where date = '$date' and HomeGame=1;";
    #print $sql . "<br>\n";
    return $this->query($sql);
  }

  private function dbconnect(){
    $conn = mysql_connect($_SESSION['MYSQL_HOST'], $_SESSION['MYSQL_USER'], $_SESSION['MYSQL_PASS']) or die ("<br/>Cουld not connect tο MySQL server");
    mysql_select_db($_SESSION['MYSQL_DATABASE'],$conn) or die ("<br/>Cουld nοt select the indicated database");
	return $conn;
  }
  private function query($sql){
    $this->dbconnect();
    $res = mysql_query($sql);
    if ($res){
        if (strpos($sql,'SELECT') === false){
            return true;
        }
    }
    else{
        if (strpos($sql,'SELECT') === false){
            return false;
        }
        else{
            return null;
        }
    }
    $consequences = array();
    while ($row = mysql_fetch_array($res)){
      $result = new DALQueryResult();
      foreach ($row as $k=>$v){
        $result->$k = $v;
      }
      $consequences[] = $result;
    }
    return $consequences;
  }
}

function wager_1_template(){
    ini_set('max_execution_time', 300);
    $random_weights_array   = array();
    exec("/var/media/var_data/domains/dubbsenterprises.com/shop/v0.99.46/http/wager/random.py 10 4",$random_weights_array);
    $random_weights_count                       = count($random_weights_array);
    $_SESSION['season_compute_display_type']    = "Summary1";
    $seasons                                    = array(2009);
    
    #  use this to run only one simulation if $random_weights_count == 0.  Comment IN the 3 lines below to have that happen.
    # $random_weights_count = 0 ;
    # $print_day_data       = 1 ;
    # $print_day_summary    = 1 ;
    $_SESSION['marginWidth']            = 15;
    $_SESSION['daysBack']               = 10;
    $_SESSION['weightATSPcnt']          = 2;
    $_SESSION['weightLASTXATSPcnt']     = 3;
    $_SESSION['weightMargin']           = 1;
    $_SESSION['weightLASTXMargin']      = 4;
    
    
#  Show Default Header HTML
    wager_header();
    foreach ($seasons as $season) {
        #update_season_results_DB($season);
        $run_number             = 1;
        print "Computing Season $season ".$_SESSION['season_compute_display_type']." - ".$random_weights_count;
        if ($random_weights_count > 0) {
            $print_day_data = $print_day_summary = 0;
            for($Randon_Result_Itteration=0;$Randon_Result_Itteration<$random_weights_count;$Randon_Result_Itteration++){
                $data = split(',', $random_weights_array[$Randon_Result_Itteration]);
                if ($data[0] == 0 && $data[1] == 0 && $data[2] == 0 && $data[3] == 0) { next; };
                $_SESSION['weightATSPcnt']      = $data[0] /10;
                $_SESSION['weightLASTXATSPcnt'] = $data[1] /10;
                $_SESSION['weightMargin']       = $data[2] /10;
                $_SESSION['weightLASTXMargin']  = $data[3] /10;
                compute_season($run_number,$season,$print_day_data,$print_day_summary);
                $run_number++;
            }
        } else {
                compute_season($run_number,$season,$print_day_data,$print_day_summary);
                $run_number++;
        }
        ?>
<div class="wp100 d_InlineBlock ">
            <div class="f_left wp100 s07 bcgray ">
                <div class="wp10 f_left">Season <?=$season?></div>
                <div class="wp10 f_left">Best</div>
                <div class="wp10 f_left">Worst</div>
                <div class="wp10 f_left">&nbsp;</div>
                <div class="wp10 f_left">&nbsp;</div>
                <div class="wp10 f_left">&nbsp;</div>
                <div class="wp10 f_left">&nbsp;</div>
                <div class="wp10 f_left">&nbsp; </div>
                <div class="wp09 f_left">&nbsp;</div>
            </div>
            <div class="f_left wp100 s07 ">
                <div class="wp10 f_left">&nbsp;</div>
                <div class="wp10 f_left">&nbsp;<?=sprintf("%01.4f",$_SESSION[$season]['highest_ats_win_percent'])?>%</div>
                <div class="wp10 f_left">&nbsp;<?=sprintf("%01.4f",$_SESSION[$season]['lowest_ats_win_percent'])?>%</div>
                <div class="wp10 f_left">&nbsp;</div>
                <div class="wp10 f_left">&nbsp;</div>
                <div class="wp10 f_left">&nbsp;</div>
                <div class="wp10 f_left">&nbsp;</div>
                <div class="wp10 f_left">&nbsp;</div>
                <div class="wp09 f_left">&nbsp;</div>
            </div>
        </div>
        <?
    }
}
    function wager_header(){
?>
<head>
    <title>Williams Wager Analysis</title>
    <META name="description" content="">
    <META name="keywords" content="">
    <link rel="stylesheet" type="text/css"  href="pos/includes/pos.css">
    <script type="text/javascript"          src="pos/includes/jQueryJS/jquery-1.4.4.min.js"></script>
    <script type="text/javascript"          src="pos/includes/jQueryJS/jquery-ui-1.8.9.custom.min.js"></script>

    <link   rel="stylesheet" type="text/css"    href="/common_includes/colors_styles.php?style=Include" media="screen">
    <script type="text/javascript"              src= "common_includes/common.js"></script>
</head>
<?
}

function compute_season($run_number,$season,$print_day_data,$print_day_summary){
    ob_start();
    $wager_dal          = new WAGER_DAL();
    $day_count          = $total_games_this_season = 0;

    $season_next_year   = $season + 1;
    $start_date         = $season."-10-15";
    $end_date           = $season_next_year."-05-15";
    $unique_days_results= $wager_dal->get_unique_game_days($start_date,$end_date);
    $number_of_days     = count($unique_days_results);

    $_SESSION['PERCENTAGES']['bcTeamName']['home_team'] = $_SESSION['PERCENTAGES']['bcTeamName']['away_team'] = '';
    trigger_error("Running $season - Test #$run_number/");
    foreach ($unique_days_results as $result) {
        if ($day_count == 0) {
            unset($_SESSION['stats']);
            $_SESSION['stats']['wager_team_wins']               = .0000010;
            $_SESSION['stats']['wager_team_loss']               = .0000010;
        }
        $day_count++;

        $this_days_game_count = 1;
        $_SESSION['stats'][$result->date]['per_date_wager_team_wins'] = 0;
        $_SESSION['stats'][$result->date]['per_date_wager_team_loss'] = 0;
        $games_on_this_day = $wager_dal->get_unique_games_by_day($result->date);
        $total_games_this_season += count($games_on_this_day);

        if ($print_day_data == 1 || $print_day_summary == 1 ) {?>
            <div id="game_day_<?=$result->date?>" style="display:inline;" >
        <? } 
            foreach ($games_on_this_day as $game_result) {
                $_SESSION['PERCENTAGES']['bcWinnerTeamName']['away_team'] = $_SESSION['PERCENTAGES']['bcWinnerTeamName']['home_team'] = $_SESSION['PERCENTAGES']['bcTeamName']['home_team'] = $_SESSION['PERCENTAGES']['bcTeamName']['away_team'] = '';
                ########################################################################
                #### cap off the SpreadMargin for blow outs.
                if ( $game_result->SpreadMargin >=  $_SESSION['marginWidth'] )          { $game_result->SpreadMargin =  $_SESSION['marginWidth'];        }
                if ( $game_result->SpreadMargin <= ($_SESSION['marginWidth']*-1)+.1 )   { $game_result->SpreadMargin = ($_SESSION['marginWidth']*-1);    }

                ########################################################################
                #### If this is the 1st time we have seen a tesam, it needs a Power Ranking
                if ( !(isset($_SESSION['stats'][$game_result->home_team]['Current_PowerRanking']))) { $_SESSION['stats'][$game_result->home_team]['Current_PowerRanking'] = 0; }
                if ( !(isset($_SESSION['stats'][$game_result->away_team]['Current_PowerRanking']))) { $_SESSION['stats'][$game_result->away_team]['Current_PowerRanking'] = 0; }

                ########################################################################
                ########################################################################
                ###  Here is where we decide if, based on the Current PowerRanking of each team and the resultant SpreadMargin of this game, would we have won the bet or lost?
                ###  But we only do that if both teams are over the number of gamesBack multiplied by 2.
                if (
                     ( isset($_SESSION['stats'][$game_result->home_team]['Team_Total_Game_Count']) && $_SESSION['stats'][$game_result->home_team]['Team_Total_Game_Count'] > (($_SESSION['daysBack']-1)*2) ) &&
                     ( isset($_SESSION['stats'][$game_result->away_team]['Team_Total_Game_Count']) && $_SESSION['stats'][$game_result->away_team]['Team_Total_Game_Count'] > (($_SESSION['daysBack']-1)*2) )
                    ) {
                        if   ( $game_result->SpreadMargin >= 0 ) { # the Home team won.
                            ##  The Home team won the game based on the spread margin value.  Now, who did i pick to win based on the CURRENT Power Ranking?
                            if ( $_SESSION['stats'][$game_result->home_team]['Current_PowerRanking']      > $_SESSION['stats'][$game_result->away_team]['Current_PowerRanking'] ) {
                                ##  I picked the home team beacause their power ranking was bigger PLUS the HOME team WON!
                                $_SESSION['stats']['wager_team_wins']++;
                                $_SESSION['stats'][$result->date]['per_date_wager_team_wins']++;
                                $_SESSION['PERCENTAGES']['bcTeamName']['away_team'] = '';
                                $_SESSION['PERCENTAGES']['bcTeamName']['home_team'] = 'bclightgreen';
                            } else {  # I Picked the AWAY team but the HOME team won.  im a loser.
                                $_SESSION['PERCENTAGES']['bcTeamName']['away_team'] = 'bcred';
                                $_SESSION['PERCENTAGES']['bcTeamName']['home_team'] = '';
                                $_SESSION['stats']['wager_team_loss']++;
                                $_SESSION['stats'][$result->date]['per_date_wager_team_loss']++;
                            }
                            $_SESSION['PERCENTAGES']['bcWinnerTeamName']['home_team'] = 'bclightgreen';
                        } else {
                            ##  The Away team won the game based on the spread margin value.  Now, who did i pick to win based on the CURRENT Power Ranking?
                            if ( $_SESSION['stats'][$game_result->away_team]['Current_PowerRanking']      > $_SESSION['stats'][$game_result->home_team]['Current_PowerRanking'] ) {
                                ##  I picked the Away team and the spread margin value of the games says the away team won.
                                $_SESSION['stats']['wager_team_wins']++;
                                $_SESSION['stats'][$result->date]['per_date_wager_team_wins']++;
                                $_SESSION['PERCENTAGES']['bcTeamName']['away_team'] = 'bclightgreen';
                                $_SESSION['PERCENTAGES']['bcTeamName']['home_team'] = '';
                            } else {  # I Picked the Home team but the Away team won.  im a loser.
                                $_SESSION['PERCENTAGES']['bcTeamName']['away_team'] = '';
                                $_SESSION['PERCENTAGES']['bcTeamName']['home_team'] = 'bcred';
                                $_SESSION['stats']['wager_team_loss']++;
                                $_SESSION['stats'][$result->date]['per_date_wager_team_loss']++;
                            }
                            $_SESSION['PERCENTAGES']['bcWinnerTeamName']['away_team'] = 'bclightgreen';
                        }
                }
                $_SESSION['PERCENTAGES']['titleTeamName']['home_team'] = sprintf("%01.4f", $_SESSION['stats'][$game_result->home_team]['Current_PowerRanking']) . " - ".$game_result->home_team;
                $_SESSION['PERCENTAGES']['titleTeamName']['away_team'] = sprintf("%01.4f", $_SESSION['stats'][$game_result->away_team]['Current_PowerRanking']) . " - ".$game_result->away_team;

                play_game($game_result,'home_team');
                play_game($game_result,'away_team');

                ########################################################################
                ##  Here we update how the team is doing on the season ATS
                if ( $game_result->SpreadMargin >= 0  )  {
                    $_SESSION['stats'][$game_result->away_team]['Team_Total_Loss_ATS']     += 1;
                    $_SESSION['stats'][$game_result->home_team]['Team_Total_Wins_ATS']     += 1;
                } else if( $game_result->SpreadMargin < 0  ) {
                    $_SESSION['stats'][$game_result->away_team]['Team_Total_Wins_ATS']     += 1;
                    $_SESSION['stats'][$game_result->home_team]['Team_Total_Loss_ATS']     += 1;
                }
                ########################################################################
                print_game_result($game_result,$print_day_data,$this_days_game_count,count($games_on_this_day));
                $this_days_game_count++ ;
            }
            if ($print_day_summary == 1) {?>
                <div class="wp100 d_InlineBlock " style="display:inline;" >
                    <div class="f_left wp100 s07 bcgray ">
                        <div class="wp10 f_left">Results for <?=$result->date?></div>
                        <div class="wp10 f_left">Wins</div>
                        <div class="wp10 f_left">Losses</div>
                        <div class="wp10 f_left">&nbsp;</div>
                        <div class="wp10 f_left">Season Wins</div>
                        <div class="wp10 f_left">Season Loss</div>
                        <div class="wp10 f_left">Season ATS %</div>
                        <div class="wp10 f_left">&nbsp;</div>
                        <div class="wp09 f_left">&nbsp;</div>
                    </div>
                    <div class="f_left wp100 s07 ">
                        <div class="wp10 f_left">&nbsp;</div>
                        <div class="wp10 f_left">&nbsp;<?=$_SESSION['stats'][$result->date]['per_date_wager_team_wins']?></div>
                        <div class="wp10 f_left">&nbsp;<?=$_SESSION['stats'][$result->date]['per_date_wager_team_loss']?></div>
                        <div class="wp10 f_left">&nbsp;</div>
                        <div class="wp10 f_left">&nbsp;<?=$_SESSION['stats']['wager_team_wins']?></div>
                        <div class="wp10 f_left">&nbsp;<?=$_SESSION['stats']['wager_team_loss']?></div>
                        <div class="wp10 f_left">&nbsp;
                            <? if ($_SESSION['stats']['wager_team_wins']!=0) { ?>
                                   <?=sprintf("%01.2f",(($_SESSION['stats']['wager_team_wins'] / ($_SESSION['stats']['wager_team_wins'] + $_SESSION['stats']['wager_team_loss']))* 100))?>%
                            <?} else {?>
                                   0.0 %
                            <? } ?>
                        </div>
                        <div class="wp10 f_left">&nbsp;</div>
                        <div class="wp09 f_left">&nbsp;</div>
                    </div>
                </div>
            <? }
        if ($print_day_data == 1 || $print_day_summary == 1 ) {?>
        </div>
        <? }
    }
    
    if ($_SESSION['season_compute_display_type'] != "Summary"){?>
        <div class="wp100 d_InlineBlock " id="weights_run_<?=$run_number?>">
            <div class="f_left wp100 s07 bcgray ">
                <div class="wp10 f_left"># <?=$run_number?> - Season Win %</div>
                <div class="wp10 f_left">Wins</div>
                <div class="wp10 f_left">Losses</div>
                <div class="wp10 f_left">&nbsp;weight ATS %</div>
                <div class="wp10 f_left">&nbsp;weight LAST XATS %</div>
                <div class="wp10 f_left">&nbsp; weight Margin</div>
                <div class="wp10 f_left s07">&nbsp;weight LAST X Margin</div>
                <div class="wp10 f_left">&nbsp; margin Width</div>
                <div class="wp09 f_left">&nbsp;days Back</div>
            </div>
            <div class="f_left wp100 s07 ">
                <div class="wp10 f_left">&nbsp;<?=sprintf("%01.2f",(        ($_SESSION['stats']['wager_team_wins'] / ($_SESSION['stats']['wager_team_wins'] + $_SESSION['stats']['wager_team_loss']) )* 100))?>%</div>
                <div class="wp10 f_left">&nbsp;<?=$_SESSION['stats']['wager_team_wins']?></div>
                <div class="wp10 f_left">&nbsp;<?=$_SESSION['stats']['wager_team_loss']?></div>
                <div class="wp10 f_left">&nbsp;<?=$_SESSION['weightATSPcnt']?></div>
                <div class="wp10 f_left">&nbsp;<?=$_SESSION['weightLASTXATSPcnt']?></div>
                <div class="wp10 f_left">&nbsp;<?=$_SESSION['weightMargin']?></div>
                <div class="wp10 f_left">&nbsp;<?=$_SESSION['weightLASTXMargin'] ?></div>
                <div class="wp10 f_left">&nbsp;<?=$_SESSION['marginWidth']?></div>
                <div class="wp09 f_left">&nbsp;<?=$_SESSION['daysBack']?></div>
            </div>
        </div>
    <?}
    
    if ( !(isset($_SESSION[$season]['highest_ats_win_percent'])) || ( ((    ($_SESSION['stats']['wager_team_wins'] / ($_SESSION['stats']['wager_team_wins'] + $_SESSION['stats']['wager_team_loss']) )* 100) > $_SESSION[$season]['highest_ats_win_percent'] ) ) ){
        $_SESSION[$season]['highest_ats_win_percent'] =                     ($_SESSION['stats']['wager_team_wins'] / ($_SESSION['stats']['wager_team_wins'] + $_SESSION['stats']['wager_team_loss']) );
    }
    
    if ( !(isset($_SESSION[$season]['lowest_ats_win_percent'] )) || ( ((    ($_SESSION['stats']['wager_team_wins'] / ($_SESSION['stats']['wager_team_wins'] + $_SESSION['stats']['wager_team_loss']) )* 100) < $_SESSION[$season]['lowest_ats_win_percent']  ) ) ){
        $_SESSION[$season]['lowest_ats_win_percent']  =                     ($_SESSION['stats']['wager_team_wins'] / ($_SESSION['stats']['wager_team_wins'] + $_SESSION['stats']['wager_team_loss']));
}
}
    function play_game($game_result,$team_type){
        $multiplier['home_team'] = -1;
        $multiplier['away_team'] =  1;
        if (!(isset($_SESSION['stats'][$game_result->$team_type]['Team_Total_Wins_ATS'])))        { $_SESSION['stats'][$game_result->$team_type]['Team_Total_Wins_ATS']     = 0; }
        if (!(isset($_SESSION['stats'][$game_result->$team_type]['Team_Total_Loss_ATS'])))        { $_SESSION['stats'][$game_result->$team_type]['Team_Total_Loss_ATS']     = 0; }
        if (!(isset($_SESSION['stats'][$game_result->$team_type]['Team_Total_Game_Count'])))      { $_SESSION['stats'][$game_result->$team_type]['Team_Total_Game_Count']   = 1;                    }   else { $_SESSION['stats'][$game_result->$team_type]['Team_Total_Game_Count']++; }
        if (!(isset($_SESSION['stats'][$game_result->$team_type]['SpreadMargin'])))               { $_SESSION['stats'][$game_result->$team_type]['SpreadMargin'] = $multiplier[$team_type]*($game_result->SpreadMargin); }   else { $_SESSION['stats'][$game_result->$team_type]['SpreadMargin']+= -1*($game_result->SpreadMargin) ; }

        unset($_SESSION['PERCENTAGES']['Win_Percent'][$game_result->$team_type]);
        unset($_SESSION['PERCENTAGES']['weightATSPercent'][$game_result->$team_type]);
        unset($_SESSION['PERCENTAGES']['Season_Spread_Margin'][$game_result->$team_type]);

        ## Season WIN ATS
            if (isset($_SESSION['stats'][$game_result->$team_type]['Team_Total_Wins_ATS']) && $_SESSION['stats'][$game_result->$team_type]['Team_Total_Wins_ATS'] > 0  ) {
                $_SESSION['PERCENTAGES']['Win_Percent'][$game_result->$team_type]           = ( $_SESSION['stats'][$game_result->$team_type]['Team_Total_Wins_ATS']               / ($_SESSION['stats'][$game_result->$team_type]['Team_Total_Wins_ATS'] + $_SESSION['stats'][$game_result->$team_type]['Team_Total_Loss_ATS'])) * 100;
                $_SESSION['PERCENTAGES']['weightATSPercent'][$game_result->$team_type]      = ( $_SESSION['PERCENTAGES']['Win_Percent'][$game_result->$team_type] / 100 ) * $_SESSION['weightATSPcnt'];
            } else {
                $_SESSION['PERCENTAGES']['weightATSPercent'][$game_result->$team_type]      = 0 ;
                $_SESSION['PERCENTAGES']['Win_Percent'][$game_result->$team_type]           = 0;
            }            

        ## Last X WIN ATS
        # before i use this game's data.  i need to get a Last X WIN ATS value.  i will use this in the powerRanking for the
        # bet determination factor.
            if (  isset($_SESSION['stats'][$game_result->$team_type]['daysBackATS']) && count($_SESSION['stats'][$game_result->$team_type]['daysBackATS']) >=  ($_SESSION['daysBack']-1)) {
                $count=0;
                foreach ($_SESSION['stats'][$game_result->$team_type]['daysBackATS'] as $Margin) { if ($Margin >= 0 ) { $count++; } }
                $_SESSION['PERCENTAGES']['Last_X_ATS'][$game_result->$team_type] = ($count / ($_SESSION['daysBack']-1)) ;
            }            
            if (!(isset($_SESSION['stats'][$game_result->$team_type]['daysBackATS']))) {$_SESSION['stats'][$game_result->$team_type]['daysBackATS'] = array();}
            if (        $_SESSION['stats'][$game_result->$team_type]['Team_Total_Game_Count'] >= $_SESSION['daysBack']) {array_pop($_SESSION['stats'][$game_result->$team_type]['daysBackATS']); array_unshift($_SESSION['stats'][$game_result->$team_type]['daysBackATS'],-1*$game_result->SpreadMargin); }
            else {array_unshift($_SESSION['stats'][$game_result->$team_type]['daysBackATS'],$multiplier[$team_type]*$game_result->SpreadMargin);}
            $_SESSION['PERCENTAGES']['Last_X_ATS_Weight'][$game_result->$team_type] = $_SESSION['PERCENTAGES']['Last_X_ATS'][$game_result->$team_type] * $_SESSION['weightLASTXATSPcnt'];

        ## Spread Margin
            $_SESSION['PERCENTAGES']['Season_Spread_Margin'][$game_result->$team_type]  = ($_SESSION['stats'][$game_result->$team_type]['SpreadMargin']                     / $_SESSION['stats'][$game_result->$team_type]['Team_Total_Game_Count']);
            $_SESSION['PERCENTAGES']['weight_Margin'][$game_result->$team_type]         = ( (($_SESSION['PERCENTAGES']['Season_Spread_Margin'][$game_result->$team_type] + $_SESSION['marginWidth'] ))                              / ($_SESSION['marginWidth']*2)) * $_SESSION['weightMargin'];

        ## Last X Avg Margin
            # before i use this game's data.  i need to get a Last X Magin value.  i will use this in the powerRanking for the
            # bet determination factor.
            if (isset($_SESSION['stats'][$game_result->$team_type]['daysBackAVGmargin']) && count($_SESSION['stats'][$game_result->$team_type]['daysBackAVGmargin']) >=  ($_SESSION['daysBack']-1)) {
                $TmpMargin=0;
                foreach ($_SESSION['stats'][$game_result->$team_type]['daysBackAVGmargin'] as $Margin) { $TmpMargin += $Margin; }
                $_SESSION['PERCENTAGES']['Last_X_ATS_MARGIN'][$game_result->$team_type] = ($TmpMargin/($_SESSION['daysBack']-1)) ;
            }
            if (!(isset($_SESSION['stats'][$game_result->$team_type]['daysBackAVGmargin']))) {$_SESSION['stats'][$game_result->$team_type]['daysBackAVGmargin'] = array();}
            if($_SESSION['stats'][$game_result->$team_type]['Team_Total_Game_Count'] >= $_SESSION['daysBack']) {array_pop($_SESSION['stats'][$game_result->$team_type]['daysBackAVGmargin']); array_unshift($_SESSION['stats'][$game_result->$team_type]['daysBackAVGmargin'],-1*$game_result->SpreadMargin); }
            else {array_unshift($_SESSION['stats'][$game_result->$team_type]['daysBackAVGmargin'],$multiplier[$team_type]*$game_result->SpreadMargin);}            

            $_SESSION['PERCENTAGES']['Last_X_ATS_MARGIN_WEIGHT'][$game_result->$team_type] = ( (($_SESSION['PERCENTAGES']['Last_X_ATS_MARGIN'][$game_result->$team_type] + $_SESSION['marginWidth'] )) / ($_SESSION['marginWidth']*2)) * $_SESSION['weightLASTXMargin'] ;

        ########################################################################
            $_SESSION['PERCENTAGES']['powerRank'][$game_result->$team_type]              = $_SESSION['PERCENTAGES']['weightATSPercent'][$game_result->$team_type] + $_SESSION['PERCENTAGES']['Last_X_ATS_Weight'][$game_result->$team_type] + $_SESSION['PERCENTAGES']['weight_Margin'][$game_result->$team_type] + $_SESSION['PERCENTAGES']['Last_X_ATS_MARGIN_WEIGHT'][$game_result->$team_type];
            $_SESSION['stats'][$game_result->$team_type]['Current_PowerRanking'] = $_SESSION['PERCENTAGES']['powerRank'][$game_result->$team_type];
    }
    function print_game_result($game_result,$print_day_data,$game_on_this_day_count,$number_of_games_on_this_day){
             if ($print_day_data == 1 ) {
                if ($game_on_this_day_count == 1) {?>
                    <div class="wp100 f_left left  ">
                        <?=$game_result->date;?> number on games on this day  <?=$number_of_games_on_this_day?>
                    </div>
                <? } ?>
                    <div class="wp100 d_InlineBlock bcred " style="display:inline;" >
                        <div class="f_left wp100 s07 bcgray ">
                            <div class="wp15 f_left">Team & Spread(H) S:<?=$game_result->Spread?> - M:<?=$game_result->SpreadMargin?></div>
                            <div class="wp08 f_left bclightblue">Season ATS Win%</div>
                            <div class="wp08 f_left bclightblue">Win Weight - <?=$_SESSION['weightATSPcnt']?></div>
                            <div class="wp09 f_left">Last <?=$_SESSION['daysBack']-1?> Win%</div>
                            <div class="wp09 f_left">Last <?=$_SESSION['daysBack']-1?> Weight - <?=$_SESSION['weightLASTXATSPcnt']?></div>
                            <div class="wp09 f_left bclightblue">Season Avg. Margin</div>
                            <div class="wp09 f_left bclightblue">Margin Weight - <?=$_SESSION['weightMargin']?></div>
                            <div class="wp09 f_left">Last <?=$_SESSION['daysBack']-1?> Margin</div>
                            <div class="wp09 f_left s07">L. <?=$_SESSION['daysBack']-1?> Marg Weight- <?=$_SESSION['weightLASTXMargin'] ?></div>
                            <div class="wp09 f_left">Power Rank</div>
                        </div>
                        <?=display_game_result($game_result,'away_team','AwayScore');?>
                        <?=display_game_result($game_result,'home_team','HomeScore');?>
                    </div>
                <?}        
    }
        function display_game_result($game_result,$team_type,$score_type){?>
        <div class="f_left wp100 s07 ">
            <div class="wp15 f_left no-overflow <?=$_SESSION['PERCENTAGES']['bcTeamName'][$team_type]?>" title="<?=$_SESSION['PERCENTAGES']['titleTeamName'][$team_type]?>"> 
                <?=$team_type?>:<?=$game_result->$team_type?> - <?=$game_result->$score_type?>
            </div>
            <div class="wp08 f_left"><?=sprintf("%01.2f", $_SESSION['PERCENTAGES']['Win_Percent'][$game_result->$team_type])?></div>
            <div class="wp08 f_left"><?=sprintf("%01.2f", $_SESSION['PERCENTAGES']['weightATSPercent'][$game_result->$team_type])?></div>
            <div class="wp09 f_left">&nbsp;<?=sprintf("%01.2f", $_SESSION['PERCENTAGES']['Last_X_ATS'][$game_result->$team_type])?></div>
            <div class="wp09 f_left">&nbsp;<?=sprintf("%01.2f", $_SESSION['PERCENTAGES']['Last_X_ATS_Weight'][$game_result->$team_type])?></div>
            <div class="wp09 f_left"><?=sprintf("%01.2f",$_SESSION['PERCENTAGES']['Season_Spread_Margin'][$game_result->$team_type])?></div>
            <div class="wp09 f_left"><?=sprintf("%01.2f",$_SESSION['PERCENTAGES']['weight_Margin'][$game_result->$team_type])?></div>
            <div class="wp09 f_left">&nbsp;<?=sprintf("%01.2f",$_SESSION['PERCENTAGES']['Last_X_ATS_MARGIN'][$game_result->$team_type])?></div>
            <div class="wp09 f_left">&nbsp;<?=sprintf("%01.2f",$_SESSION['PERCENTAGES']['Last_X_ATS_MARGIN_WEIGHT'][$game_result->$team_type])?></div>
            <div class="wp09 f_left <?=$_SESSION['PERCENTAGES']['bcTeamName'][$team_type]?>">
                <?=sprintf("%01.4f", $_SESSION['PERCENTAGES']['powerRank'][$game_result->$team_type])?>
            </div>
        </div>
    <?}

    
    
function download_season_stats($season){
        $season_next_year   = $season + 1;
        $seasoon_url        = "http://www.covers.com/pageLoader/pageLoader.aspx?page=/data/nba/statistics/".$season."-".$season_next_year."/ats_regular.html";

        $Percentages_html   = file_get_contents();
	$Todays_matchups    = file_get_contents('http://www.covers.com/sports/nba/basketball-matchups.aspx');

        set_coversTeamID();
	//set_percentages($Percentages_html);
	//show_matchups($Todays_matchups);
}
function set_percentages($html) {
    #$html = file_get_contents('http://www.yahoo.com');
    // Find all article blocks
    #$newlines = array("\t","\n","\r","\x20\x20","\0","\x0B");
    $newlines = array("\x20\x20","\0","\x0B");
    $content = str_replace($newlines, "", html_entity_decode($html));

    $start = strpos($content,'<h3>NBA ATS Records</h3>');
    $end = strpos($content,'</div>',$start) + 18;
    $html = substr($content,$start,$end-$start);
    $dom = new domDocument;
    $dom->loadHTML($html);
    $dom->preserveWhiteSpace = false;
    $tables = $dom->getElementsByTagName('table');
    $rows = $tables->item(0)->getElementsByTagName('tr');
    $count = 0;
    foreach ($rows as $row)
        {
        if ($count < 1  ) { $count++; continue; }
        $cols = $row->getElementsByTagName('td');
        $ATSrecord  = $cols->item(2)->nodeValue;
        list($wins1, $loss, $tie) = split('-', $ATSrecord);
        $games = $wins1 + $loss + $tie;
        $wins  = $wins1 + $tie ;
        $ATSrecord = $wins."-".$loss;
        $win_percent1 = ($wins / $games) * 100;
        $win_percent = sprintf("%0.2f", $win_percent1);

        $record     = $cols->item(3)->nodeValue;
        $Team       = trim($cols->item(1)->nodeValue);
        $Margin     = ltrim($cols->item(7)->nodeValue);
        $_SESSION['PERCENTAGES'][$Team]['winpercent'] = $win_percent;
        $_SESSION['PERCENTAGES'][$Team]['Margin']     = $Margin;
        $_SESSION['PERCENTAGES'][$Team]['place']      = $count;

        set_last_five_games_stats($Team);
        $count++;
    }
}
function show_matchups($html) {
    #$html = file_get_contents('http://www.yahoo.com');
    // Find all article blocks
    #$newlines = array("\t","\n","\r","\x20\x20","\0","\x0B");
    $newlines = array("\x20\x20","\0","\x0B");
    $content = str_replace($newlines, "", html_entity_decode($html));

    $start = strpos($content,'<table width="100%" cellpadding="2" cellspacing="1" class="data">');
    $end = strpos($content,'</table>',$start) + 8;
    $html2 = substr($content,$start,$end-$start);
    $dom = new domDocument;
    $dom->loadHTML($html2);
    $dom->preserveWhiteSpace = false;
    $tables = $dom->getElementsByTagName('table');
    $rows = $tables->item(0)->getElementsByTagName('tr');
    $count = 0;

    $_SESSION['marginWidth']  = 15 ;
    $_SESSION['weightMargin'] = .1;
    $LastXWeightMargin = .2;

    $_SESSION['weightATSPcnt']= .2;
    $LastXweightATSPcnt= .5;

    print "<table border=1>\n";
    foreach ($rows as $row)
    {
        if ($count < 1  ) { $count++; continue; }
        ########################################################################
        $cols = $row->getElementsByTagName('td');
            $matchup  = $cols->item(2)->getElementsByTagName('a');
                $awayTeam = $matchup->item(0)->nodeValue;
                $homeTeam = $matchup->item(1)->nodeValue;
            #$record     = $cols->item(3)->nodeValue;
        $count++;
        ########################################################################

        ########################################################################
        $percentHome                = sprintf("%0.2f", ( $_SESSION['PERCENTAGES'][$homeTeam]['winpercent'] / 100 ) );
        $LastXATSpercentHome        = $TeamInfo[$homeTeam]['LastXGamesWinPercent'];
        ########
        $_SESSION['PERCENTAGES']['weightATSPercent'][$team_type]       = $percentHome * $_SESSION['weightATSPcnt'];
        $LastXweightATSPercentHome  = $TeamInfo[$homeTeam]['LastXGamesWinPercent'] * $_SESSION['weightATSPcnt'];

        if ( $_SESSION['PERCENTAGES'][$homeTeam]['Margin'] >=  15 )   { $_SESSION['PERCENTAGES'][$homeTeam]['Margin'] =  15;   }
        if ( $_SESSION['PERCENTAGES'][$homeTeam]['Margin'] <= -14.9 ) { $_SESSION['PERCENTAGES'][$homeTeam]['Margin'] = -14.9; }
        $marginHome             = sprintf("%0.2f", ( $_SESSION['PERCENTAGES'][$homeTeam]['Margin']              + $_SESSION['marginWidth'] ) / ($_SESSION['marginWidth']*2) );
        $LastXmarginHome        = sprintf("%0.2f", ( $TeamInfo[$homeTeam]['LastXGamesMargin']   + $_SESSION['marginWidth'] ) / ($_SESSION['marginWidth']*2) );
        $_SESSION['weightMargin']       = $marginHome       * $_SESSION['weightMargin'];
        $weightLastXmarginHome  = $LastXmarginHome  * $LastXWeightMargin;
        ########################################################################
        $_SESSION['PERCENTAGES']['powerRank'][$team_type]      = $_SESSION['weightMargin'] + $_SESSION['PERCENTAGES']['weightATSPercent'][$team_type] + $weightLastXmarginHome + $LastXweightATSPercentHome;;

        ##
        ##

        ########################################################################
        $percentAway                = sprintf("%0.2f", ( $_SESSION['PERCENTAGES'][$awayTeam]['winpercent'] / 100 ) );
        $LastXATSpercentAway        = $TeamInfo[$awayTeam]['LastXGamesWinPercent'];
        ########
        $_SESSION['PERCENTAGES']['weightATSPercent'][$team_type]       = $percentAway * $_SESSION['weightATSPcnt'];
        $LastXweightATSPercentAway  = $TeamInfo[$awayTeam]['LastXGamesWinPercent'] * $_SESSION['weightATSPcnt'];

        if ( $_SESSION['PERCENTAGES'][$awayTeam]['Margin'] >=  15   ) { $_SESSION['PERCENTAGES'][$awayTeam]['Margin'] =  15;   }
        if ( $_SESSION['PERCENTAGES'][$awayTeam]['Margin'] <= -14.9 ) { $_SESSION['PERCENTAGES'][$awayTeam]['Margin'] = -14.9; }
        $marginAway         = sprintf("%0.2f", ( $_SESSION['PERCENTAGES'][$awayTeam]['Margin']          + $_SESSION['marginWidth'] ) / ($_SESSION['marginWidth']*2) );
        $LastXmarginAway    = sprintf("%0.2f", ( $TeamInfo[$awayTeam]['LastXGamesMargin']   + $_SESSION['marginWidth'] ) / ($_SESSION['marginWidth']*2) );
        $_SESSION['weightMargin']       = $marginAway       * $_SESSION['weightMargin'];
        $weightLastXmarginAway  = $LastXmarginAway  * $LastXWeightMargin;
        ########################################################################
        $_SESSION['PERCENTAGES']['powerRank'][$team_type]      = $_SESSION['weightMargin'] + $_SESSION['PERCENTAGES']['weightATSPercent'][$team_type] + $weightLastXmarginAway + $LastXweightATSPercentAway;

        $alt = "bgcolor=#ADD8E6";
        $reg = "bgcolor=#COCOCO";
        ########################################################################
        print "<tr border=2>\n";
            print "<td>Team and ATS Rank</td>\n";
            print "<td $alt>Win%</td>\n";
            print "<td $alt>Win Weight</td>\n";
            print "<td $reg>Last ".$_SESSION['gamesback']." Win%</td>\n";
            print "<td $reg>Last ".$_SESSION['gamesback']." Win Weight</td>\n";
            print "<td $alt>Margin</td>\n";
            print "<td $alt>Margin Weight</td>\n";
            print "<td $reg>Last ".$_SESSION['gamesback']." Margin</td>\n";
            print "<td $reg>Last ".$_SESSION['gamesback']." Margin Weight</td>\n";
            print "<td>Power Rank</td>\n";
        print "</tr>\n";

        print "<tr border=2>\n";
            print "<td align=center >$awayTeam #".$_SESSION['PERCENTAGES'][$awayTeam]['place']."</td>\n";
            print "<td $alt align=center> ". $percentAway ." %</td>\n";
            print "<td $alt align=center> ". $_SESSION['PERCENTAGES']['weightATSPercent'][$team_type] ."</td>\n";

            print "<td $reg align=center> ". $LastXATSpercentAway ." %</td>\n";
            print "<td $reg align=center> ". $LastXweightATSPercentAway ."</td>\n";

            print "<td $alt align=center> ". $marginAway     ."</td>\n";
            print "<td $alt align=center> ". $_SESSION['weightMargin']     ."</td>\n";

            print "<td $reg align=center> ". $LastXmarginAway     ."</td>\n";
            print "<td $reg align=center> ". $weightLastXmarginAway     ."</td>\n";

            print "<td align=center> ". $_SESSION['PERCENTAGES']['powerRank'][$team_type]     ."</td>\n";
        print "</tr>\n";

        print "<tr>\n";
            print "<td align=center>$homeTeam  #".$_SESSION['PERCENTAGES'][$homeTeam]['place']."</td>\n";
            print "<td $alt align=center> ". $percentHome ." %</td>\n";
            print "<td $alt align=center> ". $_SESSION['PERCENTAGES']['weightATSPercent'][$team_type] ."</td>\n";

            print "<td $reg align=center> ". $LastXATSpercentHome ." %</td>\n";
            print "<td $reg align=center> ". $LastXweightATSPercentHome ."</td>\n";

            print "<td $alt align=center> ". $marginHome     ."</td>\n";
            print "<td $alt align=center> ". $_SESSION['weightMargin']     ."</td>\n";

            print "<td $reg align=center> ". $LastXmarginHome     ."</td>\n";
            print "<td $reg align=center> ". $weightLastXmarginHome     ."</td>\n";

            print "<td align=center> ". $_SESSION['PERCENTAGES']['powerRank'][$team_type]     ."</td>\n";
        print "</tr>\n";

        print "<tr height=1>\n";
            print "<td >&nbsp</td>\n";
        print "</tr>\n";
    }
    print "</table>\n";
}
function set_last_five_games_stats($team,$dateCalcFrom = '12/22/10') {
    global $TeamInfo;
    $html = file_get_contents("http://www.covers.com/pageLoader/pageLoader.aspx?page=/data/nba/teams/pastresults/2010-2011/team".$TeamInfo[$team]['coversID'].".html");
    $newlines = array("\x20\x20","\0","\x0B");
    $content = str_replace($newlines, "", html_entity_decode($html));

    $start = strpos($content,'<h3>Regular Season</h3>');
    $end = strpos($content,'</table>',$start) + 18;
    $html = substr($content,$start,$end-$start);
    $dom = new domDocument;
    $dom->loadHTML($html);
    $dom->preserveWhiteSpace = false;
    $tables = $dom->getElementsByTagName('table');
    $rows = $tables->item(0)->getElementsByTagName('tr');
    $count = 0; $totalHomeScore = 0; $totalAwayScore = 0;
    foreach ($rows as $row)
    {
        $$gameCount =0;
        if ($count < 1  ) { $count++; continue; }
        $gameData = $row->getElementsByTagName('td');
        $date   = ltrim($gameData->item(0)->nodeValue);
        if ( $dateCalcFrom > $date && $gameCount < $_SESSION['gamesback'] ) {
            $verses     = $gameData->item(1)->nodeValue;
            $ScoreData  = $gameData->item(2)->getElementsByTagName('a');
            $Score      = $ScoreData->item(0)->nodeValue;
            list($ScoreHome, $ScoreAway) = split('-', $Score);
            $Spread     = $gameData->item(4)->nodeValue;
            list($result, $Spread) = split(' ', $Spread);
            $DidTheyCover = $ScoreHome - $ScoreAway + $Spread;
            if     ( $DidTheyCover == 0) { $calculaterSpreadResult = 1 ; }  # if they push make it a win
            elseif ( $DidTheyCover < 0 ) { $calculaterSpreadResult = -1; }
            elseif ( $DidTheyCover > 0 ) { $calculaterSpreadResult = 1 ; }

            #print "DATE:$date. $verses - HScore:$ScoreHome , AScore:$ScoreAway  Spread:$Spread COVER:$calculaterSpreadResult<br>\n";
            $totalHomeScore += $ScoreHome ;
            $totalAwayScore += $ScoreAway ;
            if ($calculaterSpreadResult ==  1 || $calculaterSpreadResult == 1 ) {$win++;}
        $gameCount++;
        }
    $count++;
    }
    $LastXGamesWinPercent = sprintf("%0.2f", ( $win / $gameCount ) );
    $LastXGamesHomeMargin = ($totalHomeScore / $gameCount) - ( $totalAwayScore / $gameCount) ;
    if ( $LastXGamesHomeMargin >=  15 )   { $LastXGamesHomeMargin =  15;   }
    if ( $LastXGamesHomeMargin <= -14.9 ) { $LastXGamesHomeMargin = -14.9; }
    #print "<br>";
    #print "LastXMargin:$LastXGamesHomeMargin<br>LastXGamesWinPercent:$LastXGamesWinPercent<br>\n";
    $TeamInfo[$team]['LastXGamesWinPercent'] = $LastXGamesWinPercent;
    $TeamInfo[$team]['LastXGamesMargin'] = $LastXGamesHomeMargin;

}
function update_season_results_DB($season){
    $TeamInfo = set_coversTeamID();
    if (isset($season)) {
        print ("Getting data for ".$season."<br><br>");
        foreach( $TeamInfo as $team => $value1){
            $TeamInfo[$team]['coversID'];
            print "Loading $team $season ...";
            update_team_results_DB($team,$season);
            print "Loaded $team $season <br>";
        }
    } else {
        print ("?season=20xx season Is not passed in.");
    }
}
    function update_team_results_DB($team, $season) {
    global $TeamInfo;
    $InsertUpdateDelete = new InsertUpdateDelete_DAL();
    $season_next_year   = $season + 1;
    $url = "http://www.covers.com/pageLoader/pageLoader.aspx?page=/data/nba/teams/pastresults/".$season."-".$season_next_year."/team".$TeamInfo[$team]['coversID'].".html";
    $html = file_get_contents($url);
    $newlines = array("\x20\x20","\0","\x0B");
    $content = str_replace($newlines, "", html_entity_decode($html));

    $start = strpos($content,'<h3>Regular Season</h3>');
    $end = strpos($content,'</table>',$start) + 18;
    $html = substr($content,$start,$end-$start);
    $dom = new domDocument;
    $dom->loadHTML($html);
    $dom->preserveWhiteSpace = false;
    $tables = $dom->getElementsByTagName('table');
    $rows = $tables->item(0)->getElementsByTagName('tr');
    $count = 0; $totalHomeScore = 0; $totalAwayScore = 0;
    #echo "<a href=\"$url\">$team</a><br>\n";
    foreach ($rows as $row)
    { 
        $gameCount =0;
        if ($count < 1  ) { $count++; continue; }
        $gameData = $row->getElementsByTagName('td');
        $date   = ltrim($gameData->item(0)->nodeValue);
        list($month, $day, $year) = split('/', $date);
        $date   = "20".$year."-".$month."-".$day;

        $verses = $gameData->item(1)->nodeValue;
        $Spread = $gameData->item(4)->nodeValue; list($result, $Spread) = split(' ', $Spread);
            if (strpos($verses, '@') !== false) {$homeGame =0;} else {$homeGame = 1;}
            if ($homeGame == 0) { $verses = substr($verses, 4); } else { $verses = ltrim($verses); }
                $verses = rtrim($verses);
        $ScoreData  = $gameData->item(2)->getElementsByTagName('a');
        $Score      = $ScoreData->item(0)->nodeValue;
        list($ScoreHome, $ScoreAway) = split('-', $Score);
        $ScoreHome = ltrim($ScoreHome);
        $DidTheyCover = $ScoreHome - $ScoreAway + $Spread;
        
        
        if     ( $DidTheyCover == 0) { $calculaterSpreadResult = 1 ; }  # if they push make it a win
        elseif ( $DidTheyCover < 0 ) { $calculaterSpreadResult = -1; }
        elseif ( $DidTheyCover > 0 ) { $calculaterSpreadResult = 1 ; }


        $gameCount++;

        $totalHomeScore += $ScoreHome ;
        $totalAwayScore += $ScoreAway ;

        $query = "insert IGNORE into game_results values (NULL,'$date',$homeGame,'$team','$verses',$ScoreHome,$ScoreAway,$Spread,$DidTheyCover)";
        $id = $InsertUpdateDelete->insert_query($query);
        #print "$query - $id<br>\n";
    $count++;
    }
    #$LastXGamesWinPercent = sprintf("%0.2f", ( $win / $gameCount ) );
    #$LastXGamesHomeMargin = ($totalHomeScore / $gameCount) - ( $totalAwayScore / $gameCount) ;
    #if ( $LastXGamesHomeMargin >=  15 )   { $LastXGamesHomeMargin =  15;   }
    #if ( $LastXGamesHomeMargin <= -14.9 ) { $LastXGamesHomeMargin = -14.9; }
    #print "<br>";
    #print "LastXMargin:$LastXGamesHomeMargin<br>LastXGamesWinPercent:$LastXGamesWinPercent<br>\n";
    #$TeamInfo[$team]['LastXGamesWinPercent'] = $LastXGamesWinPercent;
    #$TeamInfo[$team]['LastXGamesMargin'] = $LastXGamesHomeMargin;
    return $url;
}
function set_coversTeamID() {
    global $TeamInfo;
    $TeamInfo['Boston']['coversID'] =404169;
    $TeamInfo['Brooklyn']['coversID'] =404117;
    $TeamInfo['New York']['coversID'] =404288;
    $TeamInfo['Philadelphia']['coversID'] =404083;
    $TeamInfo['Toronto']['coversID'] =404330;
    $TeamInfo['Chicago']['coversID'] =404198;
    $TeamInfo['Cleveland']['coversID'] =404213;
    $TeamInfo['Detroit']['coversID'] =404153;
    $TeamInfo['Indiana']['coversID'] =404155;
    $TeamInfo['Milwaukee']['coversID'] =404011;
    $TeamInfo['Atlanta']['coversID'] =404085;
    $TeamInfo['Charlotte']['coversID'] =664421;
    $TeamInfo['Miami']['coversID'] =404171;
    $TeamInfo['Orlando']['coversID'] =404013;
    $TeamInfo['Washington']['coversID'] =404067;
    $TeamInfo['Denver']['coversID'] =404065;
    $TeamInfo['Minnesota']['coversID'] =403995;
    $TeamInfo['Oklahoma City']['coversID'] =404316;
    $TeamInfo['Portland']['coversID'] =403993;
    $TeamInfo['Utah']['coversID'] =404031;
    $TeamInfo['Golden State']['coversID'] =404119;
    $TeamInfo['L.A. Clippers']['coversID'] =404135;
    $TeamInfo['L.A. Lakers']['coversID'] =403977;
    $TeamInfo['Phoenix']['coversID'] =404029;
    $TeamInfo['Sacramento']['coversID'] =403975;
    $TeamInfo['Dallas']['coversID'] =404047;
    $TeamInfo['Houston']['coversID'] =404137;
    $TeamInfo['Memphis']['coversID'] =404049;
    $TeamInfo['New Orleans']['coversID'] =404101;
    $TeamInfo['San Antonio']['coversID'] =404302;
    $TeamInfo['Denver']['coversID'] =404065;
    return $TeamInfo;
}
?>