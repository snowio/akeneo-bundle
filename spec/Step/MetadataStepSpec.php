<?php

namespace spec\Snowio\Bundle\CsvConnectorBundle\Step;

use PhpSpec\ObjectBehavior;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Akeneo\Component\Batch\Job\JobRepositoryInterface;
use Akeneo\Component\Batch\Model\StepExecution;
use Akeneo\Component\Batch\Job\BatchStatus;
use Akeneo\Component\Batch\Job\ExitStatus;
use Akeneo\Component\Batch\Event\EventInterface;
use Akeneo\Component\Batch\Job\JobParameters;
use Akeneo\Component\Batch\Model\JobExecution;
use Akeneo\Component\Batch\Model\JobInstance;
use Prophecy\Argument;
use Symfony\Component\Filesystem\Filesystem;

class MetadataStepSpec extends ObjectBehavior
{
    /** @var Filesystem */
    private $filesystem;

    /** @var string */
    private $directory;

    public function let(
        EventDispatcherInterface $dispatcher,
        JobRepositoryInterface $respository
    ) {
        $this->directory = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'spec' . DIRECTORY_SEPARATOR;
        $this->filesystem = new Filesystem();
        $this->filesystem->mkdir($this->directory);

        $this->beConstructedWith(['job_test_name'], $dispatcher, $respository);
    }

    // @codingStandardsIgnoreLine
    function it_executes_with_success(
        StepExecution $execution,
        $dispatcher,
        JobParameters $jobParameters,
        BatchStatus $status,
        ExitStatus $exitStatus,
        JobInstance $jobInstance,
        JobExecution $jobExecution
    ) {
        $jobParameters->get('exportDir')->willReturn($this->directory);
        $jobParameters->get('delimiter')->willReturn('abc');
        $jobParameters->get('filters')->willReturn('abc');
        $jobParameters->has('filters')->willReturn(true);
        $jobParameters->get('enclosure')->willReturn('abc');
        $jobParameters->get('withHeader')->willReturn('abc');
        $jobParameters->get('decimalSeparator')->willReturn('abc');
        $jobParameters->has('decimalSeparator')->willReturn(true);
        $jobParameters->get('dateFormat')->willReturn('abc');
        $jobParameters->has('dateFormat')->willReturn(true);
        $jobParameters->get('with_media')->willReturn('abc');
        $jobParameters->has('with_media')->willReturn(true);

        $execution->getJobParameters()->willReturn($jobParameters);

        $jobInstance->getJobName()->willReturn('Jobtest');
        $jobExecution->getJobInstance()->willReturn($jobInstance);
        $execution->getJobExecution()->willReturn($jobExecution);

        # before
        $execution->getStatus()->willReturn($status);
        $status->getValue()->willReturn(BatchStatus::STARTING);
        $dispatcher->dispatch(EventInterface::BEFORE_STEP_EXECUTION, Argument::any())->shouldBeCalled();
        $execution->setStartTime(Argument::any())->shouldBeCalled();
        $execution->setStatus(Argument::any())->shouldBeCalled();
        $execution->upgradeStatus(Argument::any())->shouldBeCalled();

        # my step logic assertions
        $execution->addSummaryInfo('metadata_location', $this->directory.'metadata.json')->shouldBeCalled();

        # after
        $execution->getExitStatus()->willReturn($exitStatus);
        $exitStatus->getExitCode()->willReturn(ExitStatus::COMPLETED);
        $execution->isTerminateOnly()->willReturn(false);
        $execution->upgradeStatus(Argument::any())->shouldBeCalled();
        $dispatcher->dispatch(EventInterface::STEP_EXECUTION_SUCCEEDED, Argument::any())->shouldBeCalled();
        $dispatcher->dispatch(EventInterface::STEP_EXECUTION_COMPLETED, Argument::any())->shouldBeCalled();
        $execution->setEndTime(Argument::any())->shouldBeCalled();
        $execution->setExitStatus(Argument::any())->shouldBeCalled();

        $this->execute($execution);
    }
}
