<?php
// Define the request payload as an array
$requestPayload = array(
    "authorizationResponse" => array(
        "responseType" => "ACCEPTED",
        "signedPayload" => array(
            "signedPayloadType" => "FIDO",
            "fidoSignedPayload" => array(
                "id" => "45c-TkfkjQovQeAWmOy-RLBHEJ_e4jYzQYgD8VdbkePgM5d98BaAadadNYrknxgH0jQEON8zBydLgh1EqoC9DA",
                "rawId" => "45c+TkfkjQovQeAWmOy+RLBHEJ/e4jYzQYgD8VdbkePgM5d98BaAadadNYrknxgH0jQEON8zBydLgh1EqoC9DA==",
                "response" => array(
                    "authenticatorData" => "SZYN5YgOjGh0NBcPZHZgW4/krrmihjLHmVzzuoMdl2MBAAAACA==",
                    "clientDataJSON" => "eyJ0eXBlIjoid2ViYXV0aG4uZ2V0IiwiY2hhbGxlbmdlIjoiQUFBQUFBQUFBQUFBQUFBQUFBRUNBdyIsIm9yaWdpbiI6Imh0dHA6Ly9sb2NhbGhvc3Q6NDIxODEiLCJjcm9zc09yaWdpbiI6ZmFsc2UsIm90aGVyX2tleXNfY2FuX2JlX2FkZGVkX2hlcmUiOiJkbyBub3QgY29tcGFyZSBjbGllbnREYXRhSlNPTiBhZ2FpbnN0IGEgdGVtcGxhdGUuIFNlZSBodHRwczovL2dvby5nbC95YWJQZXgifQ==",
                    "signature" => "MEUCIDcJRBu5aOLJVc/sPyECmYi23w8xF35n3RNhyUNVwQ2nAiEA+Lnd8dBn06OKkEgAq00BVbmH87ybQHfXlf1Y4RJqwQ8="
                ),
                "type" => "public-key"
            )
        )
    )
);

// Set the URL for the request
$url = 'http://13.211.229.144:4040/thirdpartyTransaction/{ID}/approve';

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

    var_dump($responseArray);

    // Process the API response

    if (isset($responseArray['transactionStatus']) && isset($responseArray['currentState'])) {
        $transactionId = $responseArray['transactionStatus']['transactionId'];
        $transactionRequestState = $responseArray['transactionStatus']['transactionRequestState'];
        $transactionState = $responseArray['transactionStatus']['transactionState'];
        $currentState = $responseArray['currentState'];

        // Process the extracted information as needed
        echo "Transaction ID: $transactionId\n";
        echo "Transaction Request State: $transactionRequestState\n";
        echo "Transaction State: $transactionState\n";
        echo "Current State: $currentState\n";
    } else {
        echo "API request failed or response format is not as expected.";

    }
    // if (isset($responseArray['status']) && $responseArray['status'] === 200) {
    //     $transactionId = $responseArray['body']['transactionStatus']['transactionId'];
    //     $transactionRequestState = $responseArray['body']['transactionStatus']['transactionRequestState'];
    //     $currentState = $responseArray['body']['currentState'];
        
    //     // Process the extracted information as needed
    //     echo "Transaction ID: $transactionId\n";
    //     echo "Transaction Request State: $transactionRequestState\n";
    //     echo "Current State: $currentState\n";
    // } else {
    //     echo "API request failed or response format is not as expected.";
    // }
}
?>
