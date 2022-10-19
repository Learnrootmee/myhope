<?php

declare(strict_types=1);

namespace Staatic\WordPress\Setting\Advanced;

use Staatic\WordPress\Setting\AbstractSetting;

final class SslVerifyBehaviorSetting extends AbstractSetting
{
    const VALUE_ENABLED = 'enabled';

    const VALUE_DISABLED = 'disabled';

    const VALUE_PATH = 'path';

    public function name() : string
    {
        return 'staatic_ssl_verify_behavior';
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
        return \__('Verification', 'staatic');
    }

    /**
     * @return string|null
     */
    public function description()
    {
        return \__('Set to "Enabled" to enable SSL certificate verification and use the default CA bundle provided by operating system.<br>Set to "Disabled" to disable certificate verification (this is insecure!).<br>Set to "Enabled using custom certificate" to provide the path to a CA bundle to enable verification using a custom certificate.', 'staatic');
    }

    public function defaultValue()
    {
        return self::VALUE_ENABLED;
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
            self::VALUE_ENABLED => \__('Enabled', 'staatic'),
            self::VALUE_DISABLED => \__('Disabled', 'staatic'),
            self::VALUE_PATH => \__('Enabled using custom certificate', 'staatic')
        ];
    }
}
