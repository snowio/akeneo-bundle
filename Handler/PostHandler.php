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
    protected $filePath;
    /** @var string */
    protected $handlerType;
    /** @var string */
    protected $applicationId;
    /** @var string */
    protected $secretKey;
    /** @var \Snowio\Bundle\CsvConnectorBundle\Source\HandlerType */
    protected $handlerTypeSource;
    protected $resolvedFilePath;
    /** @var \GuzzleHttp\Client */
    protected $client;

    /**
     * PostHandler constructor.
     * @param \GuzzleHttp\ClientInterface $client
     * @param \Snowio\Bundle\CsvConnectorBundle\Source\HandlerType $handlerTypeSource
     * @author Cristian Quiroz <cq@amp.co>
     */
    public function __construct(ClientInterface $client, $handlerTypeSource)
    {
        $this->client = $client;
        $this->handlerTypeSource = $handlerTypeSource;
    }

    /**
     * @throws \Exception
     * @author Cristian Quiroz <cq@amp.co>
     */
    public function execute()
    {
        $url = $this->getUrl() . $this->getApplicationId();
        $this->stepExecution->addSummaryInfo('url', $url);

        $resource = fopen($this->getFilePath(), 'r');

        $response = $this->client->request(
            'POST',
            $url,
            [
                'body'      => $resource,
                'headers'   => [
                    'Content-Type'          => 'text/csv',
                    'SnowIO-Resource-Id'    => $this->getHandlerType(),
                    'Authorization'         => $this->getSecretKey(),
                ],
            ]
        );

        $this->stepExecution->addSummaryInfo('response_code', $response->getStatusCode());
        $this->stepExecution->addSummaryInfo('response_body', $response->getBody());

        if ($response->getStatusCode() !== 200) {
//             Unexpected response, handle
            throw new \Exception('Failed to POST csv data: ' . $response->getBody());
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
     * @param string $filePath
     * @return $this
     * @author Cristian Quiroz <cq@amp.co>
     */
    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;

        return $this;
    }

    /**
     * @return string
     * @author Cristian Quiroz <cq@amp.co>
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * @param string $handlerType
     * @return $this
     * @author Cristian Quiroz <cq@amp.co>
     */
    public function setHandlerType($handlerType)
    {
        $this->handlerType = $handlerType;

        return $this;
    }

    /**
     * @return string
     * @author Cristian Quiroz <cq@amp.co>
     */
    public function getHandlerType()
    {
        return $this->handlerType;
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
     * TODO: Hide Url, delimiter and enclosure
     * Here, we define the form fields to use
     * @return array
     * @author Cristian Quiroz <cq@amp.co>
     */
    public function getConfigurationFields()
    {
        return [
                'filePath' => [
                    'options' => [
                        'label' => 'pim_connector.export.filePath.label',
                        'help'  => 'pim_connector.export.filePath.help',
                    ],
                ],
                'url' => [
                    'type' => 'text',
                    'required' => true,
                    'options' => [
                        'label' => 'snowio_csv.form.url.label',
                        'help'  => 'snowio_csv.form.url.help'
                    ],
                ],
                'handlerType' => [
                    'type' => 'choice',
                    'options' => [
                        'choices' => $this->getSnowioHandlerTypeChoices(),
                        'required' => true,
                        'label' => 'snowio_csv.form.handlerType.label',
                    ],
                ],
                'applicationId' => [
                    'type' => 'text',
                    'required' => true,
                    'options' => [
                        'label' => 'snowio_csv.form.applicationId.label',
                    ],
                ],
                'secretKey' => [
                    'type' => 'password',
                    'required' => true,
                    'options' => [
                        'label' => 'snowio_csv.form.secretKey.label',
                    ],
                ],
            ];
    }

    /**
     * @return array
     * @author Cristian Quiroz <cq@amp.co>
     */
    private function getSnowioHandlerTypeChoices()
    {
        $handlerTypes = $this->handlerTypeSource->getAllTypes();

        $choices = [];
        foreach ($handlerTypes as $handlerType) {
            $choices[$handlerType] = $handlerType;
        }

        return $choices;
    }
}
