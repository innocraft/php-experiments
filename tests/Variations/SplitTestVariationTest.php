<?php

use InnoCraft\Experiments\Variations\SplitTestVariation;

include_once 'StandardVariationTest.php';

class SplitTestVariationTest extends StandardVariationTest {

    private $experimentName = 'myExperimentName';
    private $variationName = 'myName';

    public static function myCallableMethod() { }

    public function test_getUrl_ReturnsPassedUrl()
    {
        $variation = $this->buildSplitTestVariation('https://www.innocraft.com');

        $this->assertSame('https://www.innocraft.com', $variation->getUrl());
    }

    public function test_getUrl_ReturnsEmptyStringIfNoUrlPassed()
    {
        $variation = $this->buildSplitTestVariation(null);

        $this->assertSame('', $variation->getUrl());
    }

    public function test_getUrlWithExperimentParameters_shouldAddAbTestParametersToUrl()
    {
        $variation = $this->buildSplitTestVariation('https://www.innocraft.com');

        $this->assertSame('https://www.innocraft.com?pk_abe=myExperimentName&pk_abv=myName', $variation->getUrlWithExperimentParameters());
    }

    public function test_getUrlWithExperimentParameters_shouldAddAbTestParametersToUrlWithSearchQuery()
    {
        $variation = $this->buildSplitTestVariation('https://www.innocraft.com?foo=bar');

        $this->assertSame('https://www.innocraft.com?foo=bar&pk_abe=myExperimentName&pk_abv=myName', $variation->getUrlWithExperimentParameters());
    }

    public function test_getUrlWithExperimentParameters_escapesParameters()
    {
        $this->experimentName = 'foo&5#1<6';
        $this->variationName = 'bar&5#1<6';

        $variation = $this->buildSplitTestVariation('https://www.innocraft.com/');

        $this->assertSame('https://www.innocraft.com/?pk_abe=foo%265%231%3C6&pk_abv=bar%265%231%3C6', $variation->getUrlWithExperimentParameters());
    }

    private function buildSplitTestVariation($callable)
    {
        return $this->makeVariation(array('name' => $this->variationName, 'url' => $callable));
    }

    private function makeVariation($variation)
    {
        return new SplitTestVariation($this->experimentName, $variation);
    }

}