<?php

declare (strict_types=1);
namespace Staatic\Vendor\GuzzleHttp\Psr7;

use AllowDynamicProperties;
use Staatic\Vendor\Psr\Http\Message\StreamInterface;
#[AllowDynamicProperties]
final class LazyOpenStream implements StreamInterface
{
    use StreamDecoratorTrait;
    private $filename;
    private $mode;
    public function __construct(string $filename, string $mode)
    {
        $this->filename = $filename;
        $this->mode = $mode;
    }
    protected function createStream() : StreamInterface
    {
        return Utils::streamFor(Utils::tryFopen($this->filename, $this->mode));
    }
}
