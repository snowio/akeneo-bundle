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
}
