<?php

namespace App\Services;

use App\Exceptions\JWTException;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterval;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Lcobucci\JWT\Encoder;
use Lcobucci\JWT\Encoding\CannotDecodeContent;
use Lcobucci\JWT\Encoding\ChainedFormatter;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\Token\Builder;
use Lcobucci\JWT\Token\InvalidTokenStructure;
use Lcobucci\JWT\Token\Parser;
use Lcobucci\JWT\Token\UnsupportedHeaderFound;
use Lcobucci\JWT\UnencryptedToken;
use Lcobucci\JWT\Validation\Constraint\IssuedBy;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Constraint\StrictValidAt;
use Lcobucci\JWT\Validation\RequiredConstraintsViolated;
use Lcobucci\JWT\Validation\Validator;
use Psr\Clock\ClockInterface;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class JWTService
{
    protected Signer $algorithm;

    protected Encoder $encoder;

    protected ClockInterface $clock;

    public function __construct()
    {
        $this->algorithm = new Sha256();

        $this->encoder = new JoseEncoder();

        $this->clock = new class() implements ClockInterface {
            public function now(): CarbonImmutable
            {
                return CarbonImmutable::now();
            }
        };
    }

    /**
     * Generate a JWT token with the specified claims.
     *
     * @param string $subject The subject to include in the token.
     * @param array $claims The claims to include in the token.
     *
     * @return UnencryptedToken The generated JWT token.
     */
    public function generateToken(string $subject, array $claims = []): UnencryptedToken
    {
        $builder = $this->createBaseBuilder()
            ->relatedTo($subject)
            ->issuedAt($this->clock->now())
            ->expiresAt($this->clock->now()->addMinutes(config('jwt.ttl')));

        foreach ($claims as $name => $value) {
            $builder = $builder->withClaim($name, $value);
        }

        return $builder->getToken($this->algorithm, $this->secretKey());
    }

    /**
     * Parse and validate a JWT token, returning its claims.
     *
     * @param string $token The JWT token to parse and validate.
     *
     * @return array The parsed and validated claims.
     *
     * @throws JWTException If the token parsing or validation fails.
     */
    public function parseToken(string $token): array
    {
        $parsedToken = $this->parse($token);
        $this->checkConstraints($parsedToken);

        return $parsedToken->claims()->all();
    }

    /**
     * Get the JWT token from the "Authorization" header in the request.
     *
     * @param \Illuminate\Http\Request $request The incoming request instance.
     *
     * @return string|null The JWT token extracted from the header, or null if not present.
     */
    public function getTokenFromRequestHeader(Request $request): ?string
    {
        $header = $request->header('Authorization', '');
        $position = strrpos($header, 'Bearer ');

        if ($position === false) return null;

        $header = substr($header, $position + 7);

        $token = str_contains($header, ',') ? strstr($header, ',', true) : $header;

        return $token;
    }

    /**
     * Generate a JWT token for the given user.
     *
     * @param Authenticatable $user The user for whom the token will be generated.
     *
     * @return UnencryptedToken The generated JWT token for the user.
     */
    public function fromUser(Authenticatable $user): UnencryptedToken
    {
        $claims = [$user->getAuthIdentifierName() => $user->getAuthIdentifier()];
        $token = $this->generateToken($user->id, $claims);

        return $token;
    }

    /**
     * Get the secret key for token signing.
     *
     * @return InMemory The secret key for token signing.
     */
    protected function secretKey(): InMemory
    {
        return InMemory::file(config('jwt.private'), config('jwt.passphrase'));
    }

    /**
     * Get the public key for token verification.
     *
     * @return InMemory The public key for token verification.
     */
    protected function publicKey(): InMemory
    {
        return InMemory::file(config('jwt.public'));
    }

    /**
     * Create the base token builder with common claims.
     *
     * @return Builder The base token builder.
     */
    protected function createBaseBuilder(): Builder
    {
        $formatter = ChainedFormatter::default();

        return (new Builder($this->encoder, $formatter))
            ->issuedBy(config('app.url'))
            ->identifiedBy(Str::uuid())
            ->canOnlyBeUsedAfter($this->clock->now())
            ->issuedAt($this->clock->now());
    }

    /**
     * Parse a JWT token and return the parsed token instance.
     *
     * @param string $token The JWT token to parse.
     *
     * @return UnencryptedToken The parsed JWT token.
     *
     * @throws JWTException If the token parsing fails.
     */
    protected function parse(string $token): UnencryptedToken
    {
        $parser = new Parser(new JoseEncoder());

        try {
            $parsedToken = $parser->parse($token);
        } catch (CannotDecodeContent | InvalidTokenStructure | UnsupportedHeaderFound $e) {
            throw new JWTException('Malformed Token', Response::HTTP_UNAUTHORIZED, $e);
        } catch (Throwable $e) {
            throw new JWTException('Error parsing token', Response::HTTP_UNAUTHORIZED, $e);
        }

        return $parsedToken;
    }

    /**
     * Check the token against validation constraints.
     *
     * @param Token $token The parsed JWT token to be validated.
     *
     * @return void
     *
     * @throws JWTException If the token validation fails.
     */
    protected function checkConstraints(Token $token): void
    {
        $constraints = [
            new StrictValidAt($this->clock, $this->getLeewayInterval()),
            new IssuedBy(config('app.url')),
            new SignedWith($this->algorithm, $this->publicKey()),
        ];

        $validator = new Validator();

        try {
            $validator->assert($token, ...$constraints);
        } catch (RequiredConstraintsViolated $e) {
            $violation = Arr::first($e->violations());
            throw new JWTException($violation->getMessage(), $violation->getCode(), $violation);
        } catch (Throwable $e) {
            throw new JWTException('Error validating token', Response::HTTP_UNAUTHORIZED, $e);
        }
    }

    /**
     * Get the leeway interval for token validation.
     *
     * @return CarbonInterval|null The leeway interval or null if not set.
     */
    protected function getLeewayInterval(): ?CarbonInterval
    {
        $leewayInSeconds = config('jwt.leeway');

        return $leewayInSeconds ? CarbonInterval::seconds($leewayInSeconds) : null;
    }
}
