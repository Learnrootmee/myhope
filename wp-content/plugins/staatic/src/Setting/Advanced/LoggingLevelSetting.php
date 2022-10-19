<?php

declare(strict_types=1);

namespace Staatic\WordPress\Setting\Advanced;

use Staatic\WordPress\Setting\AbstractSetting;

final class LoggingLevelSetting extends AbstractSetting
{
    const VALUE_DISABLED = 'disabled';

    const VALUE_MINIMAL = 'minimal';

    const VALUE_DETAILED = 'detailed';

    const VALUE_EXTENSIVE = 'extensive';

    public function name() : string
    {
        return 'staatic_logging_level';
    }

    public function type() : string
    {
        return self::TYPE_STRING;
    }

    protected function template() : string
    {
        return 'select';
    }

    public function label() : string
    {
        return \__('Logging', 'staatic');
    }

    /**
     * @return string|null
     */
    public function description()
    {
        return \__('Increasing the level of logging might help with troubleshooting, but impacts publication speed.', 'staatic');
    }

    public function defaultValue()
    {
        return self::VALUE_MINIMAL;
    }

    /**
     * @param mixed[] $attributes
     * @return void
     */
    public function render($attributes = [])
    {
        parent::render(\array_merge([
            'selectOptions' => $this->selectOptions()
        ], $attributes));
    }

    private function selectOptions() : array
    {
        return [
            self::VALUE_EXTENSIVE => \__('Extensive logging', 'staatic'),
            self::VALUE_DETAILED => \__('Detailed logging', 'staatic'),
            self::VALUE_MINIMAL => \__('Minimal logging', 'staatic'),
            self::VALUE_DISABLED => \__('No logging', 'staatic')
        ];
    }
}
