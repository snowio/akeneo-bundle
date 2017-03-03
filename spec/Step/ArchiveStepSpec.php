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
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Prophecy\Argument;
use \ZipArchive;

class ArchiveStepSpec extends ObjectBehavior
{
    /** @var string */
    private $directory;

    public function let(
        EventDispatcherInterface $dispatcher,
        JobRepositoryInterface $respository,
        ZipArchive $zip
    ) {
        $this->directory = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'spec' . DIRECTORY_SEPARATOR;

        $this->beConstructedWith(['job_test_name'], $dispatcher, $respository, $zip);
    }

    // @codingStandardsIgnoreLine
    public function it_executes_with_success(
        StepExecution $execution,
        $dispatcher,
        $respository,
        JobParameters $jobParameters,
        BatchStatus $status,
        ExitStatus $exitStatus,
        $zip
    ) {
        $jobParameters->get('exportDir')->willReturn($this->directory);
        $execution->getJobParameters()->willReturn($jobParameters);
        $execution->getJobExecution()->willReturn($execution);

        # before
        $execution->getStatus()->willReturn($status);
        $status->getValue()->willReturn(BatchStatus::STARTING);
        $dispatcher->dispatch(EventInterface::BEFORE_STEP_EXECUTION, Argument::any())->shouldBeCalled();
        $execution->setStartTime(Argument::any())->shouldBeCalled();
        $execution->setStatus(Argument::any())->shouldBeCalled();
        $execution->upgradeStatus(Argument::any())->shouldBeCalled();

        $zip->open(Argument::any(), Argument::any())->willReturn(true);
        $zip->addPattern(Argument::any(), Argument::any(), Argument::any())->willReturn(array(
            '/tmp/metadata.json',
            '/tmp/anotherfile.csv'
        ));
        $zip->close()->shouldBeCalled();

        $execution->addSummaryInfo('zip_location', $this->directory.'export.zip')->shouldBeCalled();

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

    // @codingStandardsIgnoreLine
    public function it_throws_add_failure_exception_when_there_is_none_mathed_files(
        StepExecution $execution,
        $dispatcher,
        $respository,
        JobParameters $jobParameters,
        BatchStatus $status,
        ExitStatus $exitStatus,
        $zip
    ) {
        
        $jobParameters->get('exportDir')->willReturn($this->directory);
        $execution->getJobParameters()->willReturn($jobParameters);
        $execution->getJobExecution()->willReturn($execution);

        # before
        $execution->getStatus()->willReturn($status);
        $status->getValue()->willReturn(BatchStatus::STARTING);
        $dispatcher->dispatch(EventInterface::BEFORE_STEP_EXECUTION, Argument::any())->shouldBeCalled();
        $execution->setStartTime(Argument::any())->shouldBeCalled();
        $execution->setStatus(Argument::any())->shouldBeCalled();
        $execution->upgradeStatus(Argument::any())->shouldBeCalled();

        $zip->open(Argument::any(), Argument::any())->willReturn(true);

        // return empty array
        $zip->addPattern(Argument::any(), Argument::any(), Argument::any())->willReturn(array());
        
        $execution->addFailureException(Argument::any())->shouldBeCalled();

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
