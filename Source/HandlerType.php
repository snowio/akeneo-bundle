<?php

namespace Snowio\Bundle\CsvConnectorBundle\Source;

class HandlerType
{

    const PRODUCT = 'products';
    const VARIANT = 'variants';
    const CATEGORY = 'categories';
    const ATTRIBUTE = 'attributes';
    const ATTRIBUTE_OPTIONS = 'attributeOptions';

    /**
     * @return array
     */
    public function getAllTypes()
    {
        return [
            self::PRODUCT,
            self::VARIANT,
            self::CATEGORY,
            self::ATTRIBUTE,
            self::ATTRIBUTE_OPTIONS,
        ];
    }
}