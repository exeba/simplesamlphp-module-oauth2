<?php

function httpPost($url, $data) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);

    $encodedData = join('&', array_map(function($key) use ($data) {
        return $key."=".$data[$key];
    }, array_keys($data)));

    curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData);      
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $server_output = curl_exec($ch);
    curl_close($ch);

    return json_decode($server_output);
}

function httpGet($url, $token = null) {
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);
    if ($token) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'authorization: Bearer '.$token
        ]);
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $server_output = curl_exec($ch);
    curl_close($ch);

    return json_decode($server_output);
}

class DemoProvider {

    private $clientConfig;
    private $serverConfig;
    
    public function __construct($clientConfig, $serverConfig)
    {
        $this->clientConfig = $clientConfig;
        $this->serverConfig = $serverConfig;
    }

    public function getClientEndpoint()
    {
        return $this->clientConfig['redirect_uri'];
    }

    public function getUserInfoEndpoint()
    {
        return $this->serverConfig['userInfoEndpoint'];
    }

    public function getAuthorizationEndpoint()
    {
        return $this->serverConfig['authoritazionEndpoint'];
    }

    public function getAccessTokenEndpoint()
    {
        return $this->serverConfig['accessTokenEndpoint'];  
    }

    public function authCodeGrantParams($authCode, $verifier = null)
    {
        $params = [
            'grant_type' => 'authorization_code',
            'client_id' => $this->clientConfig['id'],
            'client_secret' => $this->clientConfig['secret'],
            'redirect_uri' => $this->clientConfig['redirect_uri'],
            'code' => $authCode
        ];

        if ($verifier) {
            $params['code_verifier'] = $verifier;            
        }

        return $params;
    }

    public function refreshTokenGrantParams($refreshToken) {
        return [
            'client_id' => $this->clientConfig['id'],
            'client_secret' => $this->clientConfig['secret'],
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
        ];
    }

    public function getAuthorizationUrl($state, $verifier)
    {
        return $this->getAuthorizationEndpoint()
            .'?'.$this->authorizationQuery($state, $this->buildS256Challenge($verifier));
    }


    private function buildS256Challenge($verifier)
    {
        return strtr(rtrim(base64_encode(hash('sha256', $verifier, true)), '='), '+/', '-_');
    }

    public function authorizationQuery($state, $challenge = null)
    {
        $params = $this->authorizationQueryParams($state, $challenge);
        
        return join('&', array_map(function($paramName) use (&$params) {
            return $paramName.'='.$params[$paramName];
        }, array_keys($params)));
    }

    public function authorizationQueryParams($state, $challenge = null)
    {
        $params = [
            'client_id' => $this->clientConfig['id'],
            'scope' => $this->clientConfig['scope'],
            'response_type' => 'code',
            'state' => $state,
            'redirect_uri' => $this->clientConfig['redirect_uri']
        ];

        if ($challenge) {
            $params['code_challenge'] = $challenge;
            $params['code_challenge_method'] = 'S256';
        }

        return $params;
    }

}