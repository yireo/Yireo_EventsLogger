<?php declare(strict_types=1);

namespace Yireo\EventsLogger\Test\Integration;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\Manager;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Interception\PluginListInterface;
use Magento\TestFramework\Fixture\Config as ConfigFixture;
use Magento\TestFramework\Helper\Bootstrap;
use Monolog\Handler\StreamHandler;
use PHPUnit\Framework\TestCase;
use Yireo\EventsLogger\Plugin\LogEvent;

class LogEventTest extends TestCase
{
    public function testPluginIsRegistered(): void
    {
        $pluginList = Bootstrap::getObjectManager()->get(PluginListInterface::class);
        $pluginInfo = $pluginList->get(Manager::class, []);

        $this->assertArrayHasKey('logEvent', $pluginInfo);
        $this->assertSame(LogEvent::class, $pluginInfo['logEvent']['instance']);
    }

    public function testIsDisabledByDefault(): void
    {
        $scopeConfig = Bootstrap::getObjectManager()->get(ScopeConfigInterface::class);

        $this->assertFalse($scopeConfig->isSetFlag('yireo_events_logger/general/enabled'));
    }

    #[ConfigFixture('yireo_events_logger/general/enabled', '1', 'default')]
    public function testLogsEventWhenEnabled(): void
    {
        $eventName = $this->dispatchUniqueEvent();

        $this->assertStringContainsString($eventName, $this->getLogContents());
    }

    #[ConfigFixture('yireo_events_logger/general/enabled', '0', 'default')]
    public function testDoesNotLogEventWhenDisabled(): void
    {
        $eventName = $this->dispatchUniqueEvent();

        $this->assertStringNotContainsString($eventName, $this->getLogContents());
    }

    private function dispatchUniqueEvent(): string
    {
        $eventName = 'yireo_events_logger_test_' . bin2hex(random_bytes(8));
        Bootstrap::getObjectManager()->get(ManagerInterface::class)->dispatch($eventName);

        return $eventName;
    }

    private function getLogContents(): string
    {
        /** @var StreamHandler $handler */
        $handler = Bootstrap::getObjectManager()->get('Yireo\EventsLogger\Logger\Handler');
        $file = (string)$handler->getUrl();

        return is_file($file) ? (string)file_get_contents($file) : '';
    }
}
