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
            ->addArgument('source', InputArgument::REQUIRED, 'The source location.')
            ->addArgument('destination', InputArgument::REQUIRED, 'The destination location.')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'If passed, operations are simulated instead of performed.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->deploy($output, $input->getArgument('source'), $input->getArgument('destination'), $input->getOption('dry-run'));
    }

    protected function deploy(OutputInterface $output, $source, $destination, $dryRun = false)
    {
        $output->writeln(sprintf('Deploying <comment>%s</comment> into <comment>%s</comment>', $source, $destination));

        $options = '-azCv --force --delete --no-inc-recursive';
        if ($dryRun) {
            $options .= ' --dry-run';
        }

        $counter = 0;
        $error = array();
        RsyncProcess::create()
            ->setSource($source)
            ->setDestination($destination)
            ->setOptions($options)
            ->run(function ($type, $buffer) use ($output, $source, &$counter, &$error) {
                $output->write(call_user_func_array(
                    array($this, 'deployCallback'),
                    array($source, $buffer, &$counter, &$error, $output->getVerbosity())
                ));
            });

        $message = $error ?
            "\n\n".'<fg=black;bg=yellow>Deployed %s files out of %s, with some failures:</fg=black;bg=yellow>' :
            "\n\n".'<fg=black;bg=green>Successfully deployed %s files out of %s</fg=black;bg=green>';
        $output->writeln(sprintf($message, $counter - count($error), $counter));

        if ($error) {
            $output->writeln(array_map(function($line){
                return '    - '.$line;
            }, $error));
        }
    }

    private function deployCallback($source, $buffer, &$counter, &$error, $verbosity = OutputInterface::VERBOSITY_NORMAL)
    {
        $lines = explode("\n", $buffer);
        $out = array();
        foreach ($lines as $line) {
            if (trim($line) && file_exists($source.DIRECTORY_SEPARATOR.trim($line))) {
                $counter++;
                if (OutputInterface::VERBOSITY_VERBOSE <= $verbosity) {
                    $out[] = $line."\n";
                } else {
                    $out[] = $counter % 80 ? '.' : ".\n";
                }
            } elseif (preg_match('@Permission denied@', $line)) {
                $counter++;
                $error[] = $line;
                if (OutputInterface::VERBOSITY_VERBOSE <= $verbosity) {
                    $out[] = $line."\n";
                } else {
                    $out[] = $counter % 80 ? '<error>F</error>' : "<error>F</error>\n";
                }
            } else {
                if (OutputInterface::VERBOSITY_VERBOSE <= $verbosity && trim($line)) {
                    $out[] = $line."\n";
                }
            }
        }

        return implode('', $out);
    }
}