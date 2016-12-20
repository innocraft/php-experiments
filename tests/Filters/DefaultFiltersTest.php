<?php

use InnoCraft\Experiments\Filters\DefaultFilters;
use InnoCraft\Experiments\Filters\IsInTestGroup;
use InnoCraft\Experiments\Storage\Transient;
use InnoCraft\Experiments\Filters\ScheduledDate;
use InnoCraft\Experiments\Filters\CustomFilter;
use InnoCraft\Experiments\Filters\AlwaysTrigger;
use InnoCraft\Experiments\Filters\NeverTrigger;

class FiltersTest extends PHPUnit_Framework_TestCase {

    /**
     * @var Transient
     */
    private $storage;

    public function setUp()
    {
        $this->storage = new Transient;
    }

    public function test_constructor_shouldAddDefaultFilters()
    {
        $filters = $this->makeFilter($config = array())->getFilters();
        $this->assertCount(2, $filters);
        $this->assertTrue($filters[0] instanceof IsInTestGroup);
        $this->assertTrue($filters[1] instanceof ScheduledDate);
    }

    public function test_constructor_shouldAddOptionallyAddCustomCallable()
    {
        $filters = $this->makeFilter($config = array('customFilter' => function () {}))->getFilters();
        $this->assertCount(3, $filters);
        $this->assertTrue($filters[0] instanceof IsInTestGroup);
        $this->assertTrue($filters[1] instanceof ScheduledDate);
        $this->assertTrue($filters[2] instanceof CustomFilter);
    }

    public function test_constructor_shouldAddOptionallyAddCustomFilter()
    {
        $filters = $this->makeFilter($config = array('customFilter' => new AlwaysTrigger()))->getFilters();
        $this->assertCount(3, $filters);
        $this->assertTrue($filters[0] instanceof IsInTestGroup);
        $this->assertTrue($filters[1] instanceof ScheduledDate);
        $this->assertTrue($filters[2] instanceof AlwaysTrigger);
    }

    public function test_shouldTrigger_IfNoFiltersGiven()
    {
	    $this->assertShouldTriggerConfig(array());
    }

    public function test_shouldNeverTrigger_ifPercentageIsSetToZeroAndNobodyParticipates()
    {
	    $this->assertNotShouldTriggerConfig(array('percentage' => 0));
    }

    public function test_shouldTrigger_ifPercentageIsSetToMaxAndAllParticipate()
    {
	    $this->assertShouldTriggerConfig(array('percentage' => IsInTestGroup::MAX_PERCENTAGE));
    }

    public function test_shouldNeverTrigger_ifCurrentDateIsNotBetweenStartAndEndDate()
    {
	    $this->assertNotShouldTriggerConfig(array('currentDate' => 'yesterday', 'startDate' => 'now', 'endDate' => 'tomorrow'));
    }

    public function test_shouldTrigger_ifNowIsBetweenStartAndEnd()
    {
	    $this->assertShouldTriggerConfig(array('currentDate' => 'now', 'startDate' => 'yesterday', 'endDate' => 'tomorrow'));
    }

    public function test_shouldTrigger_IfAnyFilterIsFalse_shouldReturnFalse()
    {
        $config = array(
            'percentage' => IsInTestGroup::MAX_PERCENTAGE,
            'currentDate' => 'now', 'startDate' => 'yesterday', 'endDate' => 'tomorrow',
        );

	    $this->assertShouldTriggerConfig($config);

	    $config['customFilter'] = new AlwaysTrigger();
        $this->assertShouldTriggerConfig($config);

	    $config['customFilter'] = new NeverTrigger();
        $this->assertNotShouldTriggerConfig($config);
    }

    private function assertShouldTriggerConfig($config)
    {
        $filter = $this->makeFilter($config);
        $this->assertTrue($filter->shouldTrigger());
    }

    private function assertNotShouldTriggerConfig($config)
    {
        $filter = $this->makeFilter($config);
        $this->assertFalse($filter->shouldTrigger());
    }

    private function makeFilter($config)
    {
        return new DefaultFilters($experimentName = 'ExperimentName', $this->storage, $config);
    }
}