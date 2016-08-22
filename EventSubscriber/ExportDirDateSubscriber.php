<?php

namespace Snowio\Bundle\CsvConnectorBundle\EventSubscriber;

use Akeneo\Component\Batch\Event\EventInterface;
use Akeneo\Component\Batch\Event\JobExecutionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ExportDirDateSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            EventInterface::BEFORE_JOB_EXECUTION => 'addDate'
        ];
    }

    public function addDate(JobExecutionEvent $event)
    {
        $jobInstance = $event->getJobExecution()->getJobInstance();
        $alias = $jobInstance->getAlias();

        if (in_array($alias, ['snowio_complete_export', 'snowio_incomplete_export'])) {
            $job = $jobInstance->getJob();
            $config = $job->getConfiguration();
            $config['exportDir'] = rtrim($config['exportDir'], '/') . DIRECTORY_SEPARATOR . date('Y-m-d_H:i:s');

            $job->setConfiguration($config);
        }
    }
}
