<?php

namespace Staatic\Vendor\AsyncAws\Core\Sts\ValueObject;

use DateTimeImmutable;
final class Credentials
{
    private $accessKeyId;
    private $secretAccessKey;
    private $sessionToken;
    private $expiration;
    public function __construct(array $input)
    {
        $this->accessKeyId = $input['AccessKeyId'] ?? null;
        $this->secretAccessKey = $input['SecretAccessKey'] ?? null;
        $this->sessionToken = $input['SessionToken'] ?? null;
        $this->expiration = $input['Expiration'] ?? null;
    }
    public static function create($input) : self
    {
        return $input instanceof self ? $input : new self($input);
    }
    public function getAccessKeyId() : string
    {
        return $this->accessKeyId;
    }
    public function getExpiration() : DateTimeImmutable
    {
        return $this->expiration;
    }
    public function getSecretAccessKey() : string
    {
        return $this->secretAccessKey;
    }
    public function getSessionToken() : string
    {
        return $this->sessionToken;
    }
}
