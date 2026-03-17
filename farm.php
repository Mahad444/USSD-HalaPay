<?php

// Farmers' Registration Platform
// Requirements For registration KYC
// 1. Valid ID (National ID, Passport)
// 2. Farm Details (Location, Size, Type of Crops)
// 3. Contact Information (Phone Number, Email Address)
// 4. PIN to login
// 5. Confirmation of registration

// Read the variables sent via POST from our API
$sessionId   = isset($_POST["sessionId"]) ? $_POST["sessionId"] : "";
$serviceCode = isset($_POST["serviceCode"]) ? $_POST["serviceCode"] : "";
$phoneNumber = isset($_POST["phoneNumber"]) ? $_POST["phoneNumber"] : "";
$text        = isset($_POST["text"]) ? $_POST["text"] : "";

// Explode the text to get the level of the USSD session
$textArray = explode("*", $text);

// Determine the level. If text is empty, level is 0.
$level = ($text == "") ? 0 : count($textArray);

if ($level == 0) {
    // Level 0: Welcome Menu
    $response  = "CON Welcome to the Farmers' Registration Platform.\n";
    $response .= "1. Register\n";
    echo $response;

} else if ($level == 1 && $textArray[0] == "1") {
    // Level 1: Ask for Valid ID
    echo "CON Please enter your Valid ID (National ID or Passport):";

} else if ($level == 2 && $textArray[0] == "1") {
    // Level 2: Ask for Farm Location
    echo "CON Please enter your Farm Location:(E.g., Nairobi, Kenya):";

} else if ($level == 3 && $textArray[0] == "1") {
    // Level 3: Ask for Farm Size
    echo "CON Please enter your Farm Size (e.g., 5 Acres):";

} else if ($level == 4 && $textArray[0] == "1") {
    // Level 4: Ask for Type of Crops
    echo "CON Please enter the Type of Crops you grow:(E.g., Maize, Beans):";

} else if ($level == 5 && $textArray[0] == "1") {
    // Level 5: Ask for Email Address
    echo "CON Please enter your Email Address:";

} else if ($level == 6 && $textArray[0] == "1") {
    // Level 6: Ask for PIN
    echo "CON Please set a PIN to login:";

} else if ($level == 7 && $textArray[0] == "1") {
    // Level 7: Confirm PIN
    echo "CON Please confirm your PIN:";

} else if ($level == 8 && $textArray[0] == "1") {
    // Level 8: Process Registration
    $id = $textArray[1];
    $location = $textArray[2];
    $size = $textArray[3];
    $crops = $textArray[4];
    $email = $textArray[5];
    $pin = $textArray[6];
    $confirmPin = $textArray[7];

    if ($pin != $confirmPin) {
        echo "END Your PIN does not match. Please dial the USSD code and try again.";
    } else {
        // Here you would typically save the details to a database
        // e.g., saveFarmerDetails($phoneNumber, $id, $location, $size, $crops, $email, $pin);
        
        $response  = "END Congratulations! You have successfully registered your farm.\n";
        $response .= "We have received your details and an SMS confirmation will be sent to $phoneNumber shortly.";
        echo $response;
    }
} else {
    // Invalid input or other options
    echo "END Invalid choice or input. Please try again.";
}

?>