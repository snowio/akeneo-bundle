<?php

namespace Snowio\Bundle\CsvConnectorBundle\Job\JobParameters\FormConfigurationProvider;

use Akeneo\Component\Batch\Job\JobInterface;
use Pim\Bundle\ImportExportBundle\JobParameters\FormConfigurationProviderInterface;
use Pim\Bundle\ImportExportBundle\JobParameters\FormConfigurationProvider\ProductCsvExport as BaseProductCsvExport;

/**
 * Configuration for additional fields to Product Csv Export
 *
 * @author Nei Santos <ns@amp.co>
 */
class ProductCsvExport extends BaseProductCsvExport implements FormConfigurationProviderInterface
{
    use FormConfigurationTrait;

    /**
     * {@inheritdoc}
     */
    public function supports(JobInterface $job)
    {
        return in_array($job->getName(), $this->supportedJobNames);
    }
}
