<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Admin\Page\PublicationLogs;

use Staatic\Vendor\Psr\Log\LogLevel;
use Staatic\WordPress\ListTable\AbstractListTable;
use Staatic\WordPress\ListTable\Column\DateColumn;
use Staatic\WordPress\ListTable\Column\TextColumn;
use Staatic\WordPress\ListTable\Decorator\CallbackDecorator;
use Staatic\WordPress\ListTable\View\View;
use Staatic\WordPress\Logging\LogEntry;
use Staatic\WordPress\Logging\LogEntryRepository;
use Staatic\WordPress\Service\Formatter;

class PublicationLogsTable extends AbstractListTable
{
    /** @var string */
    const NAME = 'log_entry_list_table';

    /**
     * @var Formatter
     */
    private $formatter;

    /**
     * @var LogEntryRepository
     */
    private $repository;

    public function __construct(Formatter $formatter, LogEntryRepository $repository)
    {
        $this->formatter = $formatter;
        $this->repository = $repository;
        parent::__construct('id', ['log_date', 'ASC']);
    }

    /**
     * @param string $baseUrl
     * @param mixed[] $arguments
     * @return void
     */
    public function initialize($baseUrl, $arguments = [])
    {
        parent::initialize($baseUrl, $arguments);
        $this->setupColumns();
        $this->setupViews();
    }

    /**
     * @return void
     */
    private function setupColumns()
    {
        $this->addColumns([new TextColumn('message', \__('Message', 'staatic'), [
            'decorators' => [new CallbackDecorator(function (string $input, LogEntry $item) {
                        return $this->logMessageOutput($input, $item);
                    })]
        ]), new TextColumn('log_level', \__('Level', 'staatic'), [
            'getter' => 'level'
        ]), new DateColumn($this->formatter, 'log_date', \__('Date', 'staatic'), [
            'getter' => 'date'
        ])]);
    }

    private function logMessageOutput(string $input, LogEntry $entry) : string
    {
        if ($entry->level() !== LogLevel::DEBUG) {
            return $input;
        }
        $context = $entry->context();
        $details = [];
        if (isset($context['sourceFile'], $context['sourceLine'])) {
            $details[] = \esc_html("Source: {$context['sourceFile']}:{$context['sourceLine']}");
        }
        if (isset($context['sourceClass'], $context['sourceFunction'])) {
            $details[] = \esc_html("Function: {$context['sourceClass']}::{$context['sourceFunction']}");
        }
        if (isset($context['memory'])) {
            $details[] = \esc_html(\sprintf('Memory: %s', \size_format($context['memory'])));
        }
        if (!empty($details)) {
            $input .= \sprintf('<br><span class="staatic-log-debug">%s</span>', \implode('<br>', $details));
        }

        return $input;
    }

    /**
     * @return void
     */
    public function setupViews()
    {
        $logLevels = [
            LogLevel::DEBUG => \__('Debug', 'staatic'),
            LogLevel::INFO => \__('Informational', 'staatic'),
            LogLevel::NOTICE => \__('Notice', 'staatic'),
            LogLevel::WARNING => \__('Warning', 'staatic'),
            LogLevel::ERROR => \__('Error', 'staatic'),
            LogLevel::CRITICAL => \__('Critical', 'staatic')
        ];
        foreach ($logLevels as $name => $label) {
            $this->addView(new View($name, $label));
        }
    }

    public function views() : array
    {
        $views = parent::views();
        if (!$this->repository->countWhereMatching($this->arguments['publicationId'], LogLevel::DEBUG)) {
            unset($views[LogLevel::DEBUG]);
        }

        return $views;
    }

    /**
     * @param string|null $view
     * @param string|null $query
     * @param int $limit
     * @param int $offset
     * @param string|null $orderBy
     * @param string|null $direction
     */
    public function items($view, $query, $limit, $offset, $orderBy, $direction) : array
    {
        return $this->repository->findWhereMatching(
            $this->arguments['publicationId'],
            $view,
            $query,
            $limit,
            $offset,
            $orderBy,
            $direction
        );
    }

    /**
     * @param string|null $view
     * @param string|null $query
     */
    public function numItems($view, $query) : int
    {
        return $this->repository->countWhereMatching($this->arguments['publicationId'], $view, $query);
    }

    /**
     * @return mixed[]|null
     */
    public function numItemsPerView()
    {
        return $this->repository->getLogEntriesPerLevel($this->arguments['publicationId']);
    }
}
