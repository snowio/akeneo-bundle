<?php

namespace Snowio\Bundle\CsvConnectorBundle\Job\JobParameters\FormConfigurationProvider;

trait FormConfigurationTrait
{
    /**
     * Extend with configuration to our fields and remove filePath
     *
     * @author Nei Santos <ns@amp.co>
     * @return Collection
     */
    public function getFormConfiguration()
    {
        $simpleConfiguration = parent::getFormConfiguration();

        unset($simpleConfiguration['filePath']);

        return array_merge([
            'endpoint'          => [
                'type'          => 'text',
                'options'       => [
                    'label'     => 'snowio_connector.export.csv.endpoint.label',
                    'help'      => 'snowio_connector.export.csv.endpoint.help',
                ]
            ],
            'applicationId'     => [
                'type'          => 'text',
                'options'       => [
                    'label'     => 'snowio_connector.export.csv.applicationId.label',
                    'help'      => 'snowio_connector.export.csv.applicationId.help',
                ]
            ],
            'secretKey'         => [
                'type'          => 'text',
                'options'       => [
                    'label'     => 'snowio_connector.export.csv.secretKey.label',
                    'help'      => 'snowio_connector.export.csv.secretKey.help',
                ]
            ],
            'exportDir'         => [
                'type'          => 'text',
                'options'       => [
                    'label'     => 'snowio_connector.export.csv.exportDir.label',
                    'help'      => 'snowio_connector.export.csv.exportDir.help',
                ]
            ],
        ], $simpleConfiguration);
    }
}
