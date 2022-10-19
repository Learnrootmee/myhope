<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Deployer\S3Deployer;

use Staatic\WordPress\Setting\AbstractSetting;

final class S3RegionSetting extends AbstractSetting
{
    public function name() : string
    {
        return 'staatic_aws_s3_region';
    }

    public function type() : string
    {
        return self::TYPE_STRING;
    }

    protected function template() : string
    {
        return 'string';
    }

    public function label() : string
    {
        return \__('Region', 'staatic');
    }

    /**
     * @return string|null
     */
    public function description()
    {
        return \__('The name of the region the S3 bucket resides in.', 'staatic');
    }

    public function defaultValue()
    {
        return 'us-east-1';
    }

    /**
     * @param mixed[] $attributes
     * @return void
     */
    public function render($attributes = [])
    {
        parent::render(\array_merge([
            'options' => $this->options()
        ], $attributes));
    }

    private function options() : array
    {
        $regions = [
            'us-east-2' => 'US East (Ohio)',
            'us-east-1' => 'US East (N. Virginia)',
            'us-west-1' => 'US West (N. California)',
            'us-west-2' => 'US West (Oregon)',
            'af-south-1' => 'Africa (Cape Town)',
            'ap-east-1' => 'Asia Pacific (Hong Kong)',
            'ap-south-1' => 'Asia Pacific (Mumbai)',
            'ap-northeast-1' => 'Asia Pacific (Tokyo)',
            'ap-northeast-2' => 'Asia Pacific (Seoul)',
            'ap-northeast-3' => 'Asia Pacific (Osaka-Local)',
            'ap-southeast-1' => 'Asia Pacific (Singapore)',
            'ap-southeast-2' => 'Asia Pacific (Sydney)',
            'ap-southeast-3' => 'Asia Pacific (Jakarta)',
            'ca-central-1' => 'Canada (Central)',
            'eu-central-1' => 'Europe (Frankfurt)',
            'eu-west-1' => 'Europe (Ireland)',
            'eu-west-2' => 'Europe (London)',
            'eu-south-1' => 'Europe (Milan)',
            'eu-west-3' => 'Europe (Paris)',
            'eu-north-1' => 'Europe (Stockholm)',
            'me-south-1' => 'Middle East (Bahrain)',
            'sa-east-1' => 'South America (São Paulo)',
            'cn-north-1' => 'China (Beijing)',
            'cn-northwest-1' => 'China (Ningxia)'
        ];

        return \array_map(function ($label, $region) {
            return \sprintf('%s > %s', $label, $region);
        }, $regions, \array_keys($regions));
    }
}
