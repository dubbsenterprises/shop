<?php
# functions-db.php file
# Query Class
Class Query {
    var $action;
    var $connect;

    function Query($query,$sql) {
        $this->action = mysql_query($query,$sql);
        $this->connect = $sql;
    }
    /* More functions will go here */
}
?>

