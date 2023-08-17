<?php
// Read the variables sent via POST from our API
$sessionId   = $_POST["sessionId"];
$serviceCode = $_POST["serviceCode"];
$phoneNumber = $_POST["phoneNumber"];
$text        = $_POST["text"];

if ($text == "") {
    // This is the first request. Note how we start the response with CON
    $response  = "CON Welcome to BorePay. What would you want to do? \n";
    $response .= "1. Payments \n";
    $response .= "2. My phone number";

} else if ($text == "1") {
    // Business logic for first level response
    $response = "CON Enter card no \n";

} else if ($text == "2") {
    // Business logic for first level response
    // This is a terminal request. Note how we start the response with END
    $response = "END Your phone number is ".$phoneNumber;

} else if (strpos($text, "1*1") === 0) { 
    // This is a second level response where the user selected 1 in the first instance
    if (substr_count($text, '*') === 1) {
        // This is the first step after selecting option 1*1, prompt for amount
        $response = "CON Enter the amount in UGX";
    } else if (substr_count($text, '*') === 2) {
        // User has provided the amount, extract and process
        $amount = explode('*', $text)[2];

        // Define the request payload as an array
        $requestPayload = array(
            // ... (same as before)
        );

        // Set the URL for the request
        $url = 'http://13.211.229.144:4040/thirdpartyTransaction/{ID}/initiate';

        // ... (same as before)

        // Check if the amount is equal to 1000
        if ($amount >= 1000) {
            // Proceed to ask for the mobile money PIN
            $response = "CON Payment of UGX $transferAmount initiated with a status $currentState. Please enter your Mobile Money PIN to confirm the payment.";

        } else {
            // Prompt the user to re-enter the amount till it's 1000
            $response = "CON Please enter the amount in UGX (1000):";
        }
    } else if (substr_count($text, '*') === 3) {
        // User has provided the correct amount, prompt for mobile money PIN
        $pin = explode('*', $text)[3];

        // Define the rest of your payment logic here

        // For demonstration, let's assume the payment was successful
        if ($pin === "1998") {
            // Payment successful confirmation text
            $response = "CON Payment was successful. Thank you!";
        } else {
            // Handle incorrect PIN scenario
            $response = "CON Incorrect PIN. Please enter your Mobile Money PIN again:";
        }
    }
}

// Echo the response back to the API
header('Content-type: text/plain');
echo $response;
?>
