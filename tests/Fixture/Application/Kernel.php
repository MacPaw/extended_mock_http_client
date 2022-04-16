<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\Tests\Fixture\Application;

use ExtendedMockHttpClient\ExtendedMockHttpClientBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    /**
     * This method necessary for old symfony versions
     */
    protected function configureRoutes(RouteCollectionBuilder $routes): void
    {
    }

    protected function configureContainer(ContainerBuilder $c, LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/config/packages/framework.yaml');
        $loader->load(__DIR__ . '/config/services.yaml');
    }

    public function registerBundles(): iterable
    {
        return [
            new FrameworkBundle(),
            new ExtendedMockHttpClientBundle(),
        ];
    }
}
