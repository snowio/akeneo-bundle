<?php

namespace Snowio\Bundle\CsvConnectorBundle\Step;

use Akeneo\Component\Batch\Job\JobRepositoryInterface;
use Akeneo\Component\Batch\Model\StepExecution;
use Akeneo\Component\Batch\Step\AbstractStep;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Use this class to check export threshold has been met in previous step
 * Create a service, inject the relevant threshold, and configure the job so this comes after the step you want to check
 */
class CheckThresholdsStep extends AbstractStep
{
    /** @var int */
    private $minimumExportThreshold;

    /**
     * CheckThresholdsStep constructor.
     * @param string $name
     * @param EventDispatcherInterface $eventDispatcher
     * @param JobRepositoryInterface $jobRepository
     * @param $minimumExportThreshold
     */
    public function __construct(
        $name,
        EventDispatcherInterface $eventDispatcher,
        JobRepositoryInterface $jobRepository,
        $minimumExportThreshold = 0
    ) {
        parent::__construct($name, $eventDispatcher, $jobRepository);
        $this->minimumExportThreshold = (int)$minimumExportThreshold;
    }

    /**
     * Extension point for subclasses to execute business logic. Subclasses should set the {@link ExitStatus} on the
     * {@link StepExecution} before returning.
     *
     * Do not catch exception here. It will be correctly handled by the execute() method.
     *
     * @param StepExecution $stepExecution the current step context
     *
     * @throws \Exception
     */
    protected function doExecute(StepExecution $stepExecution)
    {
        $previousStepExecution = $this->getPreviousStepExecution($stepExecution);

        $stepExecution->addSummaryInfo(
            'minimum_threshold',
            sprintf('%s (%s)', $this->minimumExportThreshold, $previousStepExecution->getStepName())
        );

        if ($this->minimumExportThreshold > 0 && !$this->doesExportCountMeetThreshold($previousStepExecution)) {
            throw new \Exception(
                sprintf(
                    'Error - attempted to export less than the minimum threshold (step: %s/threshold: %s).',
                    $previousStepExecution->getStepName(),
                    $this->minimumExportThreshold
                )
            );
        }
    }

    /**
     * @param StepExecution $stepExecution
     * @return bool
     * @author James Pollard <jp@amp.co>
     */
    private function doesExportCountMeetThreshold(StepExecution $stepExecution)
    {
        return $stepExecution->getSummaryInfo('read') >= $this->minimumExportThreshold;
    }

    /**
     * @param StepExecution $stepExecution
     * @return StepExecution
     * @throws \Exception
     * @author James Pollard <jp@amp.co>
     */
    private function getPreviousStepExecution(StepExecution $stepExecution)
    {
        $stepExecutions = $stepExecution->getJobExecution()->getStepExecutions()->toArray();
        // set array pointer to last element i.e. the current execution
        end($stepExecutions);
        $previousStepExecution = prev($stepExecutions);

        if (!($previousStepExecution instanceof StepExecution)) {
            throw new \Exception('Error during threshold check step - previous execution step was not found.');
        }

        return $previousStepExecution;
    }
}
