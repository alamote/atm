<?php

    if (__DIR__ == "Z:\\home\\alamote.ru\\atm\\php") {
        $host = "localhost";
        $user = "root";
        $password = "";
        $db_name = "atm";
    }
    else {
        $host = "us-cdbr-iron-east-03.cleardb.net";
        $user = "bfcb66f2c460d7";
        $password = "df4f0bec";
        $db_name = "heroku_3b707b6bd40ec3f";
		
    }

    $sql = new mysqli($host, $user, $password, $db_name);

?>