<?php

namespace App\Auth;

use App\Services\JWTService;
use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;
use Lcobucci\JWT\UnencryptedToken;
use Throwable;

class JWTGuard implements Guard
{
    use GuardHelpers;

    public function __construct(UserProvider $provider, protected Request $request, protected JWTService $jwt)
    {
        $this->provider = $provider;
    }

    /**
     * Get the currently authenticated user.
     */
    public function user(): ?Authenticatable
    {
        if (!is_null($this->user)) {
            return $this->user;
        }

        $token = $this->jwt->getTokenFromRequestHeader($this->request);

        if (!$token) {
            return null;
        }

        try {
            $payload = $this->jwt->parseToken($token);
            return $this->user = $this->provider->retrieveById($payload['uuid']);
        } catch (Throwable $th) {
            return null;
        }
    }

    /**
     * Validate a user's credentials.
     *
     * @param array<string, string|int|bool> $credentials
     */
    public function validate(array $credentials = []): bool
    {
        return (bool) $this->attempt($credentials, false);
    }

    /**
     * Attempt to authenticate the user using the given credentials and return the token.
     *
     * @param array<string, string|int|bool> $credentials
     */
    public function attempt(array $credentials = [], bool $login = true): bool|UnencryptedToken
    {
        $user = $this->provider->retrieveByCredentials($credentials);

        if ($this->hasValidCredentials($user, $credentials)) {
            return $login ? $this->login($user) : true;
        }

        return false;
    }

    /**
     * Create a token for a user.
     */
    public function login(Authenticatable $user): UnencryptedToken
    {
        $token = $this->jwt->fromUser($user);
        $this->user = $user;

        return $token;
    }

    /**
     * Logout the user, thus invalidating the token.
     */
    public function logout(): void
    {
        $this->invalidate();
    }

    /**
     * Invalidate the token.
     */
    public function invalidate(): void
    {
        // TODO: Add implementation to invalidate the token.
        // $this->jwt->invalidateToken();
    }

    /**
     * Determine if the user matches the credentials.
     *
     * @param array<string, string|int|bool> $credentials
     */
    protected function hasValidCredentials(?Authenticatable $user, array $credentials): bool
    {
        return !is_null($user) && $this->provider->validateCredentials($user, $credentials);
    }
}
