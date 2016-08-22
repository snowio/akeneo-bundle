<?php

namespace Snowio\Bundle\CsvConnectorBundle\Handler;

use Akeneo\Component\Batch\Item\AbstractConfigurableStepElement;
use Akeneo\Component\Batch\Model\StepExecution;
use Akeneo\Component\Batch\Step\StepExecutionAwareInterface;

class ArchiveHandler extends AbstractConfigurableStepElement implements StepExecutionAwareInterface
{
    const ZIP_FILE_NAME = 'export.zip';

    /** @var string */
    protected $exportDir;
    /** @var StepExecution */
    private $stepExecution;

    public function execute()
    {
        $zip = new \ZipArchive();
        $location = rtrim($this->exportDir, '/') . DIRECTORY_SEPARATOR . self::ZIP_FILE_NAME;
        $opened = $zip->open($location, \ZipArchive::CREATE);
        if ($opened !== true) {
            $this->stepExecution->addFailureException(new \RuntimeException('Failed to open zip, reason code:' . $opened));
        } else {
            $success = $zip->addPattern(
                '/(?:\w+\.csv|metadata.json)/',
                $this->exportDir,
                ['add_path' => '/', 'remove_all_path' => true]
            );
            if (!$success) {
                $this->stepExecution->addFailureException(new \RuntimeException('Failed to add files to zip.'));
            }
            $zip->close();
            $this->stepExecution->addSummaryInfo('zip_location', $location);
        }
    }

    public function getConfigurationFields()
    {
        return [
            'exportDir' => [
                'options' => [
                    'label' => 'snowio_connector.form.exportDir.label',
                    'help'  => 'snowio_connector.form.exportDir.help'
                ]
            ]
        ];
    }

    public function setStepExecution(StepExecution $stepExecution)
    {
        $this->stepExecution = $stepExecution;
    }

    public function getExportDir()
    {
        return $this->exportDir;
    }

    public function setExportDir($exportDir)
    {
        $this->exportDir = $exportDir;
    }
}
