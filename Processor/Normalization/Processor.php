<?php

namespace Snowio\Bundle\CsvConnectorBundle\Processor\Normalization;

use Akeneo\Component\Batch\Item\AbstractConfigurableStepElement;
use Akeneo\Component\Batch\Item\ItemProcessorInterface;
use Akeneo\Component\Batch\Model\StepExecution;
use Akeneo\Component\Batch\Step\StepExecutionAwareInterface;
use Pim\Component\Catalog\Repository\LocaleRepositoryInterface;
use Symfony\Component\Serializer\Serializer;
use UnexpectedValueException;

class Processor extends AbstractConfigurableStepElement implements
    ItemProcessorInterface,
    StepExecutionAwareInterface
{
    /** @var StepExecution */
    protected $stepExecution;

    /** @var Serializer */
    protected $serializer;

    /** @var LocaleRepositoryInterface */
    protected $localeRepository;

    /** @var string  */
    protected $format;

    public function __construct(Serializer $serializer, LocaleRepositoryInterface $localeRepository, $format)
    {
        $this->serializer       = $serializer;
        $this->localeRepository = $localeRepository;
        $this->format = $format;
    }

    public function process($item)
    {
        if (!$this->serializer->supportsEncoding($this->format)) {
            throw new UnexpectedValueException(sprintf('Serialization for the format %s is not supported', $this->format));
        }

        $context = ['locales' => $this->localeRepository->getActivatedLocaleCodes()];
        $item = $this->serializer->normalize($item, $this->format, $context);

        return $item;
    }

    public function getConfigurationFields()
    {
        return [];
    }

    public function setStepExecution(StepExecution $stepExecution)
    {
        $this->stepExecution = $stepExecution;
    }
}
