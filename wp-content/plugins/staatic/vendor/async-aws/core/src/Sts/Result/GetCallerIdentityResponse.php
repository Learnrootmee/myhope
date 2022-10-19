<?php

namespace Staatic\Vendor\AsyncAws\Core\Sts\Result;

use SimpleXMLElement;
use Staatic\Vendor\AsyncAws\Core\Response;
use Staatic\Vendor\AsyncAws\Core\Result;
class GetCallerIdentityResponse extends Result
{
    private $userId;
    private $account;
    private $arn;
    /**
     * @return string|null
     */
    public function getAccount()
    {
        $this->initialize();
        return $this->account;
    }
    /**
     * @return string|null
     */
    public function getArn()
    {
        $this->initialize();
        return $this->arn;
    }
    /**
     * @return string|null
     */
    public function getUserId()
    {
        $this->initialize();
        return $this->userId;
    }
    /**
     * @param Response $response
     * @return void
     */
    protected function populateResult($response)
    {
        $data = new SimpleXMLElement($response->getContent());
        $data = $data->GetCallerIdentityResult;
        $this->userId = ($v = $data->UserId) ? (string) $v : null;
        $this->account = ($v = $data->Account) ? (string) $v : null;
        $this->arn = ($v = $data->Arn) ? (string) $v : null;
    }
}
