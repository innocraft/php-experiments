<?php 

use InnoCraft\Experiments\Filters\ScheduledDate;

class ScheduledDateTest extends PHPUnit_Framework_TestCase {

    /**
     * @dataProvider getDateDataProvider
     */
    public function test_shouldTrigger_passingStringDates($expectedShouldTrigger, $now, $start, $end, $message)
    {
	    $filter = new ScheduledDate($now, $start, $end);
	    $this->assertSame($expectedShouldTrigger, $filter->shouldTrigger(), $message);
    }

    /**
     * @dataProvider getDateDataProvider
     */
    public function test_shouldTrigger_passingDateTime($expectedShouldTrigger, $now, $start, $end, $message)
    {
        $now = new DateTime($now);
        $start = new DateTime($start);
        $end = new DateTime($end);

	    $filter = new ScheduledDate($now, $start, $end);
	    $this->assertSame($expectedShouldTrigger, $filter->shouldTrigger(), $message);
    }

    public function getDateDataProvider()
    {
        $dayEuStart = '2017-01-02 03:04:05';
        $dayEuNow = '2017-01-02 05:06:07';
        $dayEuEnd = '2017-01-02 09:10:11';

        return array(
            array($expected = true, $now = 'now', $start = null, $end = null, 'should always trigger when no start and no end date is set'),
            array($expected = false, $now = 'now', $start = 'tomorrow', $end = null, 'should not trigger when start date is in the future'),
            array($expected = false, $now = 'now', $start = null, $end = 'yesterday', 'should not trigger when end date is in the past'),
            array($expected = false, $now = 'now', $start = 'tomorrow', $end = 'yesterday', 'should not trigger when start date is in future and end date is in the past'),
            array($expected = true, $now = 'now', $start = 'yesterday', $end = 'tomorrow', 'should trigger when now is between start and end date'),
            array($expected = true, $now = $dayEuStart, $start = $dayEuStart, $end = $dayEuEnd, 'should trigger when start date is equal to now, EU dates given'),
            array($expected = true, $now = $dayEuStart, $start = $dayEuStart, $end = $dayEuStart, 'should trigger when end date is equal to now, EU dates given'),
            array($expected = true, $now = $dayEuNow, $start = $dayEuStart, $end = $dayEuEnd, 'should trigger when now is between start and end date, EU dates given'),
            array($expected = false, $now = $dayEuStart, $start = $dayEuNow, $end = $dayEuEnd, 'should not trigger when now is not between start and end date, EU dates given'),
        );
    }
}