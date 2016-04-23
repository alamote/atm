


<?php

include_once("php/classes/Card.php");
include_once("php/classes/Client.php");

/*echo "<center>Привет, это главная страница!" . "<br>" . "Используйте эти GET-запросы для работы с банкоматом:</center>" . "<br>" . "<br>" . "

<link rel='stylesheet' href='./css/style.css' type='text/css'>
<div id='commands'>
																										
?type=auth&num=[CARD_NUMBER]&pin=[PIN_CODE]  									" . "<br>" . "
?sid=[SID]&bal= 							" . "<br>" . "
?sid=[SID]&chgpin=[NEW_PIN] 							" . "<br>" . "
?sid=[SID]&withdraw=[SUM]								" . "<br>" . "
?sid=[SID]&refill=[SUM] 							" . "<br>" . "
?sid=[SID]&transfer=[SUM]&dest=[DESTINATION_CARD_NUMBER] 	" . "<br>" . "
?sid=[SID]&end=						" . "</div>

<div id='comments'>
	Логин" . "<br>" . "
	Проверить баланс" . "<br>" . "
	Сменить PIN-код" . "<br>" . "
	Снять наличные" . "<br>" . "
	Пополнить счет	" . "<br>" . "
	Перевести средства на другую карту	" . "<br>" . "
	Закончить сессию	" . "<br>" . "
</div>

";

*/


/*
 * authentication
 * */

if ($_GET['type'] == 'auth' && isset($_GET['num']) && isset($_GET['pin'])) {

    $card = new Card($_GET['num'], "");

	$card_info = $card->getInfoAboutCard();

    if ($card_info['Blocked'] == 1) {
         
        $card->endSession();
        die("card blocked");
    }

    $check = $card->checkPIN($_GET['pin']);

    if ($check == 0) {
        echo $card->keepInMindSID();
    }
    else
        echo $check;

}
/*
 * check balance
 * */
else if (isset($_GET['sid']) && isset($_GET['bal'])) {
    $card = new Card($_GET['num'], $_GET['sid']);


    if ($card->checkSID($_GET['sid']) == 1) {
         
        die('1');
    }

    $card_info = $card->getInfoAboutCard();

    echo $card->getBalance() . " " . $card_info["Available"];
}
/*
 * cnahge PIN
 * */
else if (isset($_GET['sid']) && isset($_GET['chgpin'])) {
    $card = new Card($_GET['num'], $_GET['sid']);
    if ($card->checkSID($_GET['sid']) == 1) {
         
        die('1');
    }

    echo $card->changePIN($_GET['chgpin']);
}
/*
 * withdraw cash
 * */
else if (isset($_GET['sid']) && isset($_GET['withdraw'])) {
    $card = new Card($_GET['num'], $_GET['sid']);
    if ($card->checkSID($_GET['sid']) == 1) {
         
        die('1');
    }

    echo $card->withdrawCash($_GET['withdraw']);
}
/*
 * refill balance
 * */
else if (isset($_GET['sid']) && isset($_GET['refill'])) {
    $card = new Card($_GET['num'], $_GET['sid']);
    if ($card->checkSID($_GET['sid']) == 1) {
         
        die('1');
    }

    echo $card->refillBalance($_GET['refill']);
}
/*
 * transfer monet to dest-card_number
 * */
else if (isset($_GET['sid']) && isset($_GET['transfer']) && isset($_GET['dest'])) {
    $card = new Card($_GET['num'], $_GET['sid']);
    if ($card->checkSID($_GET['sid']) == 1) {
         
        die('1');
    }

    $dest = new Card($_GET['dest'], "");

    echo $card->transferMoney($_GET['transfer'], $dest);
}
/*
 * end the session
 * */

else if (isset($_GET['db'])) {

    $href = "/php/scripts/cards_list.php";

    echo "<script>window.location = '$href'</script>";

}
/*
 * end the session
 * */
else if (isset($_GET['sid']) && isset($_GET['end'])) {
    $card = new Card($_GET['num'], $_GET['sid']);
    if ($card->checkSID($_GET['sid']) == 1) {
         
        die('1');
    }

     
    echo $card->endSession();

}


