<?php

namespace adanlobato\Deploio\Process\RsyncProcess;

class Server implements ServerInterface
{
    /**
     * @var string
     */
    protected $dir;

    /**
     * @var string
     */
    protected $host;

    /**
     * @var string
     */
    protected $port = 22;

    /**
     * @var string
     */
    protected $user;

    /**
     * @var string
     */
    protected $password;

    public function __construct(array $options)
    {
        if (!isset($options['dir'])) {
            throw new \InvalidArgumentException('You must provide at least the directory of the server.');
        }
        $this->setDir($options['dir']);

        if (isset($options['host'])) {
            $this->host = $options['host'];
        }

        if (isset($options['port'])) {
            $this->port = $options['port'];
        }

        if (isset($options['user'])) {
            $this->user = $options['user'];
        }

        if (isset($options['password'])) {
            $this->password = $options['password'];
        }
    }

    /**
     * @param string $dir
     */
    public function setDir($dir)
    {
        $this->dir = rtrim($dir, '/').'/';
    }

    /**
     * @return string
     */
    public function getDir()
    {
        return $this->dir;
    }

    /**
     * @param string $host
     */
    public function setHost($host)
    {
        $this->host = $host;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @param string $port
     */
    public function setPort($port)
    {
        $this->port = $port;
    }

    /**
     * @return string
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @param string $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    public function __toString()
    {
        $server = '';
        if ($this->host) {
            if ($this->user) {
                $server .= $this->user;

                if ($this->password) {
                    $server .= ':'.$this->password;
                }
            }

            $server .= $server ? '@'.$this->host : $this->host;
        }

        $server .= $server ? ':'.$this->dir : $this->dir;

        return $server;
    }

    public static function createFromString($server)
    {
        if (false === strpos($server, ':')) {
            $server = 'file://'.$server;
        } else {
            $server = '//'.$server;
        }

        $server = preg_replace('#//([^:]*)@#', '//$1:@', $server);

        return new static(array(
            'host' => parse_url($server, PHP_URL_HOST),
            'port' => parse_url($server, PHP_URL_PORT),
            'user' => parse_url($server, PHP_URL_USER),
            'password' => parse_url($server, PHP_URL_PASS),
            'dir' => parse_url($server, PHP_URL_PATH),
        ));
    }
}