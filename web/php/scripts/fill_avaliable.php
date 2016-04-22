<?php

    include_once('../connectDB.php');
    include_once('../ChromePhp.php');

    $query = "SELECT * FROM `cards`";

    $res = $sql->query($query);

    while ($row = $res->fetch_array()) {

        $cur_av = $row['Credit_Limit'] + $row['Balance'];
        $cur_num_card = $row['Card_Number'];

        /*ChromePhp::log($cur_av);
        ChromePhp::log($cur_num_card);*/

        echo $row['Client_ID'] . "<br>" . $cur_av . "<br>" . $row['Credit_Limit'] . " + " . $row['Balance']. "<br>"  ;
        echo $cur_num_card . "<br><br>";

        $sql->query(" /** #lang */
            UPDATE cards
            SET Available = '$cur_av'
            WHERE Card_Number = '$cur_num_card'") or die($sql->error);

    }