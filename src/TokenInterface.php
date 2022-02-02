<?php

namespace KeycloakExtendedGuard;

interface TokenInterface
{
    public static function decode(string $token = null, string $key = null);
}
