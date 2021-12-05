INSERT INTO SimpleSAMLphp_oauth2_client 
    ('id', 'name', 'description', 'auth_source', 'secret', 'redirect_uri', 'scopes', 'is_confidential')
VALUES (
    '_a166287db4f4697a0f7faddaed857d92ef53060d13',
    'demo1client',
    'demo1client desc',
    'demo1',
    '_cc39c61257c4fdbba6721185520d9f3ccb9747fb90',
    '["http:\/\/127.0.0.1:8000\/dummyclient\/oauth2\/endpoint.php"]',
    '["basic", "extras"]',
    1);
