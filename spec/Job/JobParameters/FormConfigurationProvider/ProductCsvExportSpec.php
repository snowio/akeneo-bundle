<?php

namespace spec\Snowio\Bundle\CsvConnectorBundle\Job\JobParameters\FormConfigurationProvider;

use Akeneo\Component\Batch\Job\JobInterface;
use PhpSpec\ObjectBehavior;
use Pim\Bundle\ImportExportBundle\JobParameters\FormConfigurationProviderInterface;

class ProductCsvExportSpec extends ObjectBehavior
{
    public function let(
        FormConfigurationProviderInterface $simpleCsvExport
    ) {
        $this->beConstructedWith(
            $simpleCsvExport,
            ['test999','test777'],
            [','],
            ['yyyy-MM-dd', 'dd/MM/yyyy']
        );
    }

    // @codingStandardsIgnoreLine
    function it_is_a_form_configuration()
    {
        $this->shouldImplement('Pim\Bundle\ImportExportBundle\JobParameters\FormConfigurationProviderInterface');
    }

    // @codingStandardsIgnoreLine
    function it_supports(JobInterface $job)
    {
        $job->getName()->willReturn('test999');
        $this->supports($job)->shouldReturn(true);

        $job->getName()->willReturn('test777');
        $this->supports($job)->shouldReturn(true);
    }

    // @codingStandardsIgnoreLine
    function it_gets_form_configuration($simpleCsvExport)
    {
        $formOptions = [
            'filters' => [
                'type' => 'hidden',
                'options' => [
                    'attr' => [
                        'data-tab' => 'content'
                    ]
                ]
            ],
            'decimalSeparator' => [
                'type'    => 'choice',
                'options' => [
                    'choices'  => [','],
                    'required' => true,
                    'select2'  => true,
                    'label'    => 'pim_connector.export.csv.decimalSeparator.label',
                    'help'     => 'pim_connector.export.csv.decimalSeparator.help'
                ]
            ],
            'dateFormat' => [
                'type'    => 'choice',
                'options' => [
                    'choices'  => ['yyyy-MM-dd', 'dd/MM/yyyy'],
                    'required' => true,
                    'select2'  => true,
                    'label'    => 'pim_connector.export.csv.dateFormat.label',
                    'help'     => 'pim_connector.export.csv.dateFormat.help',
                ]
            ],
            'linesPerFile' => [
                'type'    => 'integer',
                'options' => [
                    'label' => 'pim_connector.export.csv.lines_per_files.label',
                    'help'  => 'pim_connector.export.csv.lines_per_files.help',
                ]
            ],
            'withHeader' => [
                'type'    => 'switch',
                'options' => [
                    'label' => 'pim_connector.export.csv.withHeader.label',
                    'help'  => 'pim_connector.export.csv.withHeader.help'
                ]
            ],
            'with_media' => [
                'type'    => 'switch',
                'options' => [
                    'label' => 'pim_connector.export.csv.with_media.label',
                    'help'  => 'pim_connector.export.csv.with_media.help'
                ]
            ]
        ];

        $exportConfig = [
            'endpoint' => [
                'type'    => 'text',
                'options' => [
                    'label' => 'snowio_connector.export.csv.endpoint.label',
                    'help'  => 'snowio_connector.export.csv.endpoint.help',
                ]
            ],
            'applicationId' => [
                'type'    => 'text',
                'options' => [
                    'label' => 'snowio_connector.export.csv.applicationId.label',
                    'help'  => 'snowio_connector.export.csv.applicationId.help',
                ]
            ],
            'secretKey' => [
                'type'    => 'text',
                'options' => [
                    'label' => 'snowio_connector.export.csv.secretKey.label',
                    'help'  => 'snowio_connector.export.csv.secretKey.help',
                ]
            ],
            'exportDir' => [
                'type'    => 'text',
                'options' => [
                    'label' => 'snowio_connector.export.csv.exportDir.label',
                    'help'  => 'snowio_connector.export.csv.exportDir.help',
                ]
            ]
        ];

        $simpleCsvExport->getFormConfiguration()->willReturn($formOptions);

        $this->getFormConfiguration()->shouldReturn($exportConfig + $formOptions);
    }
}
