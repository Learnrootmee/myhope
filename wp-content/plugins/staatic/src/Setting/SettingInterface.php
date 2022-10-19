<?php

declare(strict_types=1);

namespace Staatic\WordPress\Setting;

interface SettingInterface
{
    const TYPE_BOOLEAN = 'boolean';

    const TYPE_INTEGER = 'integer';

    const TYPE_NUMBER = 'number';

    const TYPE_STRING = 'string';

    const TYPE_ARRAY = 'array';

    const TYPE_OBJECT = 'object';

    const TYPE_COMPOSED = 'composed';

    //!
    public function name() : string;

    public function type() : string;

    public function label() : string;

    /**
     * @return string|null
     */
    public function extendedLabel();

    /**
     * @return string|null
     */
    public function description();

    public function isEnabled() : bool;

    public function value();

    public function defaultValue();

    public function sanitizeValue($value);

    /**
     * @param mixed[] $attributes
     * @return void
     */
    public function render($attributes = []);
}
