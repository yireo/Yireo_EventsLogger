<?php declare(strict_types=1);

namespace Yireo\EventsLogger\Plugin;

use Magento\Framework\Event\Manager;
use Psr\Log\LoggerInterface;
use Throwable;
use Yireo\EventsLogger\Config\Config;

class LogEvent
{
    private bool $isLogging = false;

    public function __construct(
        private LoggerInterface $logger,
        private Config $config
    ) {
    }

    /**
     * @param Manager $subject
     * @param mixed $eventName
     * @param array $data
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeDispatch(Manager $subject, $eventName, array $data = []): void
    {
        if ($this->isLogging || !is_string($eventName)) {
            return;
        }

        $this->isLogging = true;

        try {
            if ($this->config->isEnabled()) {
                $this->logger->debug((string)preg_replace('/[^\x20-\x7E]/', '', $eventName));
            }
        } catch (Throwable) {
            return;
        } finally {
            $this->isLogging = false;
        }
    }
}
