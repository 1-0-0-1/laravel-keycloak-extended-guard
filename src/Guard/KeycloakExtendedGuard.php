<?php

declare(strict_types=1);

namespace KeycloakExtendedGuard\Guard;

use Exception;
use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;
use KeycloakExtendedGuard\Exception\KeycloakExtendedGuardException;
use KeycloakExtendedGuard\Exception\UserNotFoundExceptionExtended;
use KeycloakExtendedGuard\KeycloakRemoteServer;
use KeycloakExtendedGuard\Token\RS256;
use function config;

class KeycloakExtendedGuard implements Guard
{
    use GuardHelpers;

    private Request $request;
    private array $config;
    private ?object $decodedToken = null;
    private ?KeycloakRemoteServer $keycloakRemoteServer;

    public function __construct(UserProvider $provider, Request $request, KeycloakRemoteServer $keycloakRemoteServer = null)
    {
        $this->config = config('keycloak');
        $this->request = $request;
        $this->provider = $provider;
        $this->keycloakRemoteServer = $keycloakRemoteServer;
        $this->user = null;

        $this->authenticate();
    }

    /**
     * @throws KeycloakExtendedGuardException
     */
    public function authenticate(): void
    {
        $token = $this->request->bearerToken();
        try {
            $decodedToken = RS256::decode($token, $this->config['client_secret']);

            if ($this->config['required_server_confirm']) {
                $this->keycloakRemoteServer->validateToken($token);
            }
            $this->decodedToken = $decodedToken;
            $this->validate([
                $this->config['user_provider_credential'] => $this->decodedToken->{$this->config['token_principal_attribute']}
            ]);
        } catch (Exception $e) {
            throw new KeycloakExtendedGuardException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Determine if the current user is authenticated.
     *
     * @return bool
     */
    public function check(): bool
    {
        return !is_null($this->user());
    }

    /**
     * Get the currently authenticated user.
     *
     * @return Authenticatable|null
     */
    public function user(): ?Authenticatable
    {
        if (!is_null($this->user)) {
            return $this->user;
        }
        return null;
    }

    /**
     * Validate a user's credentials.
     *
     * @param array $credentials
     * @return bool
     * @throws UserNotFoundExceptionExtended
     */
    public function validate(array $credentials = []): bool
    {
        if (!$this->decodedToken) {
            return false;
        }

        if ($this->config['load_user']) {
            $user = $this->provider->retrieveByCredentials($credentials);
            if (!$user) {
                throw new UserNotFoundExceptionExtended("User not found. Credentials: " . json_encode($credentials));
            }
        } else {
            $class = $this->provider->getModel();
            $user = new $class();
        }
        $this->setUser($user);
        return true;
    }
}
