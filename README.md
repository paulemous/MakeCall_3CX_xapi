**3CX API Integration - PHP Script**
This PHP script is designed to interact with the 3CX API to perform operations such as obtaining an access token and making a call from one extension to another using the 3CX PBX system.

**Prerequisites**
- Webserver with PHP 7.4 or higher: The script uses cURL to interact with the 3CX API.
- 3CX PBX: A 3CX instance with an active API access configuration (client ID and secret).

**1. Define Constants**
The script begins by defining constants for the 3CX API credentials and URL:
- XAPI_USER is the client ID for your 3CX system.
- XAPI_KEY is the secret associated with the client ID for authentication.
- XAPI_URL is the URL of your 3CX PBX instance.

**2. Get Access Token**
The function getAccessToken() is responsible for obtaining an access token required for subsequent API requests. This is done by sending a request to the 3CX API's token endpoint with your client ID and client secret.

**3. Validate the Access Token**
The function validateToken() checks whether the obtained access token is valid by calling a simple test endpoint. It verifies that the 3CX API responds positively to the token before proceeding.

**4. Initiate a Call**
The makeCall() function initiates a call from one extension to another. It uses the 3CX API's endpoint for making calls, passing the necessary details like the caller's extension and the recipient's extension or phone number.

**5. Main Execution Flow**
The script follows these steps:
- Obtain the access token.
- Validate the token.
- Initiate a call from a predefined extension to a predefined destination.

**6. Error Handling**
The script uses try-catch blocks to handle any exceptions that occur during the process. If any cURL errors or API-related issues occur, they will be captured and displayed.

_Running the Script_
- To run the script:
Clone / Save the script as a .php file (e.g., 3cx_integration.php).

- Ensure your PHP environment is set up and cURL is enabled.
- Call the script /3cx_integration.php

If successful, the script will display the access token, validate it, and initiate a call from extension "$dn" to the destination number "$destination".

**Conclusion**
This script allows you to make calls using url with the 3CX PBX system via its API.
