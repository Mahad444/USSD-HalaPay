<?php
// constant options
include_once 'util.php';

class Menu {
    
    protected $text;
    protected $sessionId;
    
    function __construct($text = null, $sessionId = null)
    {
        // $this->text = $text;
        // $this->sessionId = $sessionId;
    }

public function mainMenuRegistered(){
    // for both registered and un registered user
    $response =  "CON Reply With\n";
    $response .= "1. Send Money\n";
    $response .= "2. Withdraw Money\n";
    $response .= "3. Check Balance\n";
    
// $response = "END Check Balance\n";
// $response = "END You have successfully Registred\n";
    
    echo $response;
    
}
public function mainMenuUnRegistered(){
    // user mainMenuUnRegistered
    $response = "CON Welcome to HALAL PAY. Reply With\n";
    $response .= "1. Register\n";
    
    // $response = "END You have successfully Registred\n";
    echo $response;
}

// register name,pin,confirm pin Level 0
public function registeredMenu($textArray){
    $level = count ($textArray);
    if ($level == 1) {
        echo "CON Please Enter your Full Name:";
    } else if ($level == 2){
        echo "CON Please Set your PIN:";
    } elseif ($level == 3 ) {
        echo "CON Please re-enter your PIN";
    } else if ($level == 4 ) {
        $name = $textArray[1];
        $pin = $textArray [2];
        $confirmPin = $textArray[3];
        if ( $pin != $confirmPin ){
            echo "END Your PIN does not Match. Please try again";
        }else{
            // WE can Register the user and send an SMS 
            echo "END Congratulations You have successfully Registered To HALAL PAY. You will receive an SMS shortly";
        }
    }
    
}
// send money, Reciever mobile,amount, Pin, confirm , cancel 
public function sendMoneyMenu($textArray){
    $level = count ($textArray);
     if ($level == 1){
         echo "CON Enter reciepient PhoneNumber:";
     }elseif($level == 2){
         echo "CON Enter Amount:";         
     }else if( $level == 3){
         echo "CON Enter Your PIN:";
     } 
    //  else if ($level == 4)
    //  {
    //     $response = "CON Send ". $textArray[3] . " to ". $textArray[2] ."\n";
    //     $response .= "1.Confirm \n";
    //     $response .= "2.Cancel \n";
    //     $response .= Util::$GO_BACK . "Back \n";
    //  
    //     echo $response;
    // $response = "END You are about to send ".$textArray[2] ." to ".$textArray[1];
     else if ($level == 4){
         $recipient = $textArray[1];
         $amount = $textArray[2];
         echo "CON You are about to send KES $amount to $recipient\n";
         echo "1. Confirm\n";
         echo "2. Cancel\n";
         echo Util::$GO_BACK . ". Go Back\n";
         echo Util::$GO_TO_MAIN_MENU . ". Main Menu\n";
     }else if($level == 5 && $textArray[4] == 1){
        //  confirm to send money
        // send money plus process
        // check if PIN is correct, this is checked by network
        // if u have enough funds including charges etc...
        // after checking all the above send the money
                echo "END You have successfully sent KES ".$textArray[2] ." to ".$textArray[1];

     }else if($level == 5 && $textArray[4] == 2){
        //  Cancel end the sessionId
        echo "END Thank You for choosing HALAL PAY";
     }else if($level == 5 && $textArray[4] == Util::$GO_BACK){
         echo "END You have requested to go back to one step -PIN ";
     }else if($level == 5 && $textArray[4] == Util::$GO_TO_MAIN_MENU){
         echo "END You have requested to GO BACK to main menu ";
     }else{
         echo "END Invalid entry or MMI connection failed, Try again later ";
     }
}
// withdrawMoney , Agent No., Amount , pin, confirm,cancel, GO BACK
public function withdrawMoneyMenu($textArray){
    $level = count($textArray);
    
    if ($level == 1){
        echo "CON Enter Agent No.:";
    }elseif($level == 2){
        echo "CON Enter Amount:";
    }else if($level == 3){
        echo "CON Enter Your PIN:";
    }elseif ($level == 4){
        // Check the agent exist
        echo "CON Withdraw" . $textArray[2] . "from agent" . $textArray[1] . "\n 1.Confirm \n 2.Cancel\n";
    }elseif ($level == 5 && $textArray[4] == 1){
        // Confirm
        echo "END Your Request Is being processed";
    }else if ($level == 5 && $textArray[4] == 2){
        // Cancel
        echo "END Thank You for choosing HALAL PAY";
    }else{
        echo "END Invalid entry or MMI connection failed ";
    }

    
}

// Check Balance , PIN
public function checkBalanceMenu($textArray){
    $level = count ($textArray);
    if($level == 1){
        echo "CON Enter PIN";
    }elseif($level == 2){
        // Check PIN correctness etc
        echo "END We are processing your request, You will recieve SMS shortly";
    }
    else{
        echo "END Invalid entry or MMI connection failed ";
    }
        
    }

    // Functions of GOBACK and 98MORE
    
     public function middleware ($text){
        // remove entries for going back to the main menu
        return $this->goBack ($this->goToMainMenu($text));
    }
    
    public function goBack($text){
        // 1*4*5*1*98*2*1234*98
        $explodedText = explode("*" , $text);
        while(array_search(Util::$GO_BACK,$explodedText) != false){
            $fisrtIndex = array_search(Util::$GO_BACK,$explodedText);
            $explodedText = array_splice($explodedText, $fisrtIndex -1, 2);
        }
        return join ("*", $explodedText);
        
        
    }

    public function goToMainMenu ($text){
        // 1*4*5*1*99*2*1234*99
        $explodedText = explode("*" , $text);
        while(array_search(Util::$GO_TO_MAIN_MENU,$explodedText) != false){
            $fisrtIndex = array_search(Util::$GO_TO_MAIN_MENU,$explodedText);
            $explodedText = array_slice($explodedText,$fisrtIndex + 1);
        }
        
        return join("*", $explodedText);
    
   }
    
    
    
    
    
    
    
}






?>