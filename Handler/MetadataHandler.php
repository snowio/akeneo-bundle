<?php

namespace Snowio\Bundle\CsvConnectorBundle\Handler;

use Akeneo\Component\Batch\Item\AbstractConfigurableStepElement;
use Akeneo\Component\Batch\Model\StepExecution;
use Akeneo\Component\Batch\Step\StepExecutionAwareInterface;
use Snowio\Bundle\CsvConnectorBundle\SnowioCsvConnectorBundle;

class MetadataHandler extends AbstractConfigurableStepElement implements StepExecutionAwareInterface
{
    /** @var StepExecution */
    private $stepExecution;
    /** @var  array */
    private $configs;

    public function execute()
    {
        $location = rtrim($this->configs['exportDir'], '/') . DIRECTORY_SEPARATOR . 'metadata.json';
        file_put_contents(
            $location,
            json_encode([
                'bundleVersion' => SnowioCsvConnectorBundle::VERSION,
                'jobCode' => $this->stepExecution->getJobExecution()->getJobInstance()->getAlias(),
                'date' => gmdate('Y-m-d_H:i:s'),
                'channel' => $this->configs['channel'],
                'delimiter' => $this->configs['delimiter'],
                'enclosure' => $this->configs['enclosure'],
                'withHeader' => $this->configs['withHeader'],
                'decimalSeparator' => $this->configs['decimalSeparator'],
                'dateFormat' => $this->configs['dateFormat'],
            ])
        );

        $this->stepExecution->addSummaryInfo('metadata_location', $location);
    }

    public function getConfigurationFields()
    {
        return [];
    }

    public function setConfiguration(array $config)
    {
        parent::setConfiguration($config);
        $this->configs = $config;

    }

    public function setStepExecution(StepExecution $stepExecution)
    {
        $this->stepExecution = $stepExecution;
    }

}
