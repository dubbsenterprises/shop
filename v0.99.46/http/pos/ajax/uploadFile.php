<?php
require_once('../../../includes/general_functions.php');
$userfile           = $_POST['file_hash_name'];
$column_name        = $_POST['column_name'];
$source_record_id   = $_POST['source_record_id'];
#echo $userfile . "," . $column_name . "," .$source_record_id . "\n";
upload_file($userfile,$column_name,$source_record_id);
?>