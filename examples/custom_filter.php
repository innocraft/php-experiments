<?php
/**
 * Example for an A/B test where we have the original version (added automatically),
 * a green variation and a blue variation. The `customFilter` restricts which users take part in the experiment
 * or not. It is possible to pass a callable for `customFilter` or an object that is an instance of a FilterInterface.
 */

date_default_timezone_set('utc');

use InnoCraft\Experiments\Experiment;

include_once '../vendor/autoload.php';

$variations = [['name' => 'green'], ['name' => 'blue']];
$config = [
    // optional, a callable to further restrict with custom logic for whom the experiment will be activated
    'customFilter' => function () {
        return true; // eg return UserCountry::GetCountry() === 'de';
    }
];
$experiment = new Experiment('experimentName', $variations);

// shouldTrigger() may return false depending on the result of the custom filter
if ($experiment->shouldTrigger()) {

    // to get a randomly activated variation or a previously forced variation call
    $activated = $experiment->getActivatedVariation();

    if ($activated->getName() === 'green') {
        // ...
    }
} else {
    echo 'experiment should not be activated, usually means the original version will be shown';
}
