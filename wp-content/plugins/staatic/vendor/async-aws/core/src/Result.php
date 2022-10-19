<?php

declare (strict_types=1);
namespace Staatic\Vendor\AsyncAws\Core;

use Staatic\Vendor\AsyncAws\Core\Exception\Http\HttpException;
use Staatic\Vendor\AsyncAws\Core\Exception\Http\NetworkException;
class Result
{
    protected $awsClient;
    protected $input;
    private $initialized = \false;
    private $response;
    private $prefetchResults = [];
    public function __construct(Response $response, AbstractApi $awsClient = null, $request = null)
    {
        $this->response = $response;
        $this->awsClient = $awsClient;
        $this->input = $request;
    }
    public function __destruct()
    {
        while (!empty($this->prefetchResponses)) {
            \array_shift($this->prefetchResponses)->cancel();
        }
    }
    /**
     * @param float|null $timeout
     */
    public final function resolve($timeout = null) : bool
    {
        return $this->response->resolve($timeout);
    }
    /**
     * @param mixed[] $results
     * @param float|null $timeout
     * @param bool $downloadBody
     * @return mixed[]
     */
    public static final function wait($results, $timeout = null, $downloadBody = \false)
    {
        $resultMap = [];
        $responses = [];
        foreach ($results as $index => $result) {
            $responses[$index] = $result->response;
            $resultMap[$index] = $result;
        }
        foreach (Response::wait($responses, $timeout, $downloadBody) as $index => $response) {
            (yield $index => $resultMap[$index]);
        }
    }
    public final function info() : array
    {
        return $this->response->info();
    }
    /**
     * @return void
     */
    public final function cancel()
    {
        $this->response->cancel();
    }
    /**
     * @param $this $result
     * @return void
     */
    protected final function registerPrefetch($result)
    {
        $this->prefetchResults[\spl_object_id($result)] = $result;
    }
    /**
     * @param $this $result
     * @return void
     */
    protected final function unregisterPrefetch($result)
    {
        unset($this->prefetchResults[\spl_object_id($result)]);
    }
    /**
     * @return void
     */
    protected final function initialize()
    {
        if ($this->initialized) {
            return;
        }
        $this->resolve();
        $this->initialized = \true;
        $this->populateResult($this->response);
    }
    /**
     * @param Response $response
     * @return void
     */
    protected function populateResult($response)
    {
    }
}
