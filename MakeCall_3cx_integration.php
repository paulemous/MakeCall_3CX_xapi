<?php

define('XAPI_USER', 'ClientID'); // ClientID
define('XAPI_KEY', 'API_Secret'); // API Secret
define('XAPI_URL', 'https://FQDN:port'); // PBX URL + port (if any)

// Function to get the access token
function getAccessToken() {
    $url = XAPI_URL . '/connect/token';
    $headers = [
        'Content-Type: application/x-www-form-urlencoded'
    ];

    $data = [
        'client_id' => XAPI_USER,
        'client_secret' => XAPI_KEY,
        'grant_type' => 'client_credentials'
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        throw new Exception('cURL error: ' . curl_error($ch));
    }

    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpcode != 200) {
        throw new Exception('Error fetching token: HTTP ' . $httpcode . ' - ' . $response);
    }

    // Decode the JSON response
    $responseData = json_decode($response, true);

    // Check if token is present in the response
    if (!isset($responseData['access_token'])) {
        throw new Exception('No access token found in the response.');
    }

    return $responseData['access_token'];
}

// Function to validate the token by calling a quick test endpoint
function validateToken($accessToken) {
    $url = XAPI_URL . '/xapi/v1/Defs?$select=Id'; // Quick test endpoint
    $headers = [
        'Authorization: Bearer ' . $accessToken
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        throw new Exception('cURL error: ' . curl_error($ch));
    }

    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Check for successful response
    if ($httpcode === 200) {
        echo "Token is valid. 3CX system version is: " . json_decode($response, true)['value'][0]['Id'] . "\n";
    } else {
        throw new Exception('Error validating token: HTTP ' . $httpcode . ' - ' . $response);
    }
}

// Function to initiate a call based on the new specification
function makeCall($accessToken, $dn, $destination) {
    $url = XAPI_URL . '/xapi/v1/Users/Pbx.MakeCall';
    $headers = [
        'Authorization: Bearer ' . $accessToken,
        'Content-Type: application/json'
    ];

    // Constructing the request body as per the API specification
    $data = [
        'dn' => $dn,           // The caller (From) extension/number
        'destination' => $destination  // The recipient (To) extension/number
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        throw new Exception('cURL error: ' . curl_error($ch));
    }

    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Check for successful response
    if ($httpcode === 200) {
        echo "Call initiated successfully.\n";
        echo "Response: " . $response . "\n";  // Optional: log the response for debugging
    } else {
        throw new Exception('Error making call: HTTP ' . $httpcode . ' - ' . $response);
    }
}

try {
    // Step 1: Get access token
    $accessToken = getAccessToken();
    echo "Access Token: " . $accessToken . "\n";

    // Step 2: Validate the token
    validateToken($accessToken);

    // Step 3: Initiate a call (replace '100' with the caller's extension and '101' with the recipient's number)
    $dn = '100';  // Example caller extension
    $destination = '101';    // Example destination extension/number
    makeCall($accessToken, $dn, $destination);

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
