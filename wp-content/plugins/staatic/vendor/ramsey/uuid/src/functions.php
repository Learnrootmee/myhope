<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Uuid;

use DateTimeInterface;
use Staatic\Vendor\Ramsey\Uuid\Type\Hexadecimal;
use Staatic\Vendor\Ramsey\Uuid\Type\Integer as IntegerObject;
/**
 * @param int|null $clockSeq
 */
function v1($node = null, $clockSeq = null) : string
{
    return Uuid::uuid1($node, $clockSeq)->toString();
}
/**
 * @param IntegerObject|null $localIdentifier
 * @param Hexadecimal|null $node
 * @param int|null $clockSeq
 */
function v2(int $localDomain, $localIdentifier = null, $node = null, $clockSeq = null) : string
{
    return Uuid::uuid2($localDomain, $localIdentifier, $node, $clockSeq)->toString();
}
function v3($ns, string $name) : string
{
    return Uuid::uuid3($ns, $name)->toString();
}
function v4() : string
{
    return Uuid::uuid4()->toString();
}
function v5($ns, string $name) : string
{
    return Uuid::uuid5($ns, $name)->toString();
}
/**
 * @param Hexadecimal|null $node
 * @param int|null $clockSeq
 */
function v6($node = null, $clockSeq = null) : string
{
    return Uuid::uuid6($node, $clockSeq)->toString();
}
/**
 * @param \DateTimeInterface|null $dateTime
 */
function v7($dateTime = null) : string
{
    return Uuid::uuid7($dateTime)->toString();
}
