<?php

namespace Staatic\Vendor\AsyncAws\Core\Credentials;

use DateTimeImmutable;
use Staatic\Vendor\AsyncAws\Core\Result;
trait DateFromResult
{
    /**
     * @return DateTimeImmutable|null
     */
    private function getDateFromResult(Result $result)
    {
        $response = $result->info()['response'];
        if (null !== ($date = $response->getHeaders(\false)['date'][0] ?? null)) {
            return new DateTimeImmutable($date);
        }
        return null;
    }
}
