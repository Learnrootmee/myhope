<?php

declare(strict_types=1);

namespace Staatic\WordPress\SettingGroup;

final class SettingGroup implements SettingGroupInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $label;

    /**
     * @var int
     */
    private $position;

    private $descriptionCallback = null;

    public function __construct(string $name, string $label, int $position, $descriptionCallback = null)
    {
        $this->name = $name;
        $this->label = $label;
        $this->position = $position;
        $this->descriptionCallback = $descriptionCallback;
    }

    public function name() : string
    {
        return $this->name;
    }

    public function label() : string
    {
        return $this->label;
    }

    public function position() : int
    {
        return $this->position;
    }

    public function descriptionCallback()
    {
        return $this->descriptionCallback;
    }

    /**
     * @return void
     */
    public function render()
    {
        if ($this->descriptionCallback) {
            echo ($this->descriptionCallback)();
        }
    }

    /**
     * @param string $label
     * @return void
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * @param int $position
     * @return void
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * @return void
     */
    public function setDescriptionCallback($descriptionCallback)
    {
        $this->descriptionCallback = $descriptionCallback;
    }
}
