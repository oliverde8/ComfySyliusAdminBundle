<?php
/**
 * @author    oliverde8<oliverde8@gmail.com>
 */
namespace oliverde8\ComfySyliusAdminBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class oliverde8ComfySyliusAdminBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
    }
}
