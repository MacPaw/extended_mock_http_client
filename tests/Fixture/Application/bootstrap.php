<?php

declare(strict_types=1);

use ExtendedMockHttpClient\Tests\Fixture\Application\Kernel;

require dirname(__DIR__) . '/../../vendor/autoload.php';

$kernel = new Kernel('test', true);
$kernel->boot();
