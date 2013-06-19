<?php

namespace adanlobato\Deploio\Process\RsyncProcess;

interface ServerInterface
{
    /**
     * Returns the host
     */
    function getHost();

    /**
     * Return the connection port
     */
    function getPort();

    /**
     * Returns the directory
     */
    function getDir();

    /**
     * Returns the user
     */
    function getUser();

    /**
     * Returns the password
     */
    function getPassword();
}