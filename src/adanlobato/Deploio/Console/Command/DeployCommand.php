<?php

namespace adanlobato\Deploio\Console\Command;

use Symfony\Component\Console\Command\Command,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface,
    Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputOption;

use adanlobato\Deploio\Process\RsyncProcess;

class DeployCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('deploy')
            ->setDescription('Deploy a project to a environment.')
            ->addArgument('destination', InputArgument::REQUIRED, 'The destination location.')
            ->addOption('source', null, InputOption::VALUE_REQUIRED, 'The source location.', getcwd())
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'If passed, operations are simulated instead of performed.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getDeployer()->deploy(
            $input->getOption('source'),
            $input->getArgument('destination'),
            $input->getOption('dry-run')
        );
    }

    protected function getDeployer()
    {
        return $this->getApplication()->getContainer()->get('deployer');
    }
}