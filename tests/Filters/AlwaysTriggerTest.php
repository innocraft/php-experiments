<?php 

use InnoCraft\Experiments\Filters\AlwaysTrigger;

class AlwaysTriggerTest extends PHPUnit_Framework_TestCase {

  public function test_shouldTrigger_alwaysReturnsTrue(){
	  $filter = new AlwaysTrigger();
	  $this->assertTrue($filter->shouldTrigger());
  }

}