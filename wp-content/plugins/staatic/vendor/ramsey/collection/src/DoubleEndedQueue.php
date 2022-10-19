<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Collection;

use Staatic\Vendor\Ramsey\Collection\Exception\InvalidArgumentException;
use Staatic\Vendor\Ramsey\Collection\Exception\NoSuchElementException;
class DoubleEndedQueue extends Queue implements DoubleEndedQueueInterface
{
    private $tail = -1;
    /**
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if ($this->checkType($this->getType(), $value) === \false) {
            throw new InvalidArgumentException('Value must be of type ' . $this->getType() . '; value is ' . $this->toolValueToString($value));
        }
        $this->tail++;
        $this->data[$this->tail] = $value;
    }
    public function addFirst($element) : bool
    {
        if ($this->checkType($this->getType(), $element) === \false) {
            throw new InvalidArgumentException('Value must be of type ' . $this->getType() . '; value is ' . $this->toolValueToString($element));
        }
        $this->index--;
        $this->data[$this->index] = $element;
        return \true;
    }
    public function addLast($element) : bool
    {
        return $this->add($element);
    }
    public function offerFirst($element) : bool
    {
        try {
            return $this->addFirst($element);
        } catch (InvalidArgumentException $e) {
            return \false;
        }
    }
    public function offerLast($element) : bool
    {
        return $this->offer($element);
    }
    public function removeFirst()
    {
        return $this->remove();
    }
    public function removeLast()
    {
        $tail = $this->pollLast();
        if ($tail === null) {
            throw new NoSuchElementException('Can\'t return element from Queue. Queue is empty.');
        }
        return $tail;
    }
    public function pollFirst()
    {
        return $this->poll();
    }
    public function pollLast()
    {
        if ($this->count() === 0) {
            return null;
        }
        $tail = $this[$this->tail];
        unset($this[$this->tail]);
        $this->tail--;
        return $tail;
    }
    public function firstElement()
    {
        return $this->element();
    }
    public function lastElement()
    {
        if ($this->count() === 0) {
            throw new NoSuchElementException('Can\'t return element from Queue. Queue is empty.');
        }
        return $this->data[$this->tail];
    }
    public function peekFirst()
    {
        return $this->peek();
    }
    public function peekLast()
    {
        if ($this->count() === 0) {
            return null;
        }
        return $this->data[$this->tail];
    }
}
