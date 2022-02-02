<?php

declare(strict_types=1);

namespace KeycloakExtendedGuard;

use Exception;
use Illuminate\Support\Facades\Http;
use KeycloakExtendedGuard\Exception\KeycloakRemoteServerException;
use function config;

class KeycloakRemoteServer
{
    private array $config;

    public function __construct()
    {
        $this->config = config('keycloak');
    }

    /**
     * @throws KeycloakRemoteServerException
     */
    public function validateToken(string $token): void
    {
        try {
            $response = Http::withToken($token)->get($this->getBaseUrl() . '/protocol/openid-connect/userinfo');
            if (!$response->ok()) {
                throw new KeycloakRemoteServerException($response->json('error_description'), $response->status());
            }
        } catch (Exception $e) {
            throw new KeycloakRemoteServerException($e->getMessage(), $e->getCode());
        }
    }

    protected function getBaseUrl(): string
    {
        return rtrim(rtrim($this->config['base_url'], '/') . '/realms/' . $this->config['realm'], '/');
    }
}
