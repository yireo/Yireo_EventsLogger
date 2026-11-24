<?php declare(strict_types=1);

namespace Yireo\EventsLogger\Test\Unit\Plugin;

use Magento\Framework\Event\Manager;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Yireo\EventsLogger\Config\Config;
use Yireo\EventsLogger\Plugin\LogEvent;

class LogEventTest extends TestCase
{
    public function testDoesNotLogWhenDisabled(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->never())->method('debug');

        $this->getPlugin($logger, $this->getConfig(false))->beforeDispatch($this->getManager(), 'foo_bar');
    }

    public function testLogsEventNameWhenEnabled(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('debug')->with('foo_bar');

        $this->getPlugin($logger, $this->getConfig(true))
            ->beforeDispatch($this->getManager(), 'foo_bar', ['key' => 'value']);
    }

    public function testStripsControlCharactersFromEventName(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('debug')->with('foobarforged');

        $this->getPlugin($logger, $this->getConfig(true))
            ->beforeDispatch($this->getManager(), "foo\r\nbar\x1b\x00forged");
    }

    #[DataProvider('getNonStringEventNames')]
    public function testIgnoresNonStringEventNames(mixed $eventName): void
    {
        $config = $this->createMock(Config::class);
        $config->expects($this->never())->method('isEnabled');
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->never())->method('debug');

        $this->getPlugin($logger, $config)->beforeDispatch($this->getManager(), $eventName);
    }

    public static function getNonStringEventNames(): array
    {
        return [[null], [42], [['foo']]];
    }

    public function testDoesNotThrowWhenLoggerFails(): void
    {
        $logger = $this->createStub(LoggerInterface::class);
        $logger->method('debug')->willThrowException(new RuntimeException('Disk full'));

        $this->getPlugin($logger, $this->getConfig(true))->beforeDispatch($this->getManager(), 'foo_bar');

        $this->addToAssertionCount(1);
    }

    public function testDoesNotThrowWhenConfigFails(): void
    {
        $config = $this->createStub(Config::class);
        $config->method('isEnabled')->willThrowException(new RuntimeException('No database'));
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->never())->method('debug');

        $this->getPlugin($logger, $config)->beforeDispatch($this->getManager(), 'foo_bar');
    }

    public function testLogsAgainAfterFailure(): void
    {
        $loggedEvents = [];
        $logger = $this->createStub(LoggerInterface::class);
        $logger->method('debug')->willReturnCallback(
            function (string $message) use (&$loggedEvents) {
                $loggedEvents[] = $message;
                if ($message === 'first') {
                    throw new RuntimeException('Disk full');
                }
            }
        );

        $plugin = $this->getPlugin($logger, $this->getConfig(true));
        $plugin->beforeDispatch($this->getManager(), 'first');
        $plugin->beforeDispatch($this->getManager(), 'second');

        $this->assertSame(['first', 'second'], $loggedEvents);
    }

    public function testDoesNotRecurseWhileLogging(): void
    {
        $loggedEvents = [];
        $manager = $this->getManager();
        $logger = $this->createStub(LoggerInterface::class);
        $plugin = $this->getPlugin($logger, $this->getConfig(true));
        $logger->method('debug')->willReturnCallback(
            function (string $message) use ($plugin, $manager, &$loggedEvents) {
                $loggedEvents[] = $message;
                $plugin->beforeDispatch($manager, 'nested_event');
            }
        );

        $plugin->beforeDispatch($manager, 'outer_event');

        $this->assertSame(['outer_event'], $loggedEvents);
    }

    private function getPlugin(LoggerInterface $logger, Config $config): LogEvent
    {
        return new LogEvent($logger, $config);
    }

    private function getConfig(bool $enabled): Config
    {
        $config = $this->createStub(Config::class);
        $config->method('isEnabled')->willReturn($enabled);

        return $config;
    }

    private function getManager(): Manager
    {
        return $this->createStub(Manager::class);
    }
}
