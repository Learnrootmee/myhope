<?php

namespace Staatic\Vendor\Symfony\Component\Config\Definition\Exception;

class InvalidConfigurationException extends Exception
{
    /**
     * @var string|null
     */
    private $path;
    /**
     * @var bool
     */
    private $containsHints = \false;
    /**
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }
    /**
     * @return string|null
     */
    public function getPath()
    {
        return $this->path;
    }
    /**
     * @param string $hint
     */
    public function addHint($hint)
    {
        if (!$this->containsHints) {
            $this->message .= "\nHint: " . $hint;
            $this->containsHints = \true;
        } else {
            $this->message .= ', ' . $hint;
        }
    }
}
