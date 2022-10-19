<?php

namespace Staatic\Vendor\AsyncAws\S3\Result;

use IteratorAggregate;
use Traversable;
use SimpleXMLElement;
use DateTimeImmutable;
use Staatic\Vendor\AsyncAws\Core\Response;
use Staatic\Vendor\AsyncAws\Core\Result;
use Staatic\Vendor\AsyncAws\S3\ValueObject\Bucket;
use Staatic\Vendor\AsyncAws\S3\ValueObject\Owner;
class ListBucketsOutput extends Result implements IteratorAggregate
{
    private $buckets;
    private $owner;
    /**
     * @return mixed[]
     */
    public function getBuckets()
    {
        $this->initialize();
        return $this->buckets;
    }
    public function getIterator() : Traversable
    {
        yield from $this->getBuckets();
    }
    /**
     * @return Owner|null
     */
    public function getOwner()
    {
        $this->initialize();
        return $this->owner;
    }
    /**
     * @param Response $response
     * @return void
     */
    protected function populateResult($response)
    {
        $data = new SimpleXMLElement($response->getContent());
        $this->buckets = !$data->Buckets ? [] : $this->populateResultBuckets($data->Buckets);
        $this->owner = !$data->Owner ? null : new Owner(['DisplayName' => ($v = $data->Owner->DisplayName) ? (string) $v : null, 'ID' => ($v = $data->Owner->ID) ? (string) $v : null]);
    }
    private function populateResultBuckets(SimpleXMLElement $xml) : array
    {
        $items = [];
        foreach ($xml->Bucket as $item) {
            $items[] = new Bucket(['Name' => ($v = $item->Name) ? (string) $v : null, 'CreationDate' => ($v = $item->CreationDate) ? new DateTimeImmutable((string) $v) : null]);
        }
        return $items;
    }
}
