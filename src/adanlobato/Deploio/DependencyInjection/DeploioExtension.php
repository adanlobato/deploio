<?php

namespace adanlobato\Deploio\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\Config\Definition\Processor;

class DeploioExtension implements ExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $processor = new Processor();
        $config = $processor->processConfiguration($configuration, $configs);

        $container->setParameter('deployer.servers', $config['servers']);
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