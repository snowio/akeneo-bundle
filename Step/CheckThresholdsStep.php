<?php

namespace Snowio\Bundle\CsvConnectorBundle\Step;

use Akeneo\Component\Batch\Job\JobRepositoryInterface;
use Akeneo\Component\Batch\Model\StepExecution;
use Akeneo\Component\Batch\Step\AbstractStep;
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
        foreach ($this->getProducts($stepExecution) as $index => $product) {
            if ($index >= $this->minimumProductsThreshold - 1) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param StepExecution $stepExecution
     * @return \Generator
     * @author James Pollard <jp@amp.co>
     */
    private function getProducts(StepExecution $stepExecution)
    {
        $this->productReader->setStepExecution($stepExecution);
        $this->productReader->initialize();

        $continueExecution = true;
        while($continueExecution) {

            $product = $this->productReader->read();

            if (!($product instanceof \Pim\Component\Catalog\Model\AbstractProduct)) {
                $continueExecution = false;
                continue;
            }

            yield $product;
        }
    }
}
