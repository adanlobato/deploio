<?php

namespace adanlobato\Deploio\Console;

use Symfony\Component\Console\Application as BaseApplication;

class Application extends BaseApplication
{
    public function __construct()
    {
        parent::__construct('Deploio', '1.0.0');
    }
}