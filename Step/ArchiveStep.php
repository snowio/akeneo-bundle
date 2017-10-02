<?php

namespace Snowio\Bundle\CsvConnectorBundle\Step;

use Akeneo\Component\Batch\Step\AbstractStep;
use Akeneo\Component\Batch\Model\StepExecution;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Akeneo\Component\Batch\Job\JobRepositoryInterface;
use Snowio\Bundle\CsvConnectorBundle\MediaExport;
use \ZipArchive;

class ArchiveStep extends AbstractStep
{
    const ZIP_FILE_NAME = 'export.zip';

    /** @var ZipArchive */
    protected $zip;

    /** @var \Snowio\Bundle\CsvConnectorBundle\MediaExport\Logger */
    private $mediaExportLogger;

    /**
     * @param string                   $name
     * @param EventDispatcherInterface $eventDispatcher
     * @param JobRepositoryInterface   $jobRepository
     * @param ZipArchive               $zip
     * @param string                   $logDir
     */
    public function __construct(
        $name,
        EventDispatcherInterface $eventDispatcher,
        JobRepositoryInterface $jobRepository,
        ZipArchive $zip,
        MediaExport\Logger $mediaExportLogger
    ) {
        $this->name = $name;
        $this->jobRepository = $jobRepository;
        $this->eventDispatcher = $eventDispatcher;
        $this->zip = $zip;
        $this->mediaExportLogger = $mediaExportLogger;
    }

    /**
     * Archive will Zip the csv files on the specified directory
     * and the metada.json file.
     *
     * @param StepExecution    $stepExecution
     */
    protected function doExecute(StepExecution $stepExecution)
    {
        $jobParameters = $stepExecution->getJobParameters();

        $location = rtrim($jobParameters->get('exportDir'), '/') . DIRECTORY_SEPARATOR . self::ZIP_FILE_NAME;

        $opened = $this->zip->open($location, ZipArchive::CREATE);

        if ($opened !== true) {
            $stepExecution->addFailureException(new \RuntimeException('Failed to open zip, reason code:' . $opened));
        } else {
            $this->zip->addFile(
                $this->mediaExportLogger->getLogFileNameForJob($stepExecution->getJobExecution()->getId()),
                '/media_export.log'
            );

            $success = $this->zip->addPattern(
                '/(?:\w+\.csv|metadata.json)/',
                $jobParameters->get('exportDir'),
                ['add_path' => '/', 'remove_all_path' => true]
            );

            if (!$success) {
                $stepExecution->addFailureException(new \RuntimeException('Failed to add files to zip.'));
                return;
            }

            $this->zip->close();

            $stepExecution->addSummaryInfo('zip_location', $location);
        }
    }
}
