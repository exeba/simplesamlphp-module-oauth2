<?php 
require 'config.php';
require 'utils.php';

$provider = new DemoProvider($client, $server);

?>
<html>
	<body>
        <form method="POST" action="<?= $provider->getAuthorizationUrl($dummyState, $dummyVerifier) ?>">
            <input type="hidden" name="grant_type" value="auhtorization_code" />
            <button name="auth_code" type="submit">Auth Code</button>
        </form>

        <form method="POST" action="<?= $provider->getAccessTokenEndpoint() ?>">
            <input type="hidden" name="grant_type" value="client_credentials" />
            <input type="hidden" name="client_id" value="<?= $client['id'] ?>" />
            <input type="hidden" name="client_secret" value="<?= $client['secret'] ?>" />
            <input type="hidden" name="scope" value="<?= $client['scope'] ?>" />
            <button name="client_credentials" type="submit">Client credentials</button>
        </form>
	</body>
</html>
