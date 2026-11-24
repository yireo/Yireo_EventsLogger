<?php declare(strict_types=1);

namespace Yireo\EventsLogger\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;

class Config
{
    public function __construct(
        private ScopeConfigInterface $scopeConfig
    ) {
    }

    public function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag('yireo_events_logger/general/enabled');
    }
}
