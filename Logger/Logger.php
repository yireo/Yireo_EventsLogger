<?php declare(strict_types=1);

namespace Yireo\EventsLogger\Logger;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;

class Logger
{
    public function __construct(
        private DirectoryList $directoryList,
        private Filesystem $filesystem,
    ) {
    }

    public function log(string $eventName): void
    {
        $logFolder = $this->directoryList->getPath(DirectoryList::LOG);
        $filename = $logFolder.'/events.log';

        $directoryWrite = $this->filesystem->getDirectoryWrite(DirectoryList::LOG);
        $writer = $directoryWrite->openFile($filename, 'a+');
        $writer->write($eventName."\n");
    }
}
