<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\Tests\Fixture\Application;

use ExtendedMockHttpClient\ExtendedMockHttpClientBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function registerBundles(): iterable
    {
        return [
            new FrameworkBundle(),
            new ExtendedMockHttpClientBundle(),
        ];
    }

    private function getConfigDir(): string
    {
        return $this->getProjectDir() . '/tests/Fixture/Application/config';
    }
}
