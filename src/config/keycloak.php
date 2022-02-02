<?php

return [
    // Keycloak secret (Configure->Realm Settings->Keys->RS256->Public key)
    'client_secret' => env('KEYCLOAK_CLIENT_SECRET', ''),
    // Confirm token with request to keycloak server
    'required_server_confirm' => env('KEYCLOAK_REQUIRED_SERVER_CONFIRM', false),
    // Keycloak server URL
    'base_url' => env('KEYCLOAK_BASE_URL', ''),
    // Keycloak realm
    'realm' => env('KEYCLOAK_REALM', ''),
    // Load user from user provider if token valid
    'load_user' => env('KEYCLOAK_LOAD_USER', false),
    'user_provider_credential' => env('KEYCLOAK_USER_PROVIDER_CREDENTIAL', 'username'),
    'token_principal_attribute' => env('KEYCLOAK_TOKEN_PRINCIPAL_ATTRIBUTE', 'preferred_username'),
];
