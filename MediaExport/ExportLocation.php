<?php

namespace Snowio\Bundle\CsvConnectorBundle\MediaExport;

class ExportLocation
{
    /** @var  string */
    private $directory;

    /** @var string|false */
    private $host;

    /** @var string|false */
    private $user;

    public function __construct($directory, $host = false, $user = false)
    {
        $this->directory = $directory;
        $this->host = $host;
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function toString()
    {
        if ($this->user && $this->host) {
            return sprintf(
                '%s@%s:%s',
                $this->user,
                $this->host,
                $this->directory
            );
        }

        if (!$this->user && $this->host) {
            return sprintf(
                '%s:%s',
                $this->host,
                $this->directory
            );
        }

        return $this->directory;
    }

    public function setUser($user)
    {
        if (is_string($user) && !empty($user)) {
            $this->user = $user;
        }
    }

    public function setDirectory($directory)
    {
        if (is_string($directory) && !empty($directory)) {
            $this->directory = $directory;
        }
    }

    public function setHost($host)
    {
        if (is_string($host) && !empty($host)) {
            $this->host = $host;
        }
    }
}
