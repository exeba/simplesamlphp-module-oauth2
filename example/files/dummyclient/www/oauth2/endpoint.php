<?php 
require '../config.php'; 
require '../utils.php';

$provider = new DemoProvider($client, $server);

if (array_key_exists('code', $_GET)) {
    $token = httpPost(
            $provider->getAccessTokenEndpoint(),
            $provider->authCodeGrantParams(urldecode($_GET['code']), $dummyVerifier));
    $userInfo = httpGet($provider->getUserInfoEndpoint(), $token->access_token);
    $refreshedToken = httpPost(
            $provider->getAccessTokenEndpoint(),
            $provider->refreshTokenGrantParams($token->refresh_token));
    $error = null;
} else {
    $token = null;
    $userInfo = null;
    $refreshedToken = null;
    $error = $_GET['error'];
}

?>
<!DOCTYPE html>
<html>
    <head>
        <title></title>
    </head>
    <body>
        <h2>Issued token:</h2>
        <pre name="token"><?= json_encode($token, JSON_PRETTY_PRINT) ?></pre>
        <hr>
        <h2>User info:</h2>
        <pre name="userInfo"><?= json_encode($userInfo, JSON_PRETTY_PRINT) ?></pre>
        <hr>
        <h2>Refreshed token:</h2>
        <pre name="refreshedToken"><?= json_encode($refreshedToken, JSON_PRETTY_PRINT) ?></pre>
        <hr>
        <h2>Error:</h2>
        <pre name="error"><?= json_encode($error, JSON_PRETTY_PRINT) ?></pre>
    </body>
</html>
