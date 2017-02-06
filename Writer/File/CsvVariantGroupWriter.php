<?php

namespace Snowio\Bundle\CsvConnectorBundle\Writer\File;

use Pim\Bundle\BaseConnectorBundle\Writer\File\CsvVariantGroupWriter as BaseCsvVariantGroupWriter;
use Snowio\Bundle\CsvConnectorBundle\Writer\FileWriterOverriderTrait;

class CsvVariantGroupWriter extends BaseCsvVariantGroupWriter
{
    use FileWriterOverriderTrait;

    public function write(array $items)
    {
        $variantGroups = [];

        if (!is_dir(dirname($this->getPath()))) {
            mkdir(dirname($this->getPath()), 0777, true);
        }

        foreach ($items as $item) {
            $variantGroups[] = $item['variant_group'];
            foreach ($item['media'] as $media) {
                if ($media && isset($media['filePath']) && $media['filePath']) {
                    $this->copyMedia($media);
                }
            }
        }

        $this->items = array_merge($this->items, $variantGroups);
    }

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
