<?php


    include_once('../connectDB.php');
    include_once('../ChromePhp.php');

    $query = "SELECT * FROM `cards`";

    $res = $sql->query($query);
    while ($row = $res->fetch_array()) {

        //echo $row['Validity_Month'] . "<br> ";

        if (strlen($row['Validity_Month']) == 1) {
            $tmp = "0" . $row['Validity_Month'];
            //echo $tmp . "<br> ". "<br> ";
            $query = "UPDATE cards SET Validity_Month = '$tmp' WHERE Card_Number = '" . $row['Card_Number'] . "'";
            $sql->query($query) or die($sql->error);

            ChromePhp::log($row['Validity_Month']);
            ChromePhp::log($tmp);
            ChromePhp::log($row['Card_Number']);
            ChromePhp::log($query);

        }

    }
