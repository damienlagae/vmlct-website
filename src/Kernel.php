<?php

declare(strict_types=1);

namespace App;

use App\Shared\Security\DependencyInjection\PermissionEnumDiscoveryPass;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    protected function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new PermissionEnumDiscoveryPass());
    }
}
