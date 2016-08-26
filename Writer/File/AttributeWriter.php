<?php

namespace Snowio\Bundle\CsvConnectorBundle\Writer\File;

use Pim\Component\Connector\Writer\File\CsvWriter;
use Snowio\Bundle\CsvConnectorBundle\Writer\FileWriterOverriderTrait;

class AttributeWriter extends CsvWriter
{
    use FileWriterOverriderTrait;

    public function getConfigurationFields()
    {
        $fields = parent::getConfigurationFields();
        $this->overrideGetConfigurationFields($fields);

        return $fields;
    }

    protected function fileName()
    {
        return 'attribute';
    }
}
