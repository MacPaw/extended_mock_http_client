<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\Tests\Functional;

use ExtendedMockHttpClient\Tests\Fixture\Application\Kernel;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class AbstractKernelTestCase extends KernelTestCase
{
    protected static function getKernelClass(): string
    {
        return Kernel::class;
    }

    protected static function getContainerService(string $serviceClassOrId): ?object
    {
        if (!static::$booted) {
            static::bootKernel();
        }

        return self::$kernel->getContainer()->get($serviceClassOrId);
    }
}
