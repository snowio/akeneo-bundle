<?php

namespace Snowio\Bundle\CsvConnectorBundle\Job\JobParameters\ConstraintCollectionProvider;

use Akeneo\Component\Batch\Job\JobInterface;
use Akeneo\Component\Batch\Job\JobParameters\ConstraintCollectionProviderInterface;
use Pim\Component\Connector\Job\JobParameters\ConstraintCollectionProvider\SimpleCsvExport as BaseSimpleCsvExport;

class SimpleConstraint extends BaseSimpleCsvExport implements ConstraintCollectionProviderInterface
{
    use ConstraintTrait;

    public function supports(JobInterface $job)
    {
        return in_array($job->getName(), $this->supportedJobNames);
    }
}
