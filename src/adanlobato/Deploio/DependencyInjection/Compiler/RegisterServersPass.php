<?php

namespace adanlobato\Deploio\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use adanlobato\Deploio\Process\RsyncProcess\Server;

class RegisterServersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasParameter('deployer.servers')) {
            return;
        }

        $servers = $container->getParameter('deployer.servers');

        // Register configured servers
        $deployerDefinition = $container->getDefinition('deployer');
        foreach ($servers as $serverName => $options) {
            $deployerDefinition->addMethodCall('addServer', array(
                $serverName,
                new Server($options),
            ));
        }
    }
}