<?php

namespace Snowio\Bundle\CsvConnectorBundle\Step;

use Akeneo\Component\Batch\Step\AbstractStep;
use Akeneo\Component\Batch\Model\StepExecution;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Akeneo\Component\Batch\Job\JobRepositoryInterface;
use GuzzleHttp\Client;

class PostStep extends AbstractStep
{
    /** @var ClientInterface */
    protected $guzzle;

    /**
     * @param string                   $name
     * @param EventDispatcherInterface $eventDispatcher
     * @param JobRepositoryInterface   $jobRepository
     * @param Client                   $guzzle
     */
    public function __construct(
        $name,
        EventDispatcherInterface $eventDispatcher,
        JobRepositoryInterface $jobRepository,
        Client $guzzle
    ) {
        $this->name = $name;
        $this->jobRepository = $jobRepository;
        $this->eventDispatcher = $eventDispatcher;
        $this->guzzle = $guzzle;
    }

    /**
     * Post Step will send the zip file generated on preview step
     * to Snow.io
     *
     * @author Nei Rauni <nr@amp.co>
     * @param StepExecution $stepExecution
     */
    protected function doExecute(StepExecution $stepExecution)
    {
        $jobParameters = $stepExecution->getJobParameters();
        $endpoint = $jobParameters->get('endpoint') . $jobParameters->get('applicationId');

        $stepExecution->addSummaryInfo('endpoint', $endpoint);

        $zipFile = rtrim($jobParameters->get('exportDir'), '/') . DIRECTORY_SEPARATOR . ArchiveStep::ZIP_FILE_NAME;

        if (!file_exists($zipFile)) {
            $stepExecution->addFailureException(
                new \RuntimeException('Failed to open file '.$zipFile.' to send to snow.io')
            );
        }

        if (($resource = fopen($zipFile, 'r')) === false) {
            $stepExecution->addFailureException(
                new \RuntimeException('Failed to open file '.$zipFile.' to send to snow.io')
            );
        }

        $response = $this->guzzle->request(
            'POST',
            $endpoint,
            [
                'body'      => $resource,
                'headers'   => [
                    'Content-Type'          => 'application/zip',
                    'Authorization'         => $jobParameters->get('secretKey'),
                ],
            ]
        );

        if ($response->getStatusCode() !== 204) {
            $stepExecution->addFailureException(new \Exception('Failed to POST CSV file: ' . $response->getBody()));
        }

        $stepExecution->addSummaryInfo('response_code', $response->getStatusCode());
        $stepExecution->addSummaryInfo('response_body', $response->getBody()->getContents());
    }
}
