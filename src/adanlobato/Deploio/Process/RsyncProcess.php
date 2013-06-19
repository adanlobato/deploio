<?php

namespace adanlobato\Deploio\Process;

use Symfony\Component\Process\ProcessBuilder;

class RsyncProcess
{
    protected $source;

    protected $destination;

    protected $options = '-azCv --force --delete --no-inc-recursive';

    /**
     * @return RsyncProcess
     */
    static public function create()
    {
        return new static();
    }

    /**
     * @param $source
     * @return RsyncProcess
     */
    public function setSource($source)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * @param $destination
     * @return RsyncProcess
     */
    public function setDestination($destination)
    {
        $this->destination = $destination;

        return $this;
    }

    /**
     * @param $options
     * @return RsyncProcess
     */
    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }

    public function run($callback = null)
    {
        if (null !== $callback && !is_callable($callback)) {
            throw new \InvalidArgumentException('The given callback is not a valid PHP callable.');
        }

        $builder = ProcessBuilder::create()
            ->setPrefix('rsync')
            ->setArguments(array_merge($this->options, array(
                $this->source,
                $this->destination
            )));

        $process = $builder->getProcess();
        $process->run($callback);

        return $process->getOutput();
    }
}