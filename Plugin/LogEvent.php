<?php declare(strict_types=1);

namespace Yireo\EventsLogger\Plugin;

use Magento\Framework\Event\Manager;
use Yireo\EventsLogger\Logger\Logger;

class LogEvent
{
    public function __construct(
        private Logger $logger
    ){
    }

    /**
     * @param Manager $subject
     * @param string $eventName
     * @param array $data
     * @return array
     */
    public function beforeDispatch(Manager $subject, $eventName, array $data = []): array
    {
        $this->logger->log($eventName);

        return [$eventName, $data];
    }
}
