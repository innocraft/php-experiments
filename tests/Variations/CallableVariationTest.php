<?php 

use InnoCraft\Experiments\Variations\CallableVariation;

include_once 'StandardVariationTest.php';

class CallableVariationTest extends StandardVariationTest {

    public static function myCallableMethod() { }

    public function test_getCallable_ReturnsPassedCallableArray()
    {
        $callable = array(__CLASS__, 'myCallableMethod');
        $variation = $this->buildCallableVariation($callable);

        $this->assertSame($callable, $variation->getCallable());
    }

    public function test_getCallable_ReturnsPassedCallableClosure()
    {
        $callable = function () { return 1; };
        $variation = $this->buildCallableVariation($callable);

        $this->assertSame($callable, $variation->getCallable());
    }

    public function test_run_executesCallableOnce()
    {
        $called = 0;
        $variation = $this->buildCallableVariation(function () use (&$called) {
            $called++;
        });
        $variation->run();

        $this->assertSame(1, $called);
    }

    public function test_run_passedVariationAsArgument()
    {
        $called = null;
        $variation = $this->buildCallableVariation(function ($passedVariation) use (&$called) {
            $called = $passedVariation;
        });
        $variation->run();

        $this->assertSame($called, $variation);
        $this->assertSame($called->getName(), $variation->getName());
    }

    public function test_run_doesNotFail_IfCallableIsNotActuallyCallable()
    {
        $variation = $this->buildCallableVariation('fooBarBaz');
        $this->assertNull($variation->run());
    }

    private function buildCallableVariation($callable)
    {
        return $this->makeVariation(array('name' => 'myName', 'callable' => $callable));
    }

    private function makeVariation($variation)
    {
        return new CallableVariation($variation);
    }

}