<?php
/**
 * Example for an A/B test where we have the original version (added automatically),
 * a green variation and a blue variation. Each variation can optionally define a custom "percentage" to allocate
 * more or less traffic to it. Also the experiment can receive a percentage to define how many users take part in it
 * overall.
 */

date_default_timezone_set('utc');

use InnoCraft\Experiments\Experiment;

include_once '../vendor/autoload.php';

$variations = [
    ['name' => 'green', 'percentage' => '40'], // optional percentage, means 40% of all participants should get this variation
    ['name' => 'blue', 'percentage' => '30'], // optional percentage, means 30% of all participants should get this variation
    // the original variation will get the remaining 30% automatically, but can be set manually as well
];
$config = [
    'percentage' => 80, // optional, 80% of all users or visitors should take part in this experiment. Defaults to 100%
];

$experiment = new Experiment('experimentName', $variations, $config);

// shouldTrigger() may return false if a user is not taking part in the experiment (if the user is not one of those 80% that take part in it)
if ($experiment->shouldTrigger()) {

    // to get a randomly activated variation or a previously forced variation call
    $activated = $experiment->getActivatedVariation();

    if ($activated->getName() === 'green') {
        //...
    }
} else {
    echo 'experiment should not be activated, usually means the original version will be shown';
}