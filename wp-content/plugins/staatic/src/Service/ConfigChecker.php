<?php

declare(strict_types=1);

namespace Staatic\WordPress\Service;

use Exception;
use Staatic\WordPress\Factory\HttpClientFactory;

final class ConfigChecker
{
    /**
     * @var mixed[]
     */
    private $issues;

    /**
     * @var HttpClientFactory
     */
    private $httpClientFactory;

    public function __construct(HttpClientFactory $httpClientFactory)
    {
        $this->httpClientFactory = $httpClientFactory;
    }

    public function findIssues() : array
    {
        $this->issues = [];
        $this->testPermalinkStructure();
        $this->testWritableWorkDirectory();
        $this->testSelfConnect();
        $this->issues = \apply_filters('staatic-config-checker-issues', $this->issues);

        return $this->issues;
    }

    /**
     * @return void
     */
    private function testPermalinkStructure()
    {
        if (!\get_option('permalink_structure')) {
            $this->issues[] = \sprintf(
                /* translators: %s: Link to Permalink Settings. */
                \__('Permalink structure is not configured, see <a href="%s">Permalink Settings</a>.', 'staatic'),
                \admin_url('options-permalink.php')
            );
        }
    }

    /**
     * @return void
     */
    private function testWritableWorkDirectory()
    {
        $workDirectory = \get_option('staatic_work_directory');
        if (\is_dir($workDirectory)) {
            if (!\is_writable($workDirectory)) {
                $this->issues[] = \sprintf(
                    /* translators: %s: Work directory. */
                    \__('Work directory is not writable: "%s".', 'staatic'),
                    $workDirectory
                );
            }
        } elseif (!\is_writable(\dirname($workDirectory))) {
            $this->issues[] = \sprintf(
                /* translators: %s: Work directory. */
                \__('Work directory does not exist and can\'t be created: "%s".', 'staatic'),
                $workDirectory
            );
        }
    }

    /**
     * @return void
     */
    private function testSelfConnect()
    {
        $httpClient = $this->httpClientFactory->createInternalClient();
        $testUrl = \plugin_dir_url(\STAATIC_FILE) . 'readme.txt';

        try {
            $response = $httpClient->request('GET', $testUrl);
        } catch (Exception $e) {
            $this->issues[] = \sprintf(
                /* translators: %s: The error message. */
                \__('Staatic is unable to access your WordPress site<br><em>%s</em>.', 'staatic'),
                \esc_html($e->getMessage())
            );

            return;
        }
        if (\strstr($responseExtract = $response->getBody()->read(64), '===') === \false) {
            $this->issues[] = \sprintf(
                /* translators: %s: The error message. */
                \__('Staatic is unable to access your WordPress site<br><em>%s</em>.', 'staatic'),
                \sprintf('Unexpected response: ' . \esc_html($responseExtract))
            );
        }
    }
}
