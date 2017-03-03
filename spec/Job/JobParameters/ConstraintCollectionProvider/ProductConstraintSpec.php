<?php

namespace spec\Snowio\Bundle\CsvConnectorBundle\Job\JobParameters\ConstraintCollectionProvider;

use PhpSpec\ObjectBehavior;
use Akeneo\Component\Batch\Job\JobParameters\ConstraintCollectionProviderInterface;
use Symfony\Component\Validator\Constraints\Collection;
use Akeneo\Component\Batch\Job\JobInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProductConstraintSpec extends ObjectBehavior
{
    public function let(
        ConstraintCollectionProviderInterface $provider
    ) {
        $this->beConstructedWith(
            $provider,
            ['test11','test22']
        );
    }

    // @codingStandardsIgnoreLine
    public function it_is_a_default_values()
    {
        $this->shouldImplement('Akeneo\Component\Batch\Job\JobParameters\ConstraintCollectionProviderInterface');
    }

    // @codingStandardsIgnoreLine
    public function it_supports_a_job(JobInterface $job)
    {
        $job->getName()->willReturn('test11');
        $this->supports($job)->shouldReturn(true);
    }

    // @codingStandardsIgnoreLine
    public function it_provides_constraints_collection(
        $provider,
        Collection $decoratedCollection
    ) {
        $provider->getConstraintCollection()->willReturn($decoratedCollection);
        $collection = $this->getConstraintCollection();
        $collection->shouldReturnAnInstanceOf('Symfony\Component\Validator\Constraints\Collection');
        $fields = $collection->fields;

        $fields->shouldHaveCount(8);
        $fields->shouldHaveKey('decimalSeparator');
        $fields->shouldHaveKey('dateFormat');
        $fields->shouldHaveKey('with_media');
        $fields->shouldHaveKey('endpoint');
        $fields->shouldHaveKey('applicationId');
        $fields->shouldHaveKey('secretKey');
        $fields->shouldHaveKey('exportDir');
    }
}
