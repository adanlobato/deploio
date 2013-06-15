<?php

namespace adanlobato\Deploio\Console;

use Symfony\Component\Console\Application as BaseApplication;

use adanlobato\Deploio\Console\Command;

class Application extends BaseApplication
{
    public function __construct()
    {
        parent::__construct('Deploio', '1.0.0');

        $this->add(new Command\DeployCommand());
    }
}