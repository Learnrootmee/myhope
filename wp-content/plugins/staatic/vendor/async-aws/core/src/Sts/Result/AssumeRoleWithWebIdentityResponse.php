<?php

namespace Staatic\Vendor\AsyncAws\Core\Sts\Result;

use SimpleXMLElement;
use DateTimeImmutable;
use Staatic\Vendor\AsyncAws\Core\Response;
use Staatic\Vendor\AsyncAws\Core\Result;
use Staatic\Vendor\AsyncAws\Core\Sts\ValueObject\AssumedRoleUser;
use Staatic\Vendor\AsyncAws\Core\Sts\ValueObject\Credentials;
class AssumeRoleWithWebIdentityResponse extends Result
{
    private $credentials;
    private $subjectFromWebIdentityToken;
    private $assumedRoleUser;
    private $packedPolicySize;
    private $provider;
    private $audience;
    private $sourceIdentity;
    /**
     * @return \Staatic\Vendor\AsyncAws\Core\Sts\ValueObject\AssumedRoleUser|null
     */
    public function getAssumedRoleUser()
    {
        $this->initialize();
        return $this->assumedRoleUser;
    }
    /**
     * @return string|null
     */
    public function getAudience()
    {
        $this->initialize();
        return $this->audience;
    }
    /**
     * @return Credentials|null
     */
    public function getCredentials()
    {
        $this->initialize();
        return $this->credentials;
    }
    /**
     * @return int|null
     */
    public function getPackedPolicySize()
    {
        $this->initialize();
        return $this->packedPolicySize;
    }
    /**
     * @return string|null
     */
    public function getProvider()
    {
        $this->initialize();
        return $this->provider;
    }
    /**
     * @return string|null
     */
    public function getSourceIdentity()
    {
        $this->initialize();
        return $this->sourceIdentity;
    }
    /**
     * @return string|null
     */
    public function getSubjectFromWebIdentityToken()
    {
        $this->initialize();
        return $this->subjectFromWebIdentityToken;
    }
    /**
     * @param Response $response
     * @return void
     */
    protected function populateResult($response)
    {
        $data = new SimpleXMLElement($response->getContent());
        $data = $data->AssumeRoleWithWebIdentityResult;
        $this->credentials = !$data->Credentials ? null : new Credentials(['AccessKeyId' => (string) $data->Credentials->AccessKeyId, 'SecretAccessKey' => (string) $data->Credentials->SecretAccessKey, 'SessionToken' => (string) $data->Credentials->SessionToken, 'Expiration' => new DateTimeImmutable((string) $data->Credentials->Expiration)]);
        $this->subjectFromWebIdentityToken = ($v = $data->SubjectFromWebIdentityToken) ? (string) $v : null;
        $this->assumedRoleUser = !$data->AssumedRoleUser ? null : new AssumedRoleUser(['AssumedRoleId' => (string) $data->AssumedRoleUser->AssumedRoleId, 'Arn' => (string) $data->AssumedRoleUser->Arn]);
        $this->packedPolicySize = ($v = $data->PackedPolicySize) ? (int) (string) $v : null;
        $this->provider = ($v = $data->Provider) ? (string) $v : null;
        $this->audience = ($v = $data->Audience) ? (string) $v : null;
        $this->sourceIdentity = ($v = $data->SourceIdentity) ? (string) $v : null;
    }
}
