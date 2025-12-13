<?php
// Read the variables sent via POST from our API
// callback URl https://srv564-files.hstgr.io/4442363f430eb686/files/public_html/ussd/index.php
     include_once 'menu.php';


$sessionId   = $_POST["sessionId"];
$serviceCode = $_POST["serviceCode"];
$phoneNumber = $_POST["phoneNumber"];
$text        = $_POST["text"];

$isRegistered = true;
// create an object of the menu class
$menu = new Menu($text, $sessionId);

if ($text == "" && !$isRegistered) {
    //User is isRegistered and string is empty
    $menu->mainMenuRegistered();
    
} else if ($text == "" && $isRegistered){
    // User is UnRegistered and String is empty
     $menu->mainMenuUnRegistered();
     
} else if ($isRegistered){
    //  User is UnRegistered and String is not empty
    $textArray = explode ("*", $text);
    switch ($textArray[0]) {
        case 1:
            // call the register
            $menu->registeredMenu($textArray);
            break;
        
        default:
            // if the user enetr smonething that isnt there just throw an error
            echo "END Invalid Choice Or MMI connection error, Please Try Again";
    }
}else{
    // user is isRegistered and String is not empty
    $textArray = explode("*", $text);
    
    switch ($textArray[0]) {
        case 1:
            // send money for registered
            $menu->sendMoneyMenu($textArray);
            break;
            
        // withdrawMoneyMenu for Registered User
        case 2: 
            $menu->withdrawMoneyMenu($textArray);
        break;
        
        case 3:
            $menu->checkBalanceMenu($textArray);
            break;
        
        default:
            // if the user enetr smonething that isnt there just throw an error
            echo "END Invalid Choice Or MMI connection error, Please Try Again";
    }

}






?>