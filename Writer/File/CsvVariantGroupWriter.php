<?php

namespace Snowio\Bundle\CsvConnectorBundle\Writer\File;

use Pim\Bundle\BaseConnectorBundle\Writer\File\CsvVariantGroupWriter as BaseCsvVariantGroupWriter;
use Snowio\Bundle\CsvConnectorBundle\Writer\FileWriterOverriderTrait;

class CsvVariantGroupWriter extends BaseCsvVariantGroupWriter
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
        return 'variant_group';
    }
}
