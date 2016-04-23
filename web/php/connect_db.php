<?php

    if (__DIR__ == "Z:\\home\\alamote.ru\\atm\\php") {
        $host = "localhost";
        $user = "root";
        $password = "";
        $db_name = "atm";
    }
    else {
        $host = "sql304.byethost10.com";
        $user = "b10_16828832";
        $password = "AlaMote_12";
        $db_name = "b10_16828832_atm";
    }

    $sql = new mysqli($host, $user, $password, $db_name);

?>