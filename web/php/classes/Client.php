<?php


class Client
{
    private $sql;
    private $lang;
    private $table_name;
    private $client_ID;


    function __construct($card_number) {
        $this->connect("localhost", "root", "", "atm");
        $this->table_name = "users";

        $query = "SELECT `Client_ID` FROM `cards` WHERE `Card_Number` = ". $card_number;
        $tmp = $this->sql->query($query) or die($this->sql->error);
        $tmp = $tmp->fetch_assoc();

        $this->client_ID = $tmp['Client_ID'];


    }

    function connect($h, $u, $p, $db) {
        $host = $h;
        $user = $u;
        $password = $p;
        $db_name = $db;

        $this->sql = new mysqli($host, $user, $password, $db_name);
    }

    function getInfoAboutClient() {

        $query = "SELECT * FROM ". $this->table_name ." WHERE `Client_ID` = ". $this->client_ID;
        $tmp = $this->sql->query($query) or die($this->sql->error);
        $tmp = $tmp->fetch_assoc();

        printf("<script>console.log('". $tmp['Tel_Number'] ."')</script>");
    }
}