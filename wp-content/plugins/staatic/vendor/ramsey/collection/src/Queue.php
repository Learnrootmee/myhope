<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Collection;

use Staatic\Vendor\Ramsey\Collection\Exception\InvalidArgumentException;
use Staatic\Vendor\Ramsey\Collection\Exception\NoSuchElementException;
use Staatic\Vendor\Ramsey\Collection\Tool\TypeTrait;
use Staatic\Vendor\Ramsey\Collection\Tool\ValueToStringTrait;
class Queue extends AbstractArray implements QueueInterface
{
    use TypeTrait;
    use ValueToStringTrait;
    private $queueType;
    protected $index = 0;
    public function __construct(string $queueType, array $data = [])
    {
        $this->queueType = $queueType;
        parent::__construct($data);
    }
    /**
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if ($this->checkType($this->getType(), $value) === \false) {
            throw new InvalidArgumentException('Value must be of type ' . $this->getType() . '; value is ' . $this->toolValueToString($value));
        }
        $this->data[] = $value;
    }
    public function add($element) : bool
    {
        $this[] = $element;
        return \true;
    }
    public function element()
    {
        $element = $this->peek();
        if ($element === null) {
            throw new NoSuchElementException('Can\'t return element from Queue. Queue is empty.');
        }
        return $element;
    }
    public function offer($element) : bool
    {
        try {
            return $this->add($element);
        } catch (InvalidArgumentException $e) {
            return \false;
        }
    }
    public function peek()
    {
        if ($this->count() === 0) {
            return null;
        }
        return $this[$this->index];
    }
    public function poll()
    {
        if ($this->count() === 0) {
            return null;
        }
        $head = $this[$this->index];
        unset($this[$this->index]);
        $this->index++;
        return $head;
    }
    public function remove()
    {
        $head = $this->poll();
        if ($head === null) {
            throw new NoSuchElementException('Can\'t return element from Queue. Queue is empty.');
        }
        return $head;
    }
    public function getType() : string
    {
        return $this->queueType;
    }
}
