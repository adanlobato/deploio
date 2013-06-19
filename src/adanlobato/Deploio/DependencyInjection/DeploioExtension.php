<?php

namespace adanlobato\Deploio\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use adanlobato\Deploio\Process\RsyncProcess\Server;

class DeploioExtension implements ExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $processor = new Processor();
        $config = $processor->processConfiguration($configuration, $configs);

        // Register configured servers
        $deployerDefinition = $container->getDefinition('deployer');
        foreach ($config['servers'] as $serverName => $options) {
            $deployerDefinition->addMethodCall('addServer', array(
                $serverName,
                new Server($options),
            ));
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getAlias()
    {
        return 'deploio';
    }

    /**
     * {@inheritDoc}
     */
    public function getNamespace()
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function getXsdValidationBasePath()
    {
        return false;
    }
}