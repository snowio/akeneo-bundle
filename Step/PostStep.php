<?php

namespace Snowio\Bundle\CsvConnectorBundle\Step;

use Akeneo\Component\Batch\Model\StepExecution;
use Akeneo\Component\Batch\Item\AbstractConfigurableStepElement;
use Akeneo\Component\Batch\Step\AbstractStep;
use Snowio\Bundle\CsvConnectorBundle\Handler\PostHandler;

class PostStep extends AbstractStep
{
    /** @var  PostHandler */
    protected $post;

    /**
     * @param \Akeneo\Component\Batch\Model\StepExecution $stepExecution
     * @author Cristian Quiroz <cq@amp.co>
     */
    protected function doExecute(StepExecution $stepExecution)
    {
        // inject the step execution in the step item to be able to log summary info during execution
        $this->post->setStepExecution($stepExecution);
        $this->post->execute();
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
    public function getPost()
    {
        return $this->post;
    }

    /**
     * @param \Akeneo\Component\Batch\Step\StepExecutionAwareInterface $post
     * @author Cristian Quiroz <cq@amp.co>
     */
    public function setPost($post)
    {
        $this->post = $post;
    }

    /**
     * Step items which are configurable with the job edit form
     * @return array
     * @author Cristian Quiroz <cq@amp.co>
     */
    public function getConfigurableStepElements()
    {
        return [
            'post' => $this->getPost(),
        ];
    }
}
