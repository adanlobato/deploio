<?php

namespace adanlobato\Deploio\Console\Command;

use Symfony\Component\Console\Command\Command,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface,
    Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputOption;

use Symfony\Component\Process\ProcessBuilder,
    Symfony\Component\Process\Process;

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
        $output->writeln(sprintf(
            'Deploying <comment>%s</comment> into <comment>%s</comment>',
            $input->getArgument('source'),
            $input->getArgument('destination')
        ));

        $arguments = array(
            '-azCv',
            '--force',
            '--delete',
            '--no-inc-recursive',
            '-azC',
        );

        if ($input->getOption('dry-run')) {
            $arguments[] = '--dry-run';
        }

        $process = ProcessBuilder::create()
            ->setPrefix('rsync')
            ->setArguments(array_merge($arguments, array(
                $input->getArgument('source'),
                $input->getArgument('destination'),
            )))
            ->getProcess();

        $counter = 0;
        $error = array();
        $process->run(function ($type, $buffer) use ($input, $output, &$counter, &$error) {
            $lines = explode("\n", $buffer);
            $out = array();
            foreach ($lines as $line) {
                if (trim($line) && file_exists($input->getArgument('source').DIRECTORY_SEPARATOR.trim($line))) {
                    $counter++;
                    if (OutputInterface::VERBOSITY_VERBOSE <= $output->getVerbosity()) {
                        $out[] = $line."\n";
                    } else {
                        $out[] = $counter % 80 ? '.' : ".\n";
                    }
                } elseif (preg_match('@Permission denied@', $line)) {
                    $counter++;
                    $error[] = $line;
                    if (OutputInterface::VERBOSITY_VERBOSE <= $output->getVerbosity()) {
                        $out[] = $line."\n";
                    } else {
                        $out[] = $counter % 80 ? '<error>F</error>' : "<error>F</error>\n";
                    }
                } else {
                    if (OutputInterface::VERBOSITY_VERBOSE <= $output->getVerbosity() && trim($line)) {
                        $out[] = $line."\n";
                    }
                }
            }

            $output->write(implode('', $out));
        });

        if ($error) {
            $output->writeln(array(
                '',
                '',sprintf('<fg=black;bg=yellow>Deployed %s files out of %s, with some failures:</fg=black;bg=yellow>', $counter - count($error), $counter)
            ));
            $output->writeln(array_map(function($line){
                return '    - '.$line;
            }, $error));
        } else {
            $output->writeln(array(
                '',
                '',
                sprintf('<fg=black;bg=green>Successfully deployed %s files out of %s</fg=black;bg=green>', $counter - count($error), $counter),
            ));
        }
    }
}