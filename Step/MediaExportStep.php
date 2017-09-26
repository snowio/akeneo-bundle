<?php

namespace Snowio\Bundle\CsvConnectorBundle\Step;

use Akeneo\Component\Batch\Job\JobRepositoryInterface;
use Akeneo\Component\Batch\Model\StepExecution;
use Akeneo\Component\Batch\Step\AbstractStep;
use Akeneo\Component\FileStorage\Exception\FileTransferException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

/**
 * This class rsyncs media files to a configurable location and logs some info a separate log file
 * Forcing reconnect to MySQL was looked into as part of this, which is not necessary:
 * @see \Akeneo\Bundle\BatchBundle\Job\DoctrineJobRepository::updateStepExecution
 */
class MediaExportStep extends AbstractStep
{
    /** @var string */
    protected $exportLocation;

    /** @var string */
    private $logFile;

    /**
     * MediaExportStep constructor.
     * @param string $name
     * @param EventDispatcherInterface $eventDispatcher
     * @param JobRepositoryInterface $jobRepository
     * @param $exportLocation
     * @param $logFile
     */
    public function __construct(
        $name,
        EventDispatcherInterface $eventDispatcher,
        JobRepositoryInterface $jobRepository,
        $exportLocation,
        $logFile
    ) {
        parent::__construct($name, $eventDispatcher, $jobRepository);
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
        try {
            $currentExportDir = rtrim($stepExecution->getJobParameters()->get('exportDir'), '/');
            $newExportDir = rtrim($this->exportLocation, '/');

            $stepExecution->addSummaryInfo('log_file', $this->logFile);
            $stepExecution->addSummaryInfo('export_location', $newExportDir);

            $output = $this->syncMedia($currentExportDir, $newExportDir);
            $this->writeLog($this->getModifiedOutputForLog($output, $stepExecution));

            $stepExecution->addSummaryInfo('read', $output[1]);
            $stepExecution->addSummaryInfo('write', $output[2]);
        } catch(\Exception $e) {
            $this->writeLog(['Error - something went wrong during media export.', $e->getMessage()]);
            throw $e;
        }
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
            throw new FileTransferException('Error - rsync failure during media export.' . implode(" : ", $output));
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
            mkdir(dirname($this->logFile), 0644, true);
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
}
