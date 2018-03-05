<?php

namespace Snowio\Bundle\CsvConnectorBundle\Handler;

use Akeneo\Component\Batch\Model\StepExecution;
use Akeneo\Component\Batch\Step\StepExecutionAwareInterface;
use Akeneo\Component\Batch\Item\AbstractConfigurableStepElement;
use GuzzleHttp\ClientInterface;

class PostHandler extends AbstractConfigurableStepElement implements StepExecutionAwareInterface
{
    /** @var StepExecution */
    protected $stepExecution;
    /** @var string */
    protected $url;
    /** @var string */
    protected $exportDir;
    /** @var string */
    protected $applicationId;
    /** @var string */
    protected $secretKey;
    /** @var \GuzzleHttp\Client */
    protected $client;

    /**
     * PostHandler constructor.
     * @param \GuzzleHttp\ClientInterface $client
     * @author Cristian Quiroz <cq@amp.co>
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @throws \Exception
     * @author Cristian Quiroz <cq@amp.co>
     */
    public function execute()
    {
        $url = $this->getUrl() . $this->getApplicationId();
        $this->stepExecution->addSummaryInfo('url', $url);

        $resource = fopen(rtrim($this->exportDir, '/') . DIRECTORY_SEPARATOR . ArchiveHandler::ZIP_FILE_NAME, 'r');
        
        if (false === $resource) {
            $this->stepExecution->addFailureException(new \RuntimeException('Failed to open file to send to snow.io'));
            return;
        }

        $response = $this->client->request(
            'POST',
            $url,
            [
                'body'      => $resource,
                'headers'   => [
                    'Content-Type'          => 'application/zip',
                    'Authorization'         => $this->getSecretKey(),
                ],
            ]
        );

        $this->stepExecution->addSummaryInfo('response_code', $response->getStatusCode());
        $this->stepExecution->addSummaryInfo('response_body', $response->getBody());

        if ($response->getStatusCode() !== 204) {
//             Unexpected response, handle
            $this->stepExecution->addFailureException(new \Exception('Failed to POST csv data: ' . $response->getBody()));
        }

    }

    /**
     * @param \Akeneo\Component\Batch\Model\StepExecution $stepExecution
     * @author Cristian Quiroz <cq@amp.co>
     */
    public function setStepExecution(StepExecution $stepExecution)
    {
        $this->stepExecution = $stepExecution;
    }

    /**
     * @param string $exportDir
     * @return $this
     * @author Cristian Quiroz <cq@amp.co>
     */
    public function setExportDir($exportDir)
    {
        $this->exportDir = $exportDir;

        return $this;
    }

    /**
     * @return string
     * @author Cristian Quiroz <cq@amp.co>
     */
    public function getExportDir()
    {
        return $this->exportDir;
    }

    /**
     * @return string
     */
    public function getApplicationId()
    {
        return $this->applicationId;
    }

    /**
     * @param string $applicationId
     * @return $this
     */
    public function setApplicationId($applicationId)
    {
        $this->applicationId = $applicationId;

        return $this;
    }

    /**
     * @return string
     */
    public function getSecretKey()
    {
        return $this->secretKey;
    }

    /**
     * @param string $secretKey
     * @return $this
     */
    public function setSecretKey($secretKey)
    {
        $this->secretKey = $secretKey;

        return $this;
    }

    /**
     * @param $url
     * @return $this
     * @author Cristian Quiroz <cq@amp.co>
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return string
     * @author Cristian Quiroz <cq@amp.co>
     */
    public function getUrl()
    {
        return rtrim($this->url, '/') . '/';
    }

    /**
     * Here, we define the form fields to use
     * @return array
     * @author Cristian Quiroz <cq@amp.co>
     */
    public function getConfigurationFields()
    {
        return [
                'exportDir' => [
                    'options' => [
                        'label' => 'snowio_connector.form.exportDir.label',
                        'help'  => 'snowio_connector.form.exportDir.help'
                    ]
                ],
                'url' => [
                    'type' => 'text',
                    'required' => true,
                    'options' => [
                        'label' => 'snowio_connector.form.url.label',
                        'help'  => 'snowio_connector.form.url.help'
                    ],
                ],
                'applicationId' => [
                    'type' => 'text',
                    'required' => true,
                    'options' => [
                        'label' => 'snowio_connector.form.applicationId.label',
                    ],
                ],
                'secretKey' => [
                    'type' => 'password',
                    'required' => true,
                    'options' => [
                        'label' => 'snowio_connector.form.secretKey.label',
                    ],
                ],
            ];
    }
}
