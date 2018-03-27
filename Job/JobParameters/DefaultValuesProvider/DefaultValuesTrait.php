<?php

namespace Snowio\Bundle\CsvConnectorBundle\Job\JobParameters\DefaultValuesProvider;

trait DefaultValuesTrait
{
    /**
     * Extend with default values to our fields and remove filePath
     *
     * @author Nei Santos <ns@amp.co>
     * @return Collection
     */
    public function getDefaultValues()
    {
        $simpleDefaults = parent::getDefaultValues();

        unset($simpleDefaults['filePath']);

        return array_merge([
            'endpoint'      => '',
            'applicationId' => '',
            'secretKey'     => '',
            'exportDir'     => '',
            'rsyncDirectory'=> '',
            'rsyncUser'     => '',
            'rsyncHost'     => '',
            'rsyncOptions'  => [],
        ], $simpleDefaults);
    }
}
