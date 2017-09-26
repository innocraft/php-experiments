<?php

use InnoCraft\Experiments\Experiment;
use InnoCraft\Experiments\Filters\CustomFilter;
use InnoCraft\Experiments\Storage\Transient;
use InnoCraft\Experiments\Filters\AlwaysTrigger;
use InnoCraft\Experiments\Filters\NeverTrigger;
use InnoCraft\Experiments\Filters\DefaultFilters;
use InnoCraft\Experiments\Variations;
use InnoCraft\Experiments\Variations\VariationInterface;

class ExperimentTest extends PHPUnit_Framework_TestCase {

    private $experimentName = 'myExperiment';

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage no experimentNameOrId given
     */
    public function test_constructor_throwsException_ifExperimentNameIsNull()
    {
        $this->experimentName = null;
        $this->makeExperiment();
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage no experimentNameOrId given
     */
    public function test_constructor_throwsException_ifExperimentNameIsFalse()
    {
        $this->experimentName = false;
        $this->makeExperiment();
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage no experimentNameOrId given
     */
    public function test_constructor_throwsException_ifExperimentNameIsEmptyString()
    {
        $this->experimentName = '';
        $this->makeExperiment();
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage storage needs to be an instance of StorageInterface
     */
    public function test_constructor_throwsException_ifInvalidStorageGiven()
    {
        $this->makeExperiment($variations = [], $config = ['storage' => 'foobar']);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage filter config needs to be an instance of FilterInterface
     */
    public function test_constructor_throwsException_ifInvalidFilterGiven()
    {
	    $this->makeExperiment($variations = [], $config = ['filter' => 'foobar']);
    }

    public function test_getExperimentName_shouldReturnSetName()
    {
        $this->assertSame('myExperiment', $this->makeExperiment()->getExperimentName());
    }

    public function test_shouldTrigger_shouldUseDefaultFiltersIfNoFilterSetAndAlwaysTriggerIfNoConfigSet()
    {
        $filter = $this->makeExperiment()->getFilter();

        $this->assertTrue($filter instanceof DefaultFilters);
        $this->assertTrue($filter->shouldTrigger());
    }

    public function test_shouldTrigger_shouldNotTrigger_ifNoTrafficIsAllocated()
    {
        // rather integration test to make sure it applies default filters
        $experiment = $this->makeExperiment($variations = [], $config = ['percentage' => 0]);

        $filter = $experiment->getFilter();
        $this->assertTrue($filter instanceof DefaultFilters);
        $this->assertFalse($filter->shouldTrigger());
    }

    public function test_constructor_shouldTrigger_shouldExecutePassedFilter()
    {
        $called = 0;
        $filter = new CustomFilter(function () use (&$called) {
            $called++;
        });
        $experiment = $this->makeExperiment($variations = [], $config = ['filter' => $filter]);

        $this->assertSame($filter, $experiment->getFilter());
        $experiment->getFilter()->shouldTrigger();

        $this->assertSame(1, $called);
    }

    public function test_constructor_addsOriginalVariationIfVariationsIsAnArrayAndOriginalVersionIsNotGiven()
    {
        $experiment = $this->makeExperimentThatNeverTriggers($variations = [['name' => 'variation1']]);

        $variations = $experiment->getVariations();

        $this->assertCount(2, $variations->getVariations());
        $this->assertTrue($variations->get(Experiment::ORIGINAL_VARIATION_NAME) instanceof VariationInterface);
        $this->assertTrue($variations->exists(Experiment::ORIGINAL_VARIATION_NAME));
    }

    public function test_constructor_variationsCanBePassedAsInstnace()
    {
        $variationsInstance = new Variations($this->experimentName, [['name' => 'variation1']]);
        $experiment = $this->makeExperimentThatNeverTriggers($variationsInstance);

        $this->assertSame($variationsInstance, $experiment->getVariations());
    }

    public function test_constructor_doesNotAddsOriginalVariation_IfAlreadyGiven()
    {
        $experiment = $this->makeExperimentThatNeverTriggers($variations = [
            ['name' => 'variation1'], ['name' => Experiment::ORIGINAL_VARIATION_NAME]
        ]);

        $variations = $experiment->getVariations();

        $this->assertCount(2, $variations->getVariations());
        $this->assertTrue($variations->get(Experiment::ORIGINAL_VARIATION_NAME) instanceof VariationInterface);
        $this->assertTrue($variations->exists(Experiment::ORIGINAL_VARIATION_NAME));
    }

    public function test_constructor_doesNotAddsOriginalVariation_IfOrignalIdIsAlreadyGiven()
    {
        $experiment = $this->makeExperimentThatNeverTriggers($variations = [
            ['name' => 'variation1'], ['name' => Experiment::ORIGINAL_VARIATION_ID]
        ]);

        $variations = $experiment->getVariations();

        $this->assertCount(2, $variations->getVariations());
        $this->assertTrue($variations->get(Experiment::ORIGINAL_VARIATION_ID) instanceof VariationInterface);
        $this->assertTrue($variations->exists(Experiment::ORIGINAL_VARIATION_ID));
    }

    public function test_getActivatedVariation_shouldReturnNull_IfNoVariationsSet()
    {
        $experiment = $this->makeExperimentThatAlwaysTriggers(new Variations($this->experimentName, []));
        $this->assertCount(0, $experiment->getVariations()->getVariations());
        $this->assertSame(Experiment::DO_NOT_TRIGGER, $experiment->getActivatedVariation());
    }

    public function test_getActivatedVariation_shouldReturnNull_IfShouldNotBeTriggered()
    {
        $experiment = $this->makeExperimentThatNeverTriggers($variations = [['name' => 'variation1']]);
        $this->assertCount(2, $experiment->getVariations()->getVariations());
        $this->assertSame(Experiment::DO_NOT_TRIGGER, $experiment->getActivatedVariation());
    }

    public function test_getActivatedVariation_shouldSelectARandomVariationAndPersistItInStorage()
    {
        $experiment = $this->makeExperimentThatAlwaysTriggers($variations = [
            ['name' => 'variation1'], ['name' => 'variation2'], ['name' => 'variation3']
        ]);

        $variation = $experiment->getActivatedVariation();
        $this->assertTrue($variation instanceof VariationInterface);

        // verify persisted in storage
        $storedVariationName = $experiment->getStorage()->get('experiment', $experiment->getExperimentName());
        $this->assertSame($variation->getName(), $storedVariationName);

        // on all subsequent calls should always activate same variation
        for ($i = 0; $i < 10; $i++) {
            $this->assertSame($storedVariationName, $experiment->getActivatedVariation()->getName());
        }
    }

    public function test_getActivatedVariation_ifAVariationWasPreviouslySelected_shouldActivateThatVariation()
    {
        $experiment = $this->makeExperimentThatAlwaysTriggers($variations = [
            ['name' => 'variation1'], ['name' => 'variation2'], ['name' => 'variation3']
        ]);
        $variationNameToBeActivated = 'variation3';

        $experiment->getStorage()->set('experiment', $experiment->getExperimentName(), $variationNameToBeActivated);

        // on all subsequent calls should always activate same variation
        for ($i = 0; $i < 10; $i++) {
            $this->assertSame($variationNameToBeActivated, $experiment->getActivatedVariation()->getName());
        }
    }

    public function test_getActivatedVariation_ifAVariationWasPreviouslySelectedButItNoLongerExists_shouldSelectNewVariation()
    {
        $experiment = $this->makeExperimentThatAlwaysTriggers($variations = [
            ['name' => 'variation1'], ['name' => 'variation2'], ['name' => 'variation3']
        ]);
        $variationNameToBeActivated = 'variation10';

        $experiment->getStorage()->set('experiment', $experiment->getExperimentName(), $variationNameToBeActivated);

        $variation = $experiment->getActivatedVariation();
        $this->assertNotSame($variationNameToBeActivated, $variation->getName());

        // verify storage was updated
        $storedVariationName = $experiment->getStorage()->get('experiment', $experiment->getExperimentName());
        $this->assertSame($variation->getName(), $storedVariationName);
    }

    public function test_forceVariationName_willMakeSureThisVersionGetsActivated()
    {
        // we add 100 variations with original version, so each variation has a one percent change to get activated
        // it would be unlikely that it always activated by chance variation43 so we can be sure it did force it
        $variations = array();
        for ($i = 0; $i < 99; $i++) {
            $variations[] = ['name' => 'variation' . $i];
        }
        $experiment = $this->makeExperimentThatAlwaysTriggers($variations);

        $variationNameToBeActivated = 'variation43';
        $experiment->forceVariationName($variationNameToBeActivated);

        $storedVariationName = $experiment->getStorage()->get('experiment', $experiment->getExperimentName());
        $this->assertSame($variationNameToBeActivated, $storedVariationName);

        $this->assertSame($variationNameToBeActivated, $experiment->getActivatedVariation()->getName());
    }

    private function makeExperimentThatAlwaysTriggers($variations = [], $config = [])
    {
        $config['filter'] = new AlwaysTrigger();

        return $this->makeExperiment($variations, $config);
    }

    private function makeExperimentThatNeverTriggers($variations = [], $config = [])
    {
        $config['filter'] = new NeverTrigger();

        return $this->makeExperiment($variations, $config);
    }

    private function makeExperiment($variations = [], $config = [])
    {
        if (!isset($config['storage'])) {
            $config['storage'] = new Transient();
        }
        return new Experiment($this->experimentName, $variations, $config);
    }

    /**
     * TESTS FOR STATIC METHODS BELOW
     */

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage tracker does not implement the doTrackEvent method
     */
    public function test_trackVariationActivation_shouldThrowAnException_IfNoTrackerGiven()
    {
        $experiment = $this->makeExperimentThatAlwaysTriggers();
        $experiment->trackVariationActivation($tracker = null);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage tracker does not implement the doTrackEvent method
     */
    public function test_trackVariationActivation_shouldThrowAnException_IfTrackerDoesNotImplemntDoTrackEventMethod()
    {
        $tracker = new stdClass();

        $experiment = $this->makeExperimentThatAlwaysTriggers();
        $experiment->trackVariationActivation($tracker);
    }

    public function test_trackVariationActivation_callsDoTrackEventMethod_IfTrackerIsGiven()
    {
        $tracker = $this->getMockBuilder('MyTracker')->setMethods(['doTrackEvent'])->getMock();
        $tracker->expects($this->once())
            ->method('doTrackEvent')
            ->with($this->equalTo('abtesting'), $this->equalTo('myExperiment'), $this->equalTo('myVariation'));

        $experiment = $this->makeExperimentThatAlwaysTriggers([['name' => 'myVariation']]);
        $experiment->forceVariationName('myVariation');
        $experiment->trackVariationActivation($tracker);
    }

    public function test_getTrackingScript()
    {
        $experiment = $this->makeExperimentThatAlwaysTriggers([['name' => 'myVariation']]);
        $script = $experiment->getTrackingScript($experiment->getExperimentName(), 'myVariation');
        $this->assertSame('<script type="text/javascript">_paq.push(["AbTesting::enter", {experiment: "myExperiment", variation: "myVariation"}]);</script>', $script);
    }

    public function test_getRandomInt()
    {
        for ($i = 0; $i < 100; $i++) {
            $val = Experiment::getRandomInt(1, 2);
            $this->assertLessThanOrEqual(2, $val);
            $this->assertGreaterThanOrEqual(1, $val);

            $val = Experiment::getRandomInt(0, 99);
            $this->assertLessThanOrEqual(99, $val);
            $this->assertGreaterThanOrEqual(0, $val);
        }
    }
}