<?php

namespace Staatic\Vendor\Symfony\Component\Config\Builder;

class Property
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $originalName;
    /**
     * @var bool
     */
    private $array = \false;
    /**
     * @var bool
     */
    private $scalarsAllowed = \false;
    /**
     * @var string|null
     */
    private $type;
    /**
     * @var string|null
     */
    private $content;
    public function __construct(string $originalName, string $name)
    {
        $this->name = $name;
        $this->originalName = $originalName;
    }
    public function getName() : string
    {
        return $this->name;
    }
    public function getOriginalName() : string
    {
        return $this->originalName;
    }
    /**
     * @param string $type
     * @return void
     */
    public function setType($type)
    {
        $this->array = \false;
        $this->type = $type;
        if ('|scalar' === \substr($type, -7)) {
            $this->scalarsAllowed = \true;
            $this->type = $type = \substr($type, 0, -7);
        }
        if ('[]' === \substr($type, -2)) {
            $this->array = \true;
            $this->type = \substr($type, 0, -2);
        }
    }
    /**
     * @return string|null
     */
    public function getType()
    {
        return $this->type;
    }
    /**
     * @return string|null
     */
    public function getContent()
    {
        return $this->content;
    }
    /**
     * @param string $content
     * @return void
     */
    public function setContent($content)
    {
        $this->content = $content;
    }
    public function isArray() : bool
    {
        return $this->array;
    }
    public function areScalarsAllowed() : bool
    {
        return $this->scalarsAllowed;
    }
}
