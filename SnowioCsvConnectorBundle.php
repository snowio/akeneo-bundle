<?php

namespace Snowio\Bundle\CsvConnectorBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class SnowioPostProductExportBundle
 * @package Snowio\Bundle\PostProductExportBundle
 * @author Cristian Quiroz <cq@amp.co>
 *
 * This Bundle is used to create new csv export jobs that:
 * 1) Generate CSV files, using Akeneo's Csv Connector functionality
 * 2) Posts product data to a Snow.io using Guzzle
 */
class SnowioCsvConnectorBundle extends Bundle
{

}
