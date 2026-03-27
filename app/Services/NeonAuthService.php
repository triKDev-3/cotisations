<?php

namespace App\Services;

use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Exception;

class NeonAuthService
{
    protected string $jwksUrl;

    public function __construct()
    {
        $this->jwksUrl = config('services.neon.auth_jwks_url', env('NEON_AUTH_JWKS_URL'));
    }

    /**
     * Verify a JWT token from Neon Auth using JWKS.
     */
    public function verifyToken(string $token): ?array
    {
        try {
            $jwks = $this->getJwks();
            
            // Parse JWKS to usable public keys for Firebase/JWT
            $keys = JWK::parseKeySet($jwks);
            
            // Verify and decode the token
            // Note: In an OIDC scenario, we should also verify the audience and issuer.
            $decoded = JWT::decode($token, $keys);
            
            return (array) $decoded;
        } catch (Exception $e) {
            logger()->error('Neon Auth Verification failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get JWKS from URL, with caching to avoid extra hits.
     */
    protected function getJwks(): array
    {
        return Cache::remember('neon_auth_jwks', 3600, function () {
            $response = Http::get($this->jwksUrl);
            
            if ($response->failed()) {
                throw new Exception('Could not fetch JWKS from Neon Auth.');
            }
            
            return $response->json();
        });
    }
}
