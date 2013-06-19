<?php

namespace adanlobato\Deploio\Console;

use Symfony\Component\Console\Application as BaseApplication;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

use Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface;

use adanlobato\Deploio\Console\Command;
use adanlobato\Deploio\DependencyInjection\DeploioExtension;

class Application extends BaseApplication
{
    /**
     * @var ContainerBuilder
     */
    protected $container;

    public function __construct()
    {
        parent::__construct('Deploio', '1.0.0');

        $this->buildContainer();

        $this->add(new Command\DeployCommand());
    }

    public function run(InputInterface $input = null, OutputInterface $output = null)
    {
        parent::run(
            $this->container->get('console.input'),
            $this->container->get('console.output')
        );
    }

    /**
     * @return ContainerBuilder
     */
    public function getContainer()
    {
        return $this->container;
    }

    private function buildContainer()
    {
        $this->container = new ContainerBuilder();

        $loader = new YamlFileLoader($this->container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $this->container->register('console.input', 'Symfony\Component\Console\Input\ArgvInput');
        $this->container->register('console.output', 'Symfony\Component\Console\Output\ConsoleOutput');

        $this->container->registerExtension(new DeploioExtension());
        if (file_exists(getcwd().'/.deploio')) {
            $loader = new YamlFileLoader($this->container, new FileLocator(getcwd()));
            $loader->load('.deploio');
        }

        $this->container->compile();
    }
}