<?php
/**
 * Example for a simple A/B test where we have the original version (added automatically),
 * a green color and a blue color variation. Each variation will be activated in 33.3% of the time
 * randomly.
 *
 * In this example we specify a callable for each variation where we perform the logic that needs to be executed
 * when the variation gets activated. This way you don't need to do a
 * `if ($experiment->getActivatedVariation()->getName() === 'green') { ... }`
 */

date_default_timezone_set('utc');

use InnoCraft\Experiments\Experiment;
use InnoCraft\Experiments\Variations\VariationInterface;

include_once '../vendor/autoload.php';

$variations = [
    ['name' => 'green', 'callable' => function (VariationInterface $variation) { echo $variation->getName(); }],
    ['name' => 'blue', 'callable' => function (VariationInterface $variation) { echo $variation->getName(); }],
    // ['name' => 'original'] is added in the background, but can be set manually as well
];
$experiment = new Experiment('experimentName', $variations);

// will either not redirect if the original version gets activated, or execute a redirect to one of the
// two specified urls "https://www.innocraft.com/layout1" or "https://www.innocraft.com/layout2". For Piwik
// and possible other tools to know which variation was activated, 2 url parameters will be appended to the
// URL
$activated = $experiment->getActivatedVariation();
$activated->run();