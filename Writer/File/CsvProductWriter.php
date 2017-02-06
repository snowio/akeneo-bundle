<?php

namespace Snowio\Bundle\CsvConnectorBundle\Writer\File;

use Pim\Component\Connector\Writer\File\CsvProductWriter as BaseCsvProductWriter;
use Pim\Component\Connector\Writer\File\CsvWriter;
use Snowio\Bundle\CsvConnectorBundle\Writer\FileWriterOverriderTrait;

class CsvProductWriter extends BaseCsvProductWriter
{
    use FileWriterOverriderTrait;

    public function write(array $items)
    {
        $products = [];
        foreach ($items as $item) {
            $products[] = $item['product'];
        }
        CsvWriter::write($products);
    }

    public function getConfigurationFields()
    {
        $fields = parent::getConfigurationFields();
        $this->overrideGetConfigurationFields($fields);

        return $fields;
    }

    protected function fileName()
    {
        return 'product';
    }
}
