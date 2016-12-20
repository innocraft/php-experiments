<?php

use InnoCraft\Experiments\Variations;
use InnoCraft\Experiments\Variations\StandardVariation;
use InnoCraft\Experiments\Variations\SplitTestVariation;
use InnoCraft\Experiments\Variations\CallableVariation;

class CustomVariations extends Variations {
    public function getNumVariations()
    {
        return parent::getNumVariations();
    }

    public function getVariationDefaultPercentage()
    {
        return parent::getVariationDefaultPercentage();
    }
}

class VariationsTest extends PHPUnit_Framework_TestCase {

    public function test_shouldNotThrowAnException_IfNoVariationsGiven()
    {
        $variations = $this->makeVariations($variations = []);
        $this->assertSame([], $variations->getVariations());
        $this->assertSame(0, $variations->getNumVariations());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage A variation needs to be either an array of an instance of VariationInterface
     */
    public function test_shouldThrowAnException_IfInvalidVariationGiven()
    {
        $this->makeVariations($variations = ['foo']);
    }

    public function test_shouldConvertVariationsToInstances()
    {
        $variations = $this->makeVariations($variations = array(
            array('name' => 'variation1'),
            array('name' => 'variation2', 'percentage' => 40),
            array('name' => 'splitTestVariation', 'url' => 'https://www.innocraft.com'),
            array('name' => 'callableVariation', 'callable' => function () {}),
        ));

        $this->assertSame(4, $variations->getNumVariations());

        $variations = $variations->getVariations();
        $this->assertCount(4, $variations);
        $this->assertTrue($variations[0] instanceof StandardVariation);
        $this->assertTrue($variations[1] instanceof StandardVariation);
        $this->assertTrue($variations[2] instanceof SplitTestVariation);
        $this->assertTrue($variations[3] instanceof CallableVariation);
    }

    public function test_setVariations_canSetAnEmptyArray()
    {
        $variations = $this->makeVariations($variations = array(
            array('name' => 'variation1'),
        ));

        $variations->setVariations([]);
        $this->assertSame([], $variations->getVariations());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage A variation needs to be either an array of an instance of VariationInterface
     */
    public function test_setVariations_throwsException_ifInvalidVariationSet()
    {
        $variations = $this->makeVariations($variations = [
            ['name' => 'variation1'],
        ]);

        $variations->setVariations(['foobar']);
    }

    public function test_setVariations_overwritesExistingVariations_WorksFromArrayOrInterface()
    {
        $variations = $this->makeVariations(array(
            array('name' => 'variation1')
        ));

        $variations->setVariations(array(
            array('name' => 'variation4', 'url' => 'http://www.innocraft.com'),
            new StandardVariation(array('name' => 'variation5'))
        ));

        $this->assertSame(2, $variations->getNumVariations());

        $allVariations = $variations->getVariations();
        $this->assertTrue($allVariations[0] instanceof SplitTestVariation);
        $this->assertSame('variation4', $allVariations[0]->getName());
        $this->assertTrue($allVariations[1] instanceof StandardVariation);
        $this->assertSame('variation5', $allVariations[1]->getName());
    }

    public function test_addVariation_canAddNewVariationFromArray()
    {
        $variations = $this->makeVariations(array(
            array('name' => 'variation1')
        ));

        $this->assertSame(1, $variations->getNumVariations());

        $variations->addVariation(array('name' => 'variation4', 'url' => 'http://www.innocraft.com'));

        $this->assertSame(2, $variations->getNumVariations());

        $allVariations = $variations->getVariations();

        // verify variation added and converted to VariationInterface
        $this->assertTrue($allVariations[1] instanceof SplitTestVariation);
        $this->assertSame('variation4', $allVariations[1]->getName());

        // verify old one not removed
        $this->assertSame('variation1', $allVariations[0]->getName());
    }

    public function test_addVariation_canAddNewVariationFromInterface()
    {
        $variations = $this->makeVariations(array(
            array('name' => 'variation1')
        ));

        $this->assertSame(1, $variations->getNumVariations());

        $variation = new CallableVariation(array('name' => 'variation4', 'callable' => function() {}));
        $variations->addVariation($variation);

        $this->assertSame(2, $variations->getNumVariations());

        $allVariations = $variations->getVariations();

        // verify variation added
        $this->assertSame($variation, $allVariations[1]);

        // verify old one not removed
        $this->assertSame('variation1', $allVariations[0]->getName());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage A variation needs to be either an array of an instance of VariationInterface
     */
    public function test_addVariation_throwsException_ifInvalidVariationSet()
    {
        $variations = $this->makeVariations($variations = array(
            array('name' => 'variation1'),
        ));

        $variations->addVariation('foobar');
    }

    public function test_getVariationDefaultPercentage_noPercentageSetAtAll_shouldSharePercentageEquallyAcrossAllVariations()
    {
        $variations = $this->makeVariationsWithPercentages(array(null, null, null, null));

        // 4 variations have 25% each
        $this->assertSame(25, $variations->getVariationDefaultPercentage());
    }

    public function test_getVariationDefaultPercentage_onlyOneVariationGiven_shouldGetAllCredit()
    {
        $variations = $this->makeVariationsWithPercentages(array(null));

        $this->assertSame(100, $variations->getVariationDefaultPercentage());
    }

    public function test_getVariationDefaultPercentage_onlyOneVariationGivenWithFixedPercentage_shouldNotHaveAnyDefaultPercentage()
    {
        $variations = $this->makeVariationsWithPercentages(array(55));

        $this->assertSame(0, $variations->getVariationDefaultPercentage());
    }

    public function test_getVariationDefaultPercentage_someVariationsWithFixedPercentage_remainingVariationsShouldSharePercentage()
    {
        $variations = $this->makeVariationsWithPercentages(array(24, null, 51, 3, null));

        // (100 - 24 - 51 - 3) / 2 remaining variations
        $this->assertSame(11, $variations->getVariationDefaultPercentage());
    }

    public function test_getVariationDefaultPercentage_variationsAllocateMoreThan100Percent_shouldNotHaveAnyDefaultPercentage()
    {
        $variations = $this->makeVariationsWithPercentages(array(59, null, 51, 33, null));

        $this->assertSame(0, $variations->getVariationDefaultPercentage());
    }

    public function test_getVariationDefaultPercentage_shouldAlwaysReturnInteger()
    {
        $variations = $this->makeVariationsWithPercentages(array(null, null, null));

        // 100 / 3 = 33.3333
        $this->assertSame(33, $variations->getVariationDefaultPercentage());
    }

    public function get_shouldReturnNull_ifVariationDoesNotExist()
    {
        $variations = $this->makeExampleVariations();

        $this->assertNull($variations->get('variationFooBar999'));
    }

    public function test_get_shouldReturnInstanceOfVariation_ifItExists()
    {
        $variations = $this->makeExampleVariations();
        $variation = $variations->get('variation1');

        $this->assertTrue($variation instanceof Variations\VariationInterface);
        $this->assertSame('variation1', $variation->getName());
    }

    public function test_get_canFindVariation_EvenIfThereIsAStringIntegerTypeMismatch()
    {
        $variations = $this->makeExampleVariations();
        $variation = $variations->get(2);

        $this->assertTrue($variation instanceof Variations\VariationInterface);
        $this->assertSame('2', $variation->getName());
    }

    public function test_exists_shouldReturnFalse_ifVariationDoesNotExist()
    {
        $variations = $this->makeExampleVariations();

        $this->assertFalse($variations->exists('variationFooBar999'));
    }

    public function test_exists_shouldReturnTrue_ifVariationDoesExist()
    {
        $variations = $this->makeExampleVariations();

        $this->assertTrue($variations->exists('variation1'));
    }

    public function test_exists_canFindVariation_EvenIfThereIsAStringIntegerTypeMismatch()
    {
        $variations = $this->makeExampleVariations();
        $this->assertTrue($variations->exists(2));
        $this->assertTrue($variations->exists('2'));
    }

    public function test_selectRandomVariation_ifNoVariationsAreSet()
    {
        $variations = $this->makeExampleVariations();
        $variations->setVariations(array());

        $this->assertNull($variations->selectRandomVariation());
    }

    public function test_selectRandomVariation_shouldSelectVariationRandomly()
    {
        $selected = array();
        $variations = $this->makeExampleVariations();

        for ($i = 0; $i < 100; $i++) {
            $name = $variations->selectRandomVariation()->getName();
            if (isset($selected[$name])) {
                $selected[$name]++;
            } else {
                $selected[$name] = 1;
            }
        }

        $this->assertCount($variations->getNumVariations(), $selected); // we make sure each variation was selected at least once

        // there are 4 variations, so on 100 tries, on average each variation should be activated about 25 times
        foreach ($selected as $name => $count) {
            $this->assertTrue($count >= 7 && $count <= 45, 'each variation was selected between 7 and 25 times');
        }

        // in total there were 100 selections
        $this->assertSame(100, array_sum($selected));
    }

    private function makeVariationsWithPercentages($percentages)
    {
        $variations = array();
        foreach ($percentages as $i => $percentage) {
            if (isset($percentage)) {
                $variations[] = array('name' => 'variation' . $i, 'percentage' => $percentage);
            } else {
                $variations[] = array('name' => 'variation' . $i);
            }
        }

        return $this->makeVariations($variations);
    }

    private function makeExampleVariations()
    {
        return $this->makeVariations($variations = array(
            array('name' => 'variation1'),
            array('name' => '2'),
            array('name' => 'splitTestVariation', 'url' => 'https://www.innocraft.com'),
            array('name' => 'callableVariation', 'callable' => function () {}),
        ));
    }

    private function makeVariations($variations)
    {
        return new CustomVariations($experimentName = 'myExperiment', $variations);
    }
}