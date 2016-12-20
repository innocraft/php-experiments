<?php
/**
 * Example for an A/B test where we have the original version (added automatically),
 * a green variation and a blue variation. The example shows several config options.
 *
 * If you want to fully customize the behaviour of an experiment, have a look at "ab_test_di.php". If you
 * want to perform a split test, have a look at "split_test.php".
 */

date_default_timezone_set('utc');

use InnoCraft\Experiments\Experiment;

include_once '../vendor/autoload.php';

$variations = [
    ['name' => 'green', 'percentage' => '40'], // optional percentage, means 40% of all participants should get this variation
    ['name' => 'blue', 'percentage' => '30'], // optional percentage, means 30% of all participants should get this variation
    // the original variation will get the remaining 30% automatically
];
$config = [
    'percentage' => 80, // optional, 80% of all users or visitors should take part in this experiment. Defautls to 100%
    'startDate' => '2017-01-02 03:04:05', // optional, only activate the experiment after this date
    'endDate' => '2019-01-02 03:04:05', // optional, only activate the experiment before this date
    'currentDate' => '2018-01-02 03:04:05', // optional, defaults to "now",
    'customFilter' => function () { // optional, a callable to further restrict with custom logic for who the experiment will be activated
        return true; // eg return UserCountry::GetCountry() === 'de';
    }
];
$experiment = new Experiment('experimentName', $variations);

// to force a specific variation call
$experiment->forceVariationName('green');

// shouldTrigger() may return false for example if the user does not take part in this experiment, if the custom filter
// returns false, or if the experiment is scheduled via date and it should not be activated yet
if ($experiment->shouldTrigger()) {

    // to get a randomly activated variation or a previously forced variation call
    $activated = $experiment->getActivatedVariation();

    switch ($activated->getName()) {
        case 'green':
            echo 'green color';
            break;
        case 'blue':
            echo 'blue color';
            break;
        case Experiment::ORIGINAL_VARIATION_NAME:
            echo 'show original version';
            break;
    }
} else {
    echo 'experiment should not be activated, usually means the original version will be shown';
}
