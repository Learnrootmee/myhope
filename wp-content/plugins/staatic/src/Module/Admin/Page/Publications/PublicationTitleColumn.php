<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Admin\Page\Publications;

use Staatic\WordPress\ListTable\Column\AbstractColumn;
use Staatic\WordPress\Publication\Publication;
use Staatic\WordPress\Service\Formatter;

final class PublicationTitleColumn extends AbstractColumn
{
    /**
     * @var string|null
     */
    private $activePublicationId;

    /**
     * @var Formatter
     */
    private $formatter;

    public function __construct(Formatter $formatter, string $name, string $label, array $arguments = [])
    {
        $this->formatter = $formatter;
        parent::__construct($name, $label, $arguments);
        $this->activePublicationId = $arguments['activePublicationId'] ?? null;
    }

    /**
     * @param Publication $publication
     * @return void
     */
    public function render($publication)
    {
        $result = $this->formatter->date($publication->dateCreated());
        if ($publication->isPreview()) {
            $result = \sprintf('%s (<em>%s</em>)', $result, \__('Preview', 'staatic'));
        }
        if ($publication->id() === $this->activePublicationId) {
            $result = \sprintf('<strong>%s</strong> (<em>%s</em>)', $result, \__('Active Publication', 'staatic'));
        }
        echo $this->applyDecorators($result, $publication);
    }

    /**
     * @return string|null
     */
    public function defaultSortColumn()
    {
        return 'date_created';
    }

    public function defaultSortDirection() : string
    {
        return 'DESC';
    }
}
