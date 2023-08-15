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
    } else if (substr_count($text, '*') === 2) {
        // User has provided the amount, extract and process
        $amount = explode('*', $text)[2];
$requestPayload = array(
    "payee" => array(
        "partyIdInfo" => array(
            "partyIdType" => "MSISDN",
            "partyIdentifier" => "9876543210",
            "fspId" => "dfspb"
        )
    ),
    "payer" => array(
        "partyIdType" => "THIRD_PARTY_LINK",
        "partyIdentifier" => "1234567890",
        "fspId" => "dfspa"
    ),
    "amountType" => "SEND",
    "amount" => array(
        "amount" => "100",
        "currency" => "UGX"
    ),
    "transactionType" => array(
        "scenario" => "TRANSFER",
        "initiator" => "PAYER",
        "initiatorType" => "CONSUMER"
    ),
    "expiration" => "2044-07-15T22:17:28.985-01:00"
);

// Set the URL for the request
$url = 'http://13.211.229.144:4040/thirdpartyTransaction/{ID}/initiate';

// Initialize cURL session
$ch = curl_init();

// Set cURL options
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestPayload));
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json'
));

// Execute cURL session and capture response
$response = curl_exec($ch);

// Check for cURL errors
if (curl_errno($ch)) {
    echo 'cURL error: ' . curl_error($ch);
} else {
    // Close cURL session
    curl_close($ch);

    // Decode the JSON response
    $responseArray = json_decode($response, true);

    // Extract and display relevant information
    $authorizationRequestId = $responseArray['authorization']['authorizationRequestId'];
    $transactionRequestId = $responseArray['authorization']['transactionRequestId'];
    $challenge = $responseArray['authorization']['challenge'];
    $transferAmount = $responseArray['authorization']['transferAmount']['amount'];
    $transferCurrency = $responseArray['authorization']['transferAmount']['currency'];
    $currentState = $responseArray['currentState'];

    // Display the extracted information
    echo "Authorization Request ID: $authorizationRequestId\n";
    echo "Transaction Request ID: $transactionRequestId\n";
    echo "Challenge: $challenge\n";
    echo "Transfer Amount: $transferAmount $transferCurrency\n";
    echo "Current State: $currentState\n";

    $response = "CON Payment of UGX $transferAmount initiated with a status $currentState.";
}

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
