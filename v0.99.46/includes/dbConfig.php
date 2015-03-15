<?php
include_once 'shop_db_functions.php';

if(!isset($_SESSION)){ session_start(); }

if (substr_count($_SERVER['SERVER_NAME'],".") == 1){ 
        $server_name = "www." . $_SERVER['SERVER_NAME'] ; }
else { 
        $server_name =          $_SERVER['SERVER_NAME'] ; }

if    (preg_match("/is-a-chef.com$/i", $server_name)) {
        $_SESSION['MYSQL_USER'] = 'dubbsenterprises';
        $_SESSION['MYSQL_PASS'] = 'W3bW3bW3b';
        $_SESSION['MYSQL_DATABASE'] = 'dubbsenterprises';
        $_SESSION['MYSQL_HOST'] = 'localhost';
}
elseif(preg_match("/system101.com$/i", $server_name)) {
        $_SESSION['MYSQL_USER'] = 'dubbsenterprises';
        $_SESSION['MYSQL_PASS'] = 'W3bW3bW3b';
        $_SESSION['MYSQL_DATABASE'] = 'dubbsenterprises';
        $_SESSION['MYSQL_HOST'] = 'localhost';
} 
else {
        $_SESSION['MYSQL_USER'] = 'dubbsenterprises';
        $_SESSION['MYSQL_PASS'] = 'W3bW3bW3b';
        $_SESSION['MYSQL_DATABASE'] = 'dubbsenterprises';
        $_SESSION['MYSQL_HOST'] = 'localhost';
}
?>