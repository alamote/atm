<?php

class Card
{
    private $sql;
    private $card_number;
    private $table_name;
    private $incorrect_count;

    function __construct($card_number, $sid) {
        $this->connect("localhost", "root", "", "atm");


        $this->table_name = "cards";
        $this->incorrect_count = 0;

        if ($sid != "") {
            $query = "SELECT * FROM cards WHERE SID = '$sid'";

            $tmp = $this->sql->query($query) or die($this->sql->error);
            $tmp = $tmp->fetch_assoc();

            if (!$tmp['Card_Number'])
                die(1);

            $this->card_number = $tmp['Card_Number'];
        }
        else
            $this->card_number = $card_number;



    }

    /*
     * connect database
     * */
    function connect($h, $u, $p, $db) {

        if (__DIR__ == "Z:\\home\\alamote.ru\\atm\\php\\classes") {
            $host = $h;
            $user = $u;
            $password = $p;
            $db_name = $db;
        }
        else {
            $host = "us-cdbr-iron-east-03.cleardb.net";
			$user = "bfcb66f2c460d7";
			$password = "df4f0bec";
			$db_name = "heroku_3b707b6bd40ec3f";
        }

        $this->sql = new mysqli($host, $user, $password, $db_name);
    }

    /*
     * generate session ID
     * */
    function generateSID($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /*
     * keep in mind SID in database
     * */
    function keepInMindSID () {

        $SID = $this->generateSID();

        $query = "UPDATE ". $this->table_name ." SET `SID` = '". $SID ."' WHERE `Card_Number` = ". $this->card_number;
        $this->sql->query($query) or die($this->sql->error);

        return $SID;
    }

    /*
     * check session ID
     * */
    function checkSID ($sid) {

        $card_info = $this->getInfoAboutCard();

        $last_sid = $card_info['SID'];

        if ($sid != $last_sid)
            return 1;

        return 0;
    }

    /*
     * get information about current card
     * */
    function getInfoAboutCard () {
        $table_name = "cards";

        $query = "/** @lang */
        SELECT *
        FROM ". $table_name ."
        WHERE `Card_Number` = ". $this->card_number;

        //echo $query;

        $tmp = $this->sql->query($query) or die($this->sql->error);
        $tmp = $tmp->fetch_assoc();

        return $tmp;
    }

    /*
     * get balance on the card
     * */
    function getBalance () {
        $tmp = $this->getInfoAboutCard();

        return $tmp['Balance'];
    }

    /*
     * check the balance on the possibility of withdrawing the $amount
     * */
    function checkBalance ($amount) {
        $card_info = $this->getInfoAboutCard();

        /*
         * return codes
         *
         * 100 - ok
         * 101 - using credit
         * 102 - insufficient of money
         * 103 - insufficient of money and credit
         *
         * 110 - error
         *
         */

        if ($card_info['Is_Credit'] == 0) {
            if ($card_info['Balance'] < $amount) {
                return 102;
            }
            else if ($card_info['Balance'] >= $amount) {
                return 100;
            }
        }
        else if ($card_info['Is_Credit'] == 1) {
            $current_balance = $this->getBalance();
            $available = $card_info['Available'];

            if ($amount <= $current_balance)
                return 100;
            if ($amount <= $available)
                return 101;
            else
                return 103;

        }

        return 110;
    }

    /*
     * refill balance on the card
     * */
    function refillBalance ($amount) {
        $card_info = $this->getInfoAboutCard();

        $current_balance = $card_info['Balance'];

        $query = "UPDATE " . $this->table_name . " SET Balance = " .
            ($current_balance + $amount) . " WHERE `Card_Number` = ". $this->card_number;
        $this->sql->query($query) or die(1);

        $this->updateAvailable();

        return 0;
    }

    /*
     * update available money (credit + balance)
     * */
    function updateAvailable () {
        $card_info = $this->getInfoAboutCard();

        $cur_av = $card_info['Credit_Limit'] + $card_info['Balance'];
        $cur_num_card = $card_info['Card_Number'];

        $this->sql->query(" /** #lang */
            UPDATE cards
            SET Available = '$cur_av'

            WHERE Card_Number = '$cur_num_card'") or die($this->sql->error);
    }

    /*
     * block card
     * */
    function blockCard () {
        $query = "UPDATE " . $this->table_name . " SET `Blocked` = 1"." WHERE `Card_Number` = ". $this->card_number;
        $this->sql->query($query) or die($this->sql->error);
    }

    /*
     * unblock card
     * */
    function unblockCard () {
        $query = "UPDATE " . $this->table_name . " SET `Blocked` = 0"." WHERE `Card_Number` = ". $this->card_number;
        $this->sql->query($query) or die($this->sql->error);
    }

    /*
     * check PIN (returns true or false)
     * 3 unsuccessful attempts blocks the card
     * */
    function checkPIN ($pin) {
        $tmp = $this->getInfoAboutCard();

        if ($tmp['PIN_Code'] == $pin) {

            $query = "UPDATE " . $this->table_name . " SET `Incorrect_PIN` = 0 WHERE `Card_Number` = " . $this->card_number;
                $this->sql->query($query) or die($this->sql->error);

            return 0;
        }
        else {
            $tmp = $this->getInfoAboutCard();
            $incorrect = $tmp["Incorrect_PIN"];


            if ($incorrect == 2) {
                $this->blockCard();
            }
            else {
                $query = "UPDATE " . $this->table_name . " SET `Incorrect_PIN` = " . ($incorrect + 1) .
                    " WHERE `Card_Number` = " . $this->card_number;
                $this->sql->query($query) or die($this->sql->error);
            }
            return 1;
        }

    }

    /*
     * change PIN to $new_pin
     * */
    function changePIN ($new_pin) {
        $table_name = "cards";

        $query = "/** @lang */ UPDATE ". $table_name ." SET `PIN_Code` = ". $new_pin
            ." WHERE `Card_Number` = ". $this->card_number;
        $this->sql->query($query) or die(1);

        return 0;
    }

    /*
     * withdraw $amount money from the card (if card is credit, maybe at the expense of credit funds)
     * */
    function withdrawCash ($amount) {


        if ($this->checkBalance($amount) == 100 || $this->checkBalance($amount) == 101) {

            $card_info = $this->getInfoAboutCard();

            $current_balance = $card_info['Balance'];

            $query = "UPDATE " . $this->table_name . " SET Balance = " .
                ($current_balance - $amount) . " WHERE `Card_Number` = " . $this->card_number;
            $this->sql->query($query) or die($this->sql->error);

            $this->updateAvailable();

            return 0;
        }

/*        $current_balance = $this->getBalance();
        $card_info = $this->getInfoAboutCard();

        if ($current_balance < $amount && $card_info['Is_Credit'] == 0) {
            return 102;
        }
        else if ($current_balance <= $amount && $card_info['Is_Credit'] == 1) {
            $card_info = $this->getInfoAboutCard();

            $current_credit_used = $card_info['Credit_Used'];
            $credit_limit = $card_info['Credit_Limit'];
            $credit_amount = $amount - $current_balance;

            if ($credit_amount > ($credit_limit - $current_credit_used))
                return 103;

            $query = "UPDATE ". $this->table_name ." SET `Balance` = 0"
                ." WHERE `Card_Number` = ". $this->card_number;
            $this->sql->query($query);
            $query = "UPDATE ". $this->table_name ." SET `Credit_Used` = ". ($current_credit_used + $credit_amount)
                ." WHERE `Card_Number` = ". $this->card_number;
            $this->sql->query($query);

            return 101;

        }
        else if ($current_balance > $amount) {
            $query = "UPDATE ". $this->table_name ." SET `Balance` = ". ($current_balance - $amount)
                ." WHERE `Card_Number` = ". $this->card_number;
            $this->sql->query($query);

            return 100;
        }*/

        return 1;
    }

    /*
     * transfer $amount money from current card to $destination_number card
     * */
    function transferMoney ($amount, $destination_card) {

        if ($this->checkBalance($amount) == 100 || $this->checkBalance($amount) == 101) {

            $this->withdrawCash($amount);
            $destination_card->refillBalance($amount);

            $this->updateAvailable();
            $destination_card->updateAvailable();

            return 0;
        }
        else {
            return 1;
        }
    }

    /*
     * end the session
     * */
    function endSession () {
        $query = "UPDATE ". $this->table_name ." SET `SID` = 0 WHERE `Card_Number` = ". $this->card_number;
        $this->sql->query($query) or die(1);

        return 0;
    }
}