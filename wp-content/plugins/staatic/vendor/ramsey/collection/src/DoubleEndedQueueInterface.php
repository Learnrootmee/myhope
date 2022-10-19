<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Collection;

use Staatic\Vendor\Ramsey\Collection\Exception\NoSuchElementException;
interface DoubleEndedQueueInterface extends QueueInterface
{
    public function addFirst($element) : bool;
    public function addLast($element) : bool;
    public function offerFirst($element) : bool;
    public function offerLast($element) : bool;
    public function removeFirst();
    public function removeLast();
    public function pollFirst();
    public function pollLast();
    public function firstElement();
    public function lastElement();
    public function peekFirst();
    public function peekLast();
}
