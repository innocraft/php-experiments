<?php 

use InnoCraft\Experiments\Filters\NeverTrigger;

class NeverTriggerTest extends PHPUnit_Framework_TestCase {

  public function test_shouldTrigger_alwaysReturnsFalse(){
	  $filter = new NeverTrigger();
	  $this->assertFalse($filter->shouldTrigger());
  }

}