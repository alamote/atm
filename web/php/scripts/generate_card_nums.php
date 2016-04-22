<?php

    include_once('../connectDB.php');

    for ($i = 0; $i < 3; $i++) {

        $number = rand(1000, 9999) . "";
        $tmp .= $number;
    }

    $query = "SELECT * FROM `cards`";

    $res = $sql->query($query);

    $i = 1;

    while ($row = $res->fetch_array()) {

        $temp = $tmp;
        for ($j = 0; $j < 4 - strlen($i); $j++)
            $temp .= "0";
        $temp .= $i;

        $sql->query("UPDATE cards SET Card_Number = $temp WHERE Row_ID = ". $row['Row_ID']) or die($sql->error);

        echo $temp . "<br>" . $row['Card_Number']. "<br>" . $i. "<br>" . "<br>" ;


        $temp = "";
        $i++;
   }
