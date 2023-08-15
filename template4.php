<?php
// Read the variables sent via POST from our API
$sessionId   = $_POST["sessionId"];
$serviceCode = $_POST["serviceCode"];
$phoneNumber = $_POST["phoneNumber"];
$text        = $_POST["text"];

// Define a session variable to track the USSD flow
$sessionData = isset($_SESSION["session_data"]) ? $_SESSION["session_data"] : array();

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
    if (!isset($sessionData[$phoneNumber])) {
        // This is the first step after selecting option 1*1, prompt for amount
        $response = "CON Enter the amount in UGX";
        // Store the current step in the session data
        $sessionData[$phoneNumber] = "enter_amount";
    } else if ($sessionData[$phoneNumber] === "enter_amount") {
        // User has provided the amount, prompt for mobile money PIN
        $response = "CON Enter your mobile money PIN to confirm the payment.";
        // Update the session data step
        $sessionData[$phoneNumber] = "enter_pin";
    } else if ($sessionData[$phoneNumber] === "enter_pin") {
        // User has provided the PIN, process the payment
        $amount = explode('*', $text)[2];
        $pin = $text;

        // In a real-world scenario, you would securely process the PIN and initiate the payment
        // Here, we're just providing an example response for demonstration purposes
        $response = "END Payment processing..."; // You can replace this with actual payment processing logic
    }
}

// Save the updated session data
$_SESSION["session_data"] = $sessionData;

// Echo the response back to the API
header('Content-type: text/plain');
echo $response;
?>
