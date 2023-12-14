<?php

require_once(__DIR__ . '/vendor/autoload.php');
use QuickBooksOnline\API\DataService\DataService;

session_start();

function processCode()
{
    echo "Starting OAuth process...<br>";
    // Create SDK instance
    $config = include('config.php');
    echo "Config loaded...<br>";
    $dataService = DataService::Configure(array(
        'auth_mode' => 'oauth2',
        'ClientID' => $config['client_id'],
        'ClientSecret' =>  $config['client_secret'],
        'RedirectURI' => $config['oauth_redirect_uri'],
        'scope' => $config['oauth_scope'],
        'baseUrl' => "development"
    ));

    echo "DataService configured...<br>";
    $OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();
    echo "OAuth2LoginHelper obtained...<br>";
    $parseUrl = parseAuthRedirectUrl($_SERVER['QUERY_STRING']);
    echo "Parsed URL: " . print_r($parseUrl, true) . "<br>";

    try {
        $accessToken = $OAuth2LoginHelper->exchangeAuthorizationCodeForToken($parseUrl['code'], $parseUrl['realmId']);
        echo "Access token obtained...<br>";
        $dataService->updateOAuth2Token($accessToken);
        echo "OAuth2 token updated in DataService...<br>";

        $_SESSION['sessionAccessToken'] = $accessToken;
        echo "Access token stored in session...<br>";
        echo "Access Token: " . print_r($accessToken, true) . "<br>";
        // Redirect to a different page or show a success message
    } catch (Exception $e) {
        // Handle error
        echo "Error during token exchange: " . $e->getMessage() . "<br>";
    }
}

function parseAuthRedirectUrl($url)
{
    parse_str($url, $qsArray);
    return array(
        'code' => $qsArray['code'] ?? null,
        'realmId' => $qsArray['realmId'] ?? null
    );
}

processCode();

?>
