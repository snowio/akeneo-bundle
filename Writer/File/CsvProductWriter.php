<?php

namespace Snowio\Bundle\CsvConnectorBundle\Writer\File;

use Pim\Component\Connector\Writer\File\CsvProductWriter as BaseCsvProductWriter;
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
        parent::write($products);

        $exportDirectory = dirname($this->getPath());
        if (!is_dir($exportDirectory)) {
            $this->localFs->mkdir($exportDirectory);
        }

        foreach ($items as $item) {
            foreach ($item['media'] as $media) {
                if ($media && isset($media['filePath']) && $media['filePath']) {
                    $this->copyMedia($media);
                }
            }
        }
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
