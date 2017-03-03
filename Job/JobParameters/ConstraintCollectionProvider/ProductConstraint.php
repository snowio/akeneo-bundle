<?php

namespace Snowio\Bundle\CsvConnectorBundle\Job\JobParameters\ConstraintCollectionProvider;

use Akeneo\Component\Batch\Job\JobInterface;
use Akeneo\Component\Batch\Job\JobParameters\ConstraintCollectionProviderInterface;
use Pim\Component\Connector\Job\JobParameters\ConstraintCollectionProvider\ProductCsvExport as BaseProductCsvExport;

class ProductConstraint extends BaseProductCsvExport implements ConstraintCollectionProviderInterface
{
    use ConstraintTrait;

    public function supports(JobInterface $job)
    {
        return in_array($job->getName(), $this->supportedJobNames);
    }
}
