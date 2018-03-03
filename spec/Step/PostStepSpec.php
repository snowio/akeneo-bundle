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
use Psr\Http\Message\StreamInterface;
use Prophecy\Argument;
use Symfony\Component\Filesystem\Filesystem;

class PostStepSpec extends ObjectBehavior
{
    /** @var Filesystem */
    private $filesystem;

    /** @var string */
    private $directory;

    public function let(
        EventDispatcherInterface $dispatcher,
        JobRepositoryInterface $respository,
        Client $guzzle
    ) {
        $this->directory = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'spec' . DIRECTORY_SEPARATOR;
        $this->filesystem = new Filesystem();

        $this->beConstructedWith(['job_test_name'], $dispatcher, $respository, $guzzle);
    }

    public function letGo()
    {
        $this->filesystem->remove($this->directory);
    }

    // @codingStandardsIgnoreLine
    function it_executes_with_success(
        StepExecution $execution,
        $dispatcher,
        $respository,
        JobParameters $jobParameters,
        BatchStatus $status,
        ExitStatus $exitStatus,
        Client $guzzle,
        Response $response,
        StreamInterface $body
    ) {
        $jobParameters->get('endpoint')->willReturn('myendpointfortest');
        $jobParameters->get('exportDir')->willReturn($this->directory);
        $jobParameters->get('secretKey')->willReturn('abc');
        $jobParameters->get('applicationId')->willReturn('999');

        $file = $this->directory.'export.zip';
        $resource = fopen($file, 'w+');

        $execution->getJobParameters()->willReturn($jobParameters);
        $execution->getJobExecution()->willReturn($execution);

        # before
        $execution->getStatus()->willReturn($status);
        $status->getValue()->willReturn(BatchStatus::STARTING);
        $dispatcher->dispatch(EventInterface::BEFORE_STEP_EXECUTION, Argument::any())->shouldBeCalled();
        $execution->setStartTime(Argument::any())->shouldBeCalled();
        $execution->setStatus(Argument::any())->shouldBeCalled();
        $execution->upgradeStatus(Argument::any())->shouldBeCalled();

        # my step logic assertions
        $execution->addSummaryInfo('endpoint', 'myendpointfortest999')->shouldBeCalled();
        $execution->addSummaryInfo('response_code', '204')->shouldBeCalled();
        $execution->addSummaryInfo('response_body', 'data received')->shouldBeCalled();

        $response->getStatusCode()->willReturn(204);

        $body->getContents()->willReturn('data received');
        $response->getBody()->willReturn($body);
        $guzzle->request('POST', 'myendpointfortest999', Argument::any())->willReturn($response);

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

        //remove file after test
        unlink($file);
    }
}
