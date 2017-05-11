<?php

namespace Snowio\Bundle\CsvConnectorBundle\Writer\File\Csv;

use Akeneo\Component\Batch\Job\JobInterface;

trait WriterOverriderTrait
{
    /**
     * We overwrite the getPath to use step name as filename
     * @author Nei Santos <ns@amp.co>
     * @return string
     */
    public function getPath(array $placeholders = [])
    {
        $parameters = $this->stepExecution->getJobParameters();

        $filePath = implode(
            '',
            [
                rtrim($parameters->get('exportDir'), '/'),
                DIRECTORY_SEPARATOR,
                $this->sanitize($this->stepExecution->getStepName()),
                ".csv"
            ]
        );

        return $filePath;
    }

    /**
     * Export medias from the working directory to the output expected directory.
     *
     * Basically, we first remove the content of /path/where/my/user/expects/the/export/files/.
     * (This path can exist of an export was launched previously)
     *
     * Then we copy /path/of/the/working/directory/files/ to /path/where/my/user/expects/the/export/files/.
     */
    protected function exportMedias()
    {
        $outputDirectory = dirname($this->getPath());
        $workingDirectory = $this->stepExecution->getJobExecution()->getExecutionContext()
            ->get(JobInterface::WORKING_DIRECTORY_PARAMETER);

        $outputFilesDirectory = $outputDirectory . DIRECTORY_SEPARATOR . 'files';
        $workingFilesDirectory = $workingDirectory . 'files';

        /*
        if ($this->localFs->exists($outputFilesDirectory)) {
            $this->localFs->remove($outputFilesDirectory);
        }*/

        if ($this->localFs->exists($workingFilesDirectory)) {
            $this->localFs->mirror($workingFilesDirectory, $outputFilesDirectory);
        }
    }
}
