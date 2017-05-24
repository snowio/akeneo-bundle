<?php

namespace Snowio\Bundle\CsvConnectorBundle\Step;

use Akeneo\Component\Batch\Job\JobRepositoryInterface;
use Akeneo\Component\Batch\Model\StepExecution;
use Akeneo\Component\Batch\Step\AbstractStep;
use Pim\Component\Catalog\Model\AbstractProduct;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class CheckThresholdsStep extends AbstractStep
{
    /** @var int */
    private $minimumProductsThreshold;

    /** @var \Pim\Component\Connector\Reader\Database\ProductReader  */
    private $productReader;

    /**
     * CheckThresholdsStep constructor.
     * @param string $name
     * @param EventDispatcherInterface $eventDispatcher
     * @param JobRepositoryInterface $jobRepository
     * @param \Pim\Component\Connector\Reader\Database\ProductReader $productReader
     * @param $minimumProductsThreshold
     */
    public function __construct(
        $name,
        EventDispatcherInterface $eventDispatcher,
        JobRepositoryInterface $jobRepository,
        \Pim\Component\Connector\Reader\Database\ProductReader $productReader,
        $minimumProductsThreshold
    ) {
        parent::__construct($name, $eventDispatcher, $jobRepository);
        $this->minimumProductsThreshold = (int)$minimumProductsThreshold;
        $this->productReader = $productReader;
    }

    /**
     * Extension point for subclasses to execute business logic. Subclasses should set the {@link ExitStatus} on the
     * {@link StepExecution} before returning.
     *
     * Do not catch exception here. It will be correctly handled by the execute() method.
     *
     * @param StepExecution $stepExecution the current step context
     *
     * @throws \Exception
     */
    protected function doExecute(StepExecution $stepExecution)
    {
        $stepExecution->addSummaryInfo('minimum_products_threshold', $this->minimumProductsThreshold);

        if ($this->minimumProductsThreshold > 0 && !$this->isProductCountAboveMinimumThreshold($stepExecution)) {
            throw new \Exception(
                sprintf(
                    'Error - attempted to export fewer products than the minimum threshold (%s).',
                    $this->minimumProductsThreshold
                )
            );
        }
    }

    /**
     * @param StepExecution $stepExecution
     * @return bool
     * @author James Pollard <jp@amp.co>
     */
    private function isProductCountAboveMinimumThreshold(StepExecution $stepExecution)
    {
        $this->productReader->setStepExecution($stepExecution);
        $this->productReader->initialize();

        for ($i = 0; $i < $this->minimumProductsThreshold; $i++) {
            $product = $this->productReader->read();
            if (!($product instanceof AbstractProduct)) {
                return false;
            }
        }

        return true;
    }
}
