<?php

namespace Snowio\Bundle\CsvConnectorBundle\Step;

use Akeneo\Component\Batch\Job\JobRepositoryInterface;
use Akeneo\Component\Batch\Model\StepExecution;
use Akeneo\Component\Batch\Step\AbstractStep;
use Akeneo\Component\FileStorage\Exception\FileTransferException;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

class MediaExportStep extends AbstractStep
{
    /** @var string */
    protected $exportLocation;

    /** @var \Doctrine\DBAL\Connection */
    private $connection;

    /** @var string */
    private $logFile;

    /**
     * MediaExportStep constructor.
     * @param string $name
     * @param EventDispatcherInterface $eventDispatcher
     * @param JobRepositoryInterface $jobRepository
     * @param EntityManager $entityManager
     * @param $exportLocation
     * @param $logFile
     */
    public function __construct(
        $name,
        EventDispatcherInterface $eventDispatcher,
        JobRepositoryInterface $jobRepository,
        EntityManager $entityManager,
        $exportLocation,
        $logFile
    ) {
        parent::__construct($name, $eventDispatcher, $jobRepository);
        $this->connection = $entityManager->getConnection();
        $this->exportLocation = $exportLocation;
        $this->logFile = $logFile;
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
        $currentExportDir = rtrim($stepExecution->getJobParameters()->get('exportDir'), '/');
        $newExportDir = rtrim($this->exportLocation, '/');

        $output = $this->syncMedia($currentExportDir, $newExportDir);
        $this->writeLog($this->getModifiedOutputForLog($output, $stepExecution));

        $this->forceReconnect();

        $stepExecution->addSummaryInfo('log_file', $this->logFile);
        $stepExecution->addSummaryInfo('export_location', $newExportDir);
    }

    /**
     * @param $currentExportDir
     * @param $newExportDir
     * @return array
     * @throws FileTransferException
     * @author James Pollard <jp@amp.co>
     */
    protected function syncMedia($currentExportDir, $newExportDir)
    {
        exec("rsync -ah --stats $currentExportDir/ $newExportDir/", $output, $status);

        if ($status !== 0) {
            throw new FileTransferException('Error - rsync failure during media export.');
        }

        return $output;
    }

    /**
     * @param array $content
     * @author James Pollard <jp@amp.co>
     */
    protected function writeLog(array $content)
    {
        if (!is_dir(dirname($this->logFile))) {
            mkdir(dirname($this->logFile), 0777, true);
        }

        $handle = fopen($this->logFile, 'a+');
        if ($handle === false) {
            throw new FileNotFoundException(
                sprintf('Error - log file (%s) could not be opened during media export.', $this->logFile)
            );
        }

        fputcsv($handle, $content, PHP_EOL);
        fclose($handle);
    }

    /**
     * @param array $output
     * @param StepExecution $stepExecution
     * @return array
     * @author James Pollard <jp@amp.co>
     */
    protected function getModifiedOutputForLog(array $output, StepExecution $stepExecution)
    {
        $jobParameters = $stepExecution->getJobParameters();
        $jobExecution = $stepExecution->getJobExecution();

        array_unshift(
            $output,
            '------------------------------',
            sprintf('Export Profile: %s (%s)', $jobExecution->getLabel(), $jobParameters->get('applicationId')),
            sprintf('Execution ID: %s', $jobExecution->getId()),
            date('d/m/Y H:i:s')
        );

        return $output;
    }

    /**
     * @throws DBALException
     * @author James Pollard <jp@amp.co>
     */
    private function forceReconnect()
    {
        $this->connection->connect();
        if (!$this->connection->isConnected()) {
            throw new DBALException('Error - MySQL connection lost during media export.');
        }
    }
}
