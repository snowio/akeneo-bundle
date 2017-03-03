<?php

namespace Snowio\Bundle\CsvConnectorBundle\Writer\File\Csv;

use Pim\Component\Connector\Writer\File\Csv\ProductWriter as BaseProductWriter;

class ProductWriter extends BaseProductWriter
{
    use WriterOverriderTrait;
}
