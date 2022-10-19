<?php

declare(strict_types=1);

namespace Staatic\WordPress\SettingGroup;

interface SettingGroupInterface
{
    public function name() : string;

    public function label() : string;

    public function position() : int;

    public function descriptionCallback();

    /**
     * @return void
     */
    public function render();

    /**
     * @param string $label
     * @return void
     */
    public function setLabel($label);

    /**
     * @param int $position
     * @return void
     */
    public function setPosition($position);

    /**
     * @return void
     */
    public function setDescriptionCallback($descriptionCallback);
}
