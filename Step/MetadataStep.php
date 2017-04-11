<?php

namespace Snowio\Bundle\CsvConnectorBundle\Step;

use Akeneo\Component\Batch\Step\AbstractStep;
use Akeneo\Component\Batch\Model\StepExecution;
use Snowio\Bundle\CsvConnectorBundle\SnowioCsvConnectorBundle;

class MetadataStep extends AbstractStep
{
    /**
     * Create json file with metadata information
     *
     * @param StepExecution $stepExecution
     */
    protected function doExecute(StepExecution $stepExecution)
    {
        $jobParameters = $stepExecution->getJobParameters();

        $location = rtrim($jobParameters->get('exportDir'), '/') . DIRECTORY_SEPARATOR . 'metadata.json';

        $content = $this->generateContent($stepExecution, $jobParameters);

        if (false === file_put_contents($location, $content)) {
            $stepExecution->addFailureException(
                new \RuntimeException('Cannot create metadata file.')
            );
        }

        $stepExecution->addSummaryInfo('metadata_location', $location);
    }

    /**
     * Create json file with metadata
     *
     * @param StepExecution $stepExecution
     * @param JobParameters $jobParameters
     *
     * @return String Json
     */
    protected function generateContent($stepExecution, $jobParameters)
    {
        $content = [
            'bundleVersion'     => SnowioCsvConnectorBundle::VERSION,
            'jobCode'           => $stepExecution->getJobExecution()->getJobInstance()->getJobName(),
            'date'              => gmdate('Y-m-d H:i:s'),
            'delimiter'         => $jobParameters->get('delimiter'),
            'enclosure'         => $jobParameters->get('enclosure'),
            'withHeader'        => $jobParameters->get('withHeader')
        ];

        if ($jobParameters->has('filters')) {
            $content['filters'] = $jobParameters->get('filters');
        }

        if ($jobParameters->has('decimalSeparator')) {
            $content['decimalSeparator'] = $jobParameters->get('decimalSeparator');
        }

        if ($jobParameters->has('dateFormat')) {
            $content['dateFormat'] = $jobParameters->get('dateFormat');
        }

        if ($jobParameters->has('with_media')) {
            $content['with_media'] = $jobParameters->get('with_media');
        }

        return json_encode($content);
    }
}
