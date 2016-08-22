<?php

namespace Snowio\Bundle\CsvConnectorBundle\Writer;

trait FileWriterOverriderTrait
{
    protected $exportDir;

    protected abstract function fileName();

    private function overrideGetConfigurationFields(array &$fields)
    {
        $fields['exportDir'] = [
            'options' => [
                'label' => 'snowio_connector.form.exportDir.label',
                'help'  => 'snowio_connector.form.exportDir.help'
            ]
        ];

        unset($fields['filePath']);
    }

    public function getExportDir()
    {
        return $this->exportDir;
    }

    public function setExportDir($exportDir)
    {
        $this->exportDir = $exportDir;
        $this->filePath = rtrim($exportDir, '/') . DIRECTORY_SEPARATOR . "{$this->fileName()}.csv";
    }
}
