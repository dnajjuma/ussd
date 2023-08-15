<?php
// Read the variables sent via POST from our API
$sessionId   = $_POST["sessionId"];
$serviceCode = $_POST["serviceCode"];
$phoneNumber = $_POST["phoneNumber"];
$text        = $_POST["text"];

if ($text == "") {
    // This is the first request. Note how we start the response with CON
    $response  = "Welcome to BOREPAY, What would you want to do? \n";
    $response .= "1. See my Card No";
    $response .= "2. Phone number";

} else if ($text == "1") {
    // Business logic for first level response
    $response = "BOREPAY Choose account information you want to view \n";
    $response .= "1. Card number \n";

} else if ($text == "2") {
    // Business logic for first level response
    // This is a terminal request. Note how we start the response with END
    $response = "END Your phone number is ".$phoneNumber;

} else if($text == "1*1") { 
    // This is a second level response where the user selected 1 in the first instance
    $cardNumber  = "CUI1001";

    // This is a terminal request. Note how we start the response with END
    $response = "END Your card number is ".$cardNumber;

}


// Echo the response back to the API
header('Content-type: text/plain');
echo $response;