<?php

namespace Snowio\Bundle\CsvConnectorBundle\Step;

use Akeneo\Component\Batch\Model\StepExecution;
use Akeneo\Component\Batch\Item\AbstractConfigurableStepElement;
use Akeneo\Component\Batch\Step\AbstractStep;

class PostStep extends AbstractStep
{

    /** @var AbstractConfigurableStepElement */
    protected $handler;

    /**
     * @param \Akeneo\Component\Batch\Model\StepExecution $stepExecution
     * @author Cristian Quiroz <cq@amp.co>
     */
    protected function doExecute(StepExecution $stepExecution)
    {
        // inject the step execution in the step item to be able to log summary info during execution
        $this->handler->setStepExecution($stepExecution);
        $this->handler->execute();
    }

    /**
     * As step configuration, we merge the step items configuration
     * @return array
     * @author Cristian Quiroz <cq@amp.co>
     */
    public function getConfiguration()
    {
        $configuration = array();
        foreach ($this->getConfigurableStepElements() as $stepElement) {
            if ($stepElement instanceof AbstractConfigurableStepElement) {
                foreach ($stepElement->getConfiguration() as $key => $value) {
                    if (!isset($configuration[$key]) || $value) {
                        $configuration[$key] = $value;
                    }
                }
            }
        }

        return $configuration;
    }

    /**
     * We inject the configuration in each step item
     * @param array $config
     * @author Cristian Quiroz <cq@amp.co>
     */
    public function setConfiguration(array $config)
    {
        foreach ($this->getConfigurableStepElements() as $stepElement) {
            if ($stepElement instanceof AbstractConfigurableStepElement) {
                $stepElement->setConfiguration($config);
            }
        }
    }

    /**
     * These getter / setter are required to allow to configure from form and execute
     * @return \Akeneo\Component\Batch\Step\StepExecutionAwareInterface
     * @author Cristian Quiroz <cq@amp.co>
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * @param \Akeneo\Component\Batch\Step\StepExecutionAwareInterface $handler
     * @author Cristian Quiroz <cq@amp.co>
     */
    public function setHandler($handler)
    {
        $this->handler = $handler;
    }

    /**
     * Step items which are configurable with the job edit form
     * @return array
     * @author Cristian Quiroz <cq@amp.co>
     */
    public function getConfigurableStepElements()
    {
        return [
            'handler' => $this->getHandler(),
        ];
    }
}
