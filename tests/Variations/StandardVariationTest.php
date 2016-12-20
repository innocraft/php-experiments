<?php 

use InnoCraft\Experiments\Variations\StandardVariation;

class StandardVariationTest extends PHPUnit_Framework_TestCase {

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage No variation name is given
     */
    public function test_construct_shouldThrowException_IfVariationIsEmpty()
    {
        $this->makeVariation(null);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage No variation name is given
     */
    public function test_construct_shouldThrowException_IfVariationNameIsNotSet()
    {
        $this->buildVariation(false);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage No variation name is given
     */
    public function test_construct_shouldThrowException_IfVariationNameIsEmptyString()
    {
        $this->buildVariation('');
    }

    public function test_constructor_nameCanBeZero()
    {
        $variation = $this->buildVariation('0');
        $this->assertSame('0', $variation->getName());

        $variation = $this->buildVariation(0);
        $this->assertSame(0, $variation->getName());
    }

    public function test_getName_returnsTheVariationName()
    {
        $variation = $this->buildVariation('myName');
        $this->assertSame('myName', $variation->getName());
    }

    public function test_getPercentage_isNullByDefault()
    {
        $variation = $this->buildVariation('myName');
        $this->assertNull($variation->getPercentage());
    }

    public function test_getPercentage_shouldReturnValue_IfGivenInConstructor()
    {
        $variation = $this->buildVariation('myName', $percentage = '94');
        $this->assertSame(94, $variation->getPercentage());
    }

    public function test_getPercentage_shouldIgnoreFalseValue()
    {
        $variation = $this->buildVariation('myName', $percentage = false);
        $this->assertNull($variation->getPercentage());
    }

    public function test_run_doesNotFail()
    {
        $variation = $this->buildVariation('myName');
        $this->assertNull($variation->run());
    }

    private function buildVariation($name, $percentage = null)
    {
        $variation = array('name' => $name);

        if (isset($percentage)) {
            $variation['percentage'] = $percentage;
        }

        return $this->makeVariation($variation);
    }

    private function makeVariation($variation)
    {
        return new StandardVariation($variation);
    }

}