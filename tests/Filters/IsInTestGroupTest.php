<?php 

use InnoCraft\Experiments\Filters\IsInTestGroup;
use InnoCraft\Experiments\Storage\Transient;
use InnoCraft\Experiments\Storage\StorageInterface;

class IsInTestGroupTest extends PHPUnit_Framework_TestCase {

    /**
     * @var StorageInterface
     */
    private $storage;

    /**
     * @var string
     */
    private $experimentName = 'myName';

    public function setUp()
    {
        $this->storage = new Transient();
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage should not be higher than 100
     */
    public function test_construct_shouldThrowAnException_IfPercentageIsHigherThan100()
    {
        $this->makeFilter(IsInTestGroup::MAX_PERCENTAGE + 1);
    }

    public function test_shouldTrigger_shouldNeverTrigger_IfPercentageIsZero()
    {
        $this->assertNotShouldTrigger(0);
    }

    public function test_shouldTrigger_shouldNeverTrigger_IfPercentageIsNull()
    {
        $this->assertNotShouldTrigger(null);
    }

    public function test_shouldTrigger_doesNotTrigger_shouldPersistResultInStorage()
    {
        $this->assertNotShouldTrigger(0);

        $this->assertSame(0, $this->storage->get(IsInTestGroup::STORAGE_NAMESPACE, $this->experimentName));
    }

    public function test_shouldTrigger_doesTrigger_shouldPersistResultInStorage()
    {
        $this->assertShouldTrigger(IsInTestGroup::MAX_PERCENTAGE);

        $this->assertSame(1, $this->storage->get(IsInTestGroup::STORAGE_NAMESPACE, $this->experimentName));
    }

    public function test_shouldTrigger_shouldAlwaysUseResultFromCacheFirst_NoMatterThePercentage()
    {
        $this->storage->set(IsInTestGroup::STORAGE_NAMESPACE, $this->experimentName, 1);
        // would usually definitely return false, but we force it to read the cache value
        $this->assertShouldTrigger(0);

        $this->storage->set(IsInTestGroup::STORAGE_NAMESPACE, $this->experimentName, 0);
        // would usually definitely return true, but we force it to read the cache value
        $this->assertNotShouldTrigger(IsInTestGroup::MAX_PERCENTAGE);
    }

    public function test_shouldTrigger_shouldAlwaysTrigger_IfPercentageIs100()
    {
        $this->assertShouldTrigger(IsInTestGroup::MAX_PERCENTAGE);
    }

    public function test_shouldTrigger_doesRandomlyDecideIfSomeoneShouldParticipate()
    {
        $numTriggered = 0;
        $numNotTriggered = 0;
        for ($i = 0; $i < 100; $i++) {
            $this->storage = new Transient(); // we need to reset the storage each time, otherwise we get always the same result
            $filter = $this->makeFilter($percentage = 20);
            if ($filter->shouldTrigger()) {
                $numTriggered++;
            } else {
                $numNotTriggered++;
            }
        }

        // because it is random we cannot tell exactly how often it is going to be activated but roughly this should be fine
        $this->assertTrue($numTriggered > 4 && $numTriggered < 40);
        $this->assertTrue($numNotTriggered > 40 && $numTriggered < 95);
    }

    private function assertShouldTrigger($percentage)
    {
        $this->assertTrue($this->makeFilter($percentage)->shouldTrigger());
    }

    private function assertNotShouldTrigger($percentage)
    {
        $this->assertFalse($this->makeFilter($percentage)->shouldTrigger());
    }

    private function makeFilter($percentage)
    {
        return new IsInTestGroup($this->storage, $this->experimentName, $percentage);
    }

}