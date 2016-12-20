<?php
/**
 * Example for an A/B test where we have the original version (added automatically),
 * a green variation and a blue variation. It shows you how you can schedule an experiment to run it only
 * for a certain time.
 */

date_default_timezone_set('utc');

use InnoCraft\Experiments\Experiment;

include_once '../vendor/autoload.php';

$variations = [['name' => 'green'], ['name' => 'blue']];

$config = [
    // optional, only activate the experiment after this date. Instead of a string, a DateTimeInterface can be used.
    'startDate' => '2017-01-02 03:04:05',
    // optional, only activate the experiment before this date. Instead of a string, a DateTimeInterface can be used.
    'endDate' => '2019-01-02 03:04:05',
    // optional, defaults to "now". Instead of a string, a DateTimeInterface can be used.
    'currentDate' => '2018-01-02 03:04:05',
];

$experiment = new Experiment('experimentName', $variations, $config);

// shouldTrigger() may return false if the current date is not between start date and end date
if ($experiment->shouldTrigger()) {

    // to get a randomly activated variation or a previously forced variation call
    $activated = $experiment->getActivatedVariation();

    if ($activated->getName() === 'green') {
        echo 'do something';
    }
} else {
    echo 'experiment should not be activated, usually means the original version will be shown';
}