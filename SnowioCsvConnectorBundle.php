<?php

namespace Snowio\Bundle\CsvConnectorBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Pim\Bundle\EnrichBundle\DependencyInjection\Reference\ReferenceFactory;

/**
 * Class SnowioCsvConnectorBundle
 * @package Snowio\Bundle\SnowioCsvConnectorBundle
 * @author Nei Santos <ns@amp.co>
 *
 * This Bundle is used to create new csv export jobs that:
 * 1) Generate CSV files, using Akeneo's Csv Connector functionality
 * 2) Posts product data to a Snow.io using Guzzle
 */
class SnowioCsvConnectorBundle extends Bundle
{
    /** Increment the version number if exported data has BC break changes. */
    const VERSION = 2;
}
