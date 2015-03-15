<?php
include_once('general_functions.php');
//1. Edit ajax/MainDiv.php,  add a condition for the new page.

class ITEM_SEARCH_DAL {
  public function __construct(){}
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

function item_search() {
?>
<div class="ReportsTopRow main_bc_color2 main_color2_text"><a href="#" title="Item Search" onclick="mainDiv('item_search'); return false;">Item Search</a></div>
    <div style="max-height: 1000px;" class="f_left wp100 hp94">
        <div class="leftSpace main_bc_color2 main_color2_text">&nbsp;</div>
        <div class="middleSpace wp96">
            <div class="f_left wp100 hp10">
                <div class="f_left hp100 wp35 left vtop no-overflow">
                    XXX&nbsp
                </div>
                <div class="f_right hp100 wp50 right">&nbsp;
                    XXX&nbsp
                </div>
            </div>
        </div>
        <div class="rightSpace main_bc_color2 main_color2_text">&nbsp;</div>
    </div>
<div class="ReportsBottomRow main_bc_color2 main_color2_text">&nbsp;</div>
<?
}