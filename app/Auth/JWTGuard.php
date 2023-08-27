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
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user(): ?Authenticatable
    {
        if (!is_null($this->user)) return $this->user;

        $token = $this->jwt->getTokenFromRequestHeader($this->request);

        if (!$token) return null;

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
     * @param  array $credentials
     * @return bool
     */
    public function validate(array $credentials = []): bool
    {
        return (bool) $this->attempt($credentials, false);
    }

    /**
     * Attempt to authenticate the user using the given credentials and return the token.
     *
     * @param  array $credentials
     * @param  bool $login
     * @return bool UnencryptedToken
     */
    public function attempt(array $credentials = [], $login = true): bool|UnencryptedToken
    {
        $user = $this->provider->retrieveByCredentials($credentials);

        if ($this->hasValidCredentials($user, $credentials)) {
            return $login ? $this->login($user) : true;
        }

        return false;
    }

    /**
     * Create a token for a user.
     *
     * @param  Authenticatable  $user
     * @return UnencryptedToken
     */
    public function login(Authenticatable $user): UnencryptedToken
    {
        $token = $this->jwt->fromUser($user);
        $this->user = $user;

        return $token;
    }

    /**
     * Logout the user, thus invalidating the token.
     *
     * @return void
     */
    public function logout()
    {
        $this->invalidate();
    }

    /**
     * Invalidate the token.
     *
     * @return void
     */
    public function invalidate()
    {
        // TODO: Add implementation to invalidate the token.
        // $this->jwt->invalidateToken();
    }

    /**
     * Determine if the user matches the credentials.
     *
     * @param  Authenticatable  $user
     * @param  array  $credentials
     * @return bool
     */
    protected function hasValidCredentials(Authenticatable $user, $credentials): bool
    {
        return $user !== null && $this->provider->validateCredentials($user, $credentials);
    }
}
