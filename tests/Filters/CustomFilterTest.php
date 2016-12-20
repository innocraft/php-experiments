<?php 

use InnoCraft\Experiments\Filters\CustomFilter;

class CustomFilterTest extends PHPUnit_Framework_TestCase {

    public static function customFilterCallback()
    {
        return true;
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage is not a callable
     */
    public function test_construct_shouldThrowException_IfCallableIsNotCallable()
    {
        $this->makeFilter('fooBarBaz');
    }

    public function test_shouldTrigger_shouldExecuteCallable()
    {
        $called = false;
        $this->assertShouldTrigger(function () use (&$called) {
            $called = true;
            return true;
        });

        $this->assertTrue($called);
    }

    public function test_shouldTrigger_shouldExecuteCallableAndCastToBool()
    {
        $this->assertShouldTrigger(function () {
            return 1;
        });
    }

    public function test_shouldTrigger_shouldExecuteAnArray()
    {
        $this->assertShouldTrigger(array(__CLASS__, 'customFilterCallback'));
    }

    public function test_shouldTrigger_shouldNotTrigger_IfCallableReturnsFalse()
    {
        $this->assertNotShouldTrigger(function () {
            return false;
        });
    }

    private function assertShouldTrigger($callable)
    {
        $this->assertTrue($this->makeFilter($callable)->shouldTrigger());
    }

    private function assertNotShouldTrigger($callable)
    {
        $this->assertFalse($this->makeFilter($callable)->shouldTrigger());
    }

    private function makeFilter($callable)
    {
        return new CustomFilter($callable);
    }

}