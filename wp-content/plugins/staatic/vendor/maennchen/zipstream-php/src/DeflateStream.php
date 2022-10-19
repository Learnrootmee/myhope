<?php

declare (strict_types=1);
namespace Staatic\Vendor\ZipStream;

use Staatic\Vendor\ZipStream\Option\File;
class DeflateStream extends Stream
{
    protected $filter;
    protected $options;
    /**
     * @return void
     */
    public function rewind()
    {
        if ($this->filter) {
            $this->removeDeflateFilter();
            $this->seek(0);
            $this->addDeflateFilter($this->options);
        } else {
            \rewind($this->stream);
        }
    }
    /**
     * @return void
     */
    public function removeDeflateFilter()
    {
        if (!$this->filter) {
            return;
        }
        \stream_filter_remove($this->filter);
        $this->filter = null;
    }
    /**
     * @param File $options
     * @return void
     */
    public function addDeflateFilter($options)
    {
        $this->options = $options;
        $optionsArr = ['comment' => $options->getComment(), 'method' => $options->getMethod(), 'deflateLevel' => $options->getDeflateLevel(), 'time' => $options->getTime()];
        $this->filter = \stream_filter_append($this->stream, 'zlib.deflate', \STREAM_FILTER_READ, $optionsArr);
    }
}
