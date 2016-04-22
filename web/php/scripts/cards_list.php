<link rel="stylesheet" href="../../css/style.css" type="text/css">

<?php

    include_once('../connectDB.php');

    $num_query = "SHOW COLUMNS FROM cards";
    $res_num = $sql->query($num_query) or die($this->sql->error);

    echo "<table>";
    echo "<tr>";
    $text = "<td class='main_td'>";

    while ($row = $res_num->fetch_array()) {
        echo $text.$row[0]."</td>";
    }

    $query = "SELECT * FROM `cards`";
    $res = $sql->query($query) or die($sql->error);

    while ($row = $res->fetch_array()) {
        echo "<tr>";
        for ($i = 0; $i < count($row) / 2; $i++) {
            echo "<td>" . $row[$i] . "</td>";
        }

        echo "</tr>";

    }

    echo "</table>";




