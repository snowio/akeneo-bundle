<?php

namespace Snowio\Bundle\CsvConnectorBundle\Writer\File\Csv;

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
     * Override Export medias to avoid deleting the files path since it is shared.
     *
     * More info see https://github.com/AmpersandHQ/bes-akeneo/pull/46
     */
    protected function exportMedias()
    {
        $outputDirectory = dirname($this->getPath());
        $workingDirectory = $this->stepExecution->getJobExecution()->getExecutionContext()
            ->get(JobInterface::WORKING_DIRECTORY_PARAMETER);

        $outputFilesDirectory = $outputDirectory . DIRECTORY_SEPARATOR . 'files';
        $workingFilesDirectory = $workingDirectory . 'files';

        /* Avoid removing files folder
        if ($this->localFs->exists($outputFilesDirectory)) {
            $this->localFs->remove($outputFilesDirectory);
        }*/

        if ($this->localFs->exists($workingFilesDirectory)) {
            $this->localFs->mirror($workingFilesDirectory, $outputFilesDirectory);
        }
    }
}
