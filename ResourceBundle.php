<?php

declare(strict_types=1);

namespace Owl\Bundle\ResourceBundle;

use Owl\Bundle\ResourceBundle\DependencyInjection\Compiler\ActionsResourcePass;
use Owl\Bundle\ResourceBundle\DependencyInjection\Compiler\RegisterResourceFilterPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class ResourceBundle extends Bundle
{
    /**
     * @inheritdoc
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ActionsResourcePass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, -1);
        $container->addCompilerPass(new RegisterResourceFilterPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, -1);
    }
}
