<?php 
require '../config.php'; 
require '../utils.php';

$provider = new DemoProvider($client, $server);

$token = httpPost(
        $provider->getAccessTokenEndpoint(),
        $provider->authCodeGrantParams(urldecode($_GET['code']), $dummyVerifier));

$userInfo = httpGet($provider->getUserInfoEndpoint(), $token->access_token);

$refreshedToken = httpPost(
        $provider->getAccessTokenEndpoint(),
        $provider->refreshTokenGrantParams($token->refresh_token));

?>
<!DOCTYPE html>
<html>
    <head>
        <title></title>
    </head>
    <body>
        <h2>Issued token:</h2>
        <pre><?= json_encode($token, JSON_PRETTY_PRINT) ?></pre>
            <?= $token->access_token ?>
        <hr>
        <h2>User info:</h2>
        <pre><?= json_encode($userInfo, JSON_PRETTY_PRINT) ?></pre>
        <hr>
        <h2>Refreshed token:</h2>
        <pre><?= json_encode($refreshedToken, JSON_PRETTY_PRINT) ?></pre>
    </body>
</html>
