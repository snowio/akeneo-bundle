<?php

namespace Snowio\Bundle\CsvConnectorBundle\Job\JobParameters\ConstraintCollectionProvider;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Uuid;
use Pim\Component\Catalog\Validator\Constraints\WritableDirectory;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Collection;

trait ConstraintTrait
{
    /**
     * Extend with our fields and remove filePath
     *
     * @author Nei Santos <ns@amp.co>
     * @return Collection
     */
    public function getConstraintCollection()
    {
        $constraintFields = parent::getConstraintCollection();
        $simpleFields = $constraintFields->fields;

        unset($simpleFields['filePath']);

        return new Collection(['fields' => array_merge([
            'endpoint'      => [
                new NotBlank(['groups' => ['Default', 'FileConfiguration']]),
                new Regex([
                    'pattern' => '/^http(.*)\/$/',
                    'message' => 'The endpoint should be an http url ending with slash. (http://localhost/)'
                ])
            ],
            'applicationId' => [
                new Uuid(['groups' => ['Default', 'FileConfiguration']]),
                new NotBlank(['groups' => ['Default', 'FileConfiguration']])
            ],
            'secretKey'     => new NotBlank(['groups' => ['Default', 'FileConfiguration']]),
            'exportDir'     => [
                new NotBlank(['groups' => ['Default', 'Execution', 'FileConfiguration']]),
                new WritableDirectory(['groups' => ['Execution', 'FileConfiguration']]),
            ],
            'rsyncDirectory'     => [],
            'rsyncUser'     => [],
            'rsyncHost'     => [],
            'rsyncOptions'  => [],
        ], $simpleFields)]);
    }
}
