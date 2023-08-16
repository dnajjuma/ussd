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

        // Check if the amount is equal to 1000
        if ($amount == 1000) {

              // User has provided the correct amount, prompt for mobile money PIN
        $pin = explode('*', $text)[3];

        // Define the rest of your payment logic here

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
                "amount" => $amount,
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
            // echo "Authorization Request ID: $authorizationRequestId\n";
            // echo "Transaction Request ID: $transactionRequestId\n";
            // echo "Challenge: $challenge\n";
            // echo "Transfer Amount: $transferAmount $transferCurrency\n";
            // echo "Current State: $currentState\n";
        }    
                // Here you can process the payment logic based on the provided amount

                // $response = "CON Payment of UGX $transferAmount initiated with a status $currentState. Please enter Mobile Money PIN .";
                // $response = "CON Payment of UGX $transferAmount initiated with a status $currentState. Please enter Mobile Money PIN.";
            
            // Proceed to ask for the mobile money PIN
            $response = "CON $transferAmount accepted with a status $currentState. Please enter your Mobile Money PIN to confirm the payment.";
        } else {
            // Prompt the user to re-enter the amount till it's 1000
            $response = "CON Please enter the amount in UGX (1000):";
        }
    } else if (substr_count($text, '*') === 3) {
      }



        // For demonstration, let's assume the payment was successful
        // $response = "CON Payment of UGX 1000 initiated. Please enter your Mobile Money PIN to confirm the payment.";
}


// Echo the response back to the API
header('Content-type: text/plain');
echo $response;
?>
