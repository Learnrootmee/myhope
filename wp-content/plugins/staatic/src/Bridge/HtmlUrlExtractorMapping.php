<?php

declare(strict_types=1);

namespace Staatic\WordPress\Bridge;

use Staatic\Crawler\UrlExtractor\Mapping\HtmlUrlExtractorMapping as BaseMapping;

class HtmlUrlExtractorMapping extends BaseMapping
{
    public function __construct()
    {
        parent::__construct();
        $this->mapping['img'][] = 'data-wpfc-original-srcset';
        $this->srcsetAttributes[] = 'data-wpfc-original-srcset';
    }
}
