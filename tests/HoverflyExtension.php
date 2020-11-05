<?php declare(strict_types=1);

namespace ShipEngine\Tests;

use PHPUnit\Runner\BeforeFirstTestHook;
use PHPUnit\Runner\AfterLastTestHook;

final class HoverflyExtension implements BeforeFirstTestHook, AfterLastTestHook
{
    public function executeBeforeFirstTest(): void
    {
        exec('hoverfly -webserver -response-body-files-path simengine > /dev/null &');
        sleep(1);
    }

    public function executeAfterLastTest(): void
    {
        exec('hoverctl stop');
    }
}
