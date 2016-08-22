<?php

namespace Snowio\Bundle\CsvConnectorBundle\Step;

use Akeneo\Component\Batch\Item\AbstractConfigurableStepElement;
use Akeneo\Component\Batch\Model\StepExecution;
use Akeneo\Component\Batch\Step\AbstractStep;
use Snowio\Bundle\CsvConnectorBundle\Handler\ArchiveHandler;
use Snowio\Bundle\CsvConnectorBundle\Handler\MetadataHandler;

class ArchiveStep extends AbstractStep
{
    /** @var  MetadataHandler */
    protected $metadata;
    /** @var  ArchiveHandler */
    protected $archive;

    protected function doExecute(StepExecution $stepExecution)
    {
        $this->metadata->setStepExecution($stepExecution);
        $this->metadata->execute();
        $this->archive->setStepExecution($stepExecution);
        $this->archive->execute();
    }

    public function getConfiguration()
    {
        $configuration = array();
        foreach ($this->getConfigurableStepElements() as $stepElement) {
            if ($stepElement instanceof AbstractConfigurableStepElement) {
                foreach ($stepElement->getConfiguration() as $key => $value) {
                    if (!isset($configuration[$key]) || $value) {
                        $configuration[$key] = $value;
                    }
                }
            }
        }

        return $configuration;
    }

    public function setConfiguration(array $config)
    {
        foreach ($this->getConfigurableStepElements() as $stepElement) {
            if ($stepElement instanceof AbstractConfigurableStepElement) {
                $stepElement->setConfiguration($config);
            }
        }
    }

    public function setMetadata($metadata)
    {
        $this->metadata = $metadata;

        return $this;
    }

    public function getMetadata()
    {
        return $this->metadata;
    }

    public function getArchive()
    {
        return $this->archive;
    }

    public function setArchive($archive)
    {
        $this->archive = $archive;
    }

    public function getConfigurableStepElements()
    {
        return [
            'metadata' => $this->getMetadata(),
            'archive' => $this->getArchive(),
        ];
    }
}
