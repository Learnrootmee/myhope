<?php

declare(strict_types=1);

namespace Staatic\WordPress\Publication\Task;

use Staatic\WordPress\Factory\StaticGeneratorFactory;
use Staatic\WordPress\Publication\Publication;

final class FinishCrawlerTask implements TaskInterface
{
    /**
     * @var StaticGeneratorFactory
     */
    private $factory;

    public function __construct(StaticGeneratorFactory $factory)
    {
        $this->factory = $factory;
    }

    public function name() : string
    {
        return 'finish_crawler';
    }

    public function description() : string
    {
        return \__('Finishing crawler', 'staatic');
    }

    /**
     * @param Publication $publication
     */
    public function supports($publication) : bool
    {
        if ($publication->metadataByKey('sourcePublicationId')) {
            // Skip in case an existing publication was this publication's source.
            return \false;
        }

        return \true;
    }

    /**
     * @param Publication $publication
     */
    public function execute($publication) : bool
    {
        $staticGenerator = ($this->factory)($publication);
        $staticGenerator->finish($publication->build());

        return \true;
    }
}
