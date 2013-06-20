<?php

namespace adanlobato\Deploio\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use adanlobato\Deploio\Compiler;

class CompileCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('compile')
            ->setDescription('Compiles the fixer as a phar file')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $compiler = new Compiler();
        $compiler->compile();
    }
}