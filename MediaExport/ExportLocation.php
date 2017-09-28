<?php

namespace Snowio\Bundle\MediaExport;

class ExportLocation
{
    /** @var  string */
    private $directory;

    /** @var string|null */
    private $host;

    /** @var string|null */
    private $user;

    public function __construct($directory, $host = null, $user = null)
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
