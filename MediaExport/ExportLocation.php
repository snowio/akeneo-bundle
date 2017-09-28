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
}
