<?php

namespace adanlobato\Deploio\Deployer;

use Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface;

use adanlobato\Deploio\Process\RsyncProcess;

class RsyncDeployer
{
    protected $input;
    protected $output;

    public function __construct(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
    }

    public function deploy($source, $destination, $dryRun = false)
    {
        $this->output->writeln(sprintf(
            'Deploying <comment>%s</comment> into <comment>%s</comment> <info>%s</info>',
            $source,
            $destination,
            $dryRun ? '(DRY RUN)' : ''
        ));

        $options = array('-azCv', '--force', '--delete', '--no-inc-recursive');

        if ($dryRun) {
            $options[] = '--dry-run';
        }

        $output = $this->output;
        $counter = false;
        $error = array();
        RsyncProcess::create()
            ->setSource($source)
            ->setDestination($destination)
            ->setOptions($options)
            ->run(function ($type, $buffer) use ($output, &$counter, &$error) {
                $output->write(call_user_func_array(
                    array($this, 'deployCallback'),
                    array($buffer, &$counter, &$error, $output->getVerbosity())
                ));
            });

        $message = $error ?
            "\n\n".'<fg=black;bg=yellow>Deployed %s files out of %s, with some failures:</fg=black;bg=yellow>' :
            "\n\n".'<fg=black;bg=green>Successfully deployed %s files out of %s</fg=black;bg=green>';
        $this->output->writeln(sprintf($message, $counter - count($error), (int) $counter));

        if ($error) {
            $this->output->writeln(array_map(function($line){
                return '    - '.$line;
            }, $error));
        }
    }

    private function deployCallback($buffer, &$counter, &$error, $verbosity = OutputInterface::VERBOSITY_NORMAL)
    {
        $lines = explode("\n", $buffer);
        $out = array();
        foreach ($lines as $line) {
            if ('./' === $line) {
                $counter = 0;
            }

            if (false === $counter) {
                continue;
            }

            if (trim($line) && preg_match('/^[^*?"<>|:]*$/', trim($line))) {
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