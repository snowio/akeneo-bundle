<?php

namespace Snowio\Bundle\CsvConnectorBundle\Job\JobParameters\DefaultValuesProvider;

use Akeneo\Component\Batch\Job\JobInterface;
use Akeneo\Component\Batch\Job\JobParameters\DefaultValuesProviderInterface;
use Pim\Component\Connector\Job\JobParameters\DefaultValuesProvider\ProductCsvExport as BaseProductCsvExport;

class ProductDefaultValues extends BaseProductCsvExport implements DefaultValuesProviderInterface
{
    use DefaultValuesTrait;

    public function supports(JobInterface $job)
    {
        return in_array($job->getName(), $this->supportedJobNames);
    }
}
