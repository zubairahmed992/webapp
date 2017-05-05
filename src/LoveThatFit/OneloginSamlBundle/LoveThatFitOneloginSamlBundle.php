<?php

namespace LoveThatFit\OneloginSamlBundle;

use LoveThatFit\OneloginSamlBundle\DependencyInjection\Compiler\SecurityCompilerPass;
use LoveThatFit\OneloginSamlBundle\DependencyInjection\Security\Factory\SamlFactory;
use LoveThatFit\OneloginSamlBundle\DependencyInjection\Security\Factory\SamlUserProviderFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class LoveThatFitOneloginSamlBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new SamlFactory());
        $extension->addUserProviderFactory(new SamlUserProviderFactory());

        $container->addCompilerPass(new SecurityCompilerPass());
    }
}
