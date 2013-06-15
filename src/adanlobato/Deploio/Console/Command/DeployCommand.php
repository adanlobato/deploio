<?php

namespace adanlobato\Deploio\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class DeployCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('deploy')
            ->setDescription('Deploy a project to a environment.')
            ->addArgument('source', InputArgument::REQUIRED, 'The source location.')
            ->addArgument('destination', InputArgument::REQUIRED, 'The destination location.')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'If passed, operations are simulated instead of performed.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $command = 'rsync -azC --force --delete --progress --no-inc-recursive %s %s %s';
        $dryRun = $input->getOption('dry-run') ? '--dry-run' : '';

        system(sprintf($command, $dryRun, $input->getArgument('source'), $input->getArgument('destination')));
    }
}