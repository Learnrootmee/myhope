<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Uuid\Generator;

use Staatic\Vendor\Ramsey\Uuid\Converter\TimeConverterInterface;
use Staatic\Vendor\Ramsey\Uuid\Provider\TimeProviderInterface;
use function hex2bin;
class UnixTimeGenerator implements TimeGeneratorInterface
{
    /**
     * @var TimeConverterInterface
     */
    private $timeConverter;
    /**
     * @var \Staatic\Vendor\Ramsey\Uuid\Provider\TimeProviderInterface
     */
    private $timeProvider;
    /**
     * @var RandomGeneratorInterface
     */
    private $randomGenerator;
    public function __construct(TimeConverterInterface $timeConverter, TimeProviderInterface $timeProvider, RandomGeneratorInterface $randomGenerator)
    {
        $this->timeConverter = $timeConverter;
        $this->timeProvider = $timeProvider;
        $this->randomGenerator = $randomGenerator;
    }
    /**
     * @param int|null $clockSeq
     */
    public function generate($node = null, $clockSeq = null) : string
    {
        $random = $this->randomGenerator->generate(10);
        $time = $this->timeProvider->getTime();
        $unixTimeHex = $this->timeConverter->calculateTime($time->getSeconds()->toString(), $time->getMicroseconds()->toString());
        return hex2bin($unixTimeHex->toString()) . $random;
    }
}
