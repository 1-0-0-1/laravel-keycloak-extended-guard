# Keycloak Extended Guard for Laravel

This package helps you authenticate users on a Laravel API based on JWT tokens generated from Keycloak Server with remote checks.

# Install
Require the package
```
composer require 1-0-0-1/laravel-keycloak-extended-guard
```

# Configuration

## Laravel Auth

Changes on `config/auth.php`
```php
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],
    'keycloak' => [ # <-- Add this block
        'driver' => 'keycloak',
        'provider' => 'users',
    ],
```
## Laravel Routes
Just protect some endpoints on `routes/api.php` and you are done!

```php
// public endpoints
Route::get('/hello', function () {
    return ':)';
});

// protected endpoints
Route::middleware('auth:keycloak')->post('/secret_page', function () {
    return 'Welcome to secret place';
});
```

## Keycloak Guard
The Keycloak Extended Guard configuration can be handled from Laravel `.env` file. Be sure all strings **are trimmed.**

Optionally you can publish the config file.

```
php artisan vendor:publish  --provider="KeycloakExtendedGuard\KeycloakExtendedGuardServiceProvider"
```


```php
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
```
**client_secret**

*Required.*

The Keycloak Server realm public key (string).
> How to get realm public key? Click on "Realm Settings" > "Keys" > "Algorithm RS256" Line > "Public Key" Button

**load_user**

*Required. Default is `false`.*

If you do not have an `users` table you must disable this.
It fetchs user from database and fill values into authenticated user object. If enabled, it will work together with `user_provider_credential` and `token_principal_attribute`.

**user_provider_credential**

*Required. Default is `username`.*

The field from "users" table that contains the user unique identifier (eg.  username, email, nickname). This will be confronted against  `token_principal_attribute` attribute, while authenticating.

**token_principal_attribute**

*Required. Default is `preferred_username`.*

The property from JWT token that contains the user identifier.
This will be confronted against  `user_provider_credential` attribute, while authenticating.

**required_server_confirm**

*Required. Default is `false`.*

Allow token validation on keycloak server

**base_url**

*Optional*

Keycloak server URL

 **realm**

*Optional*

Keycloak realm name
