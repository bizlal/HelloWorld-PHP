<?php

require_once(__DIR__ . '/vendor/autoload.php');
use QuickBooksOnline\API\DataService\DataService;

session_start();

function makeAPICall()
{
    // Create SDK instance
    $config = include('config.php');
    $dataService = DataService::Configure(array(
        'auth_mode' => 'oauth2',
        'ClientID' => $config['client_id'],
        'ClientSecret' =>  $config['client_secret'],
        'RedirectURI' => $config['oauth_redirect_uri'],
        'scope' => $config['oauth_scope'],
        'baseUrl' => "development"
    ));

    // Retrieve the accessToken value from session variable
    $accessToken = $_SESSION['sessionAccessToken'];

    // Update the OAuth2Token of the dataService object
    $dataService->updateOAuth2Token($accessToken);

    // Query for Customer data
    $customerInfo = $dataService->Query("SELECT * FROM Customer");

    // Display relevant customer info
    foreach ($customerInfo as $customer) {
        print_r("Customer ID: " . $customer->Id . "\n");
        print_r("Customer Name: " . $customer->DisplayName . "\n");
        print_r("Customer Email: " . $customer->PrimaryEmailAddr->Address . "\n");
        print_r("Phone Number: " . $customer->PrimaryPhone->FreeFormNumber . "\n");
        print_r("Billing Address: " . $customer->BillAddr->Line1 . ", " . $customer->BillAddr->City . ", " . $customer->BillAddr->PostalCode . "\n");
        print_r("Balance: " . $customer->Balance . "\n");
        print_r("------------------------------------------------\n");
    }

    return $customerInfo;
}

$result = makeAPICall();

?>
