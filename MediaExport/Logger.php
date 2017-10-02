<?php

namespace Snowio\Bundle\CsvConnectorBundle\MediaExport;

use Symfony\Component\Filesystem\Exception\FileNotFoundException;

class Logger
{
    /** @var string */
    private $logDirectory;
    
    /**
     * @param string $logDirectory
     */
    public function __construct($logDirectory)
    {
        $this->logDirectory = $logDirectory;
    }

    /**
     * @param array $content
     * @param $jobId
     */
    public function writeLog(array $content, $jobId)
    {
        $logFile = $this->getLogFileNameForJob($jobId);

        if (!is_dir(dirname($logFile))) {
            mkdir(dirname($logFile), 0755, true);
        }

        $handle = fopen($logFile, 'a+');
        if ($handle === false) {
            throw new FileNotFoundException(
                sprintf('Error - log file (%s) could not be opened during media export.', $logFile)
            );
        }

        fputcsv($handle, $content, PHP_EOL);
        fclose($handle);
    }

    /**
     * @param $jobId
     * @return string
     */
    public function getLogFileNameForJob($jobId)
    {
        return rtrim($this->logDirectory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $jobId . '.log';
    }
}
