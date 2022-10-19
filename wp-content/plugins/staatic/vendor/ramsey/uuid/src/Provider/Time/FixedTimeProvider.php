<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Uuid\Provider\Time;

use Staatic\Vendor\Ramsey\Uuid\Provider\TimeProviderInterface;
use Staatic\Vendor\Ramsey\Uuid\Type\Integer as IntegerObject;
use Staatic\Vendor\Ramsey\Uuid\Type\Time;
class FixedTimeProvider implements TimeProviderInterface
{
    /**
     * @var Time
     */
    private $time;
    public function __construct(Time $time)
    {
        $this->time = $time;
    }
    /**
     * @return void
     */
    public function setUsec($value)
    {
        $this->time = new Time($this->time->getSeconds(), $value);
    }
    /**
     * @return void
     */
    public function setSec($value)
    {
        $this->time = new Time($value, $this->time->getMicroseconds());
    }
    public function getTime() : Time
    {
        return $this->time;
    }
}
