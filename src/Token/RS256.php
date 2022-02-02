<?php

declare(strict_types=1);

namespace KeycloakExtendedGuard\Token;

use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use KeycloakExtendedGuard\Exception\TokenExceptionExtended;
use KeycloakExtendedGuard\TokenInterface;

class RS256 implements TokenInterface
{
    /**
     * @throws TokenExceptionExtended
     */
    public static function decode(string $token = null, string $key = null): ?object
    {
        try {
            if (empty($token)) {
                throw new TokenExceptionExtended("Token is empty");
            }
            $publicKey = self::createPublicKey($key);
            return JWT::decode($token, new Key($publicKey, 'RS256'));
        } catch (Exception $e) {
            throw new TokenExceptionExtended($e->getMessage());
        }
    }

    private static function createPublicKey(string $key): string
    {
        return "-----BEGIN PUBLIC KEY-----\n" . wordwrap($key, 64, "\n", true) . "\n-----END PUBLIC KEY-----";
    }
}
