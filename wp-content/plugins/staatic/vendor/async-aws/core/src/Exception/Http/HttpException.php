<?php

declare (strict_types=1);
namespace Staatic\Vendor\AsyncAws\Core\Exception\Http;

use Staatic\Vendor\AsyncAws\Core\Exception\Exception;
use Staatic\Vendor\Symfony\Contracts\HttpClient\ResponseInterface;
interface HttpException extends Exception
{
    public function getResponse() : ResponseInterface;
    /**
     * @return string|null
     */
    public function getAwsCode();
    /**
     * @return string|null
     */
    public function getAwsType();
    /**
     * @return string|null
     */
    public function getAwsMessage();
    /**
     * @return string|null
     */
    public function getAwsDetail();
}
