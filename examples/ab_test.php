<?php
/**
 * Example for a very simple A/B test where we have the original version (added automatically),
 * a green variation and a blue variation. Each variation will be activated in 33.3% of the time randomly.
 *
 * To see how to allocate different traffic to variations, how to force a variation and more have a look at
 * the other examples. If you want to fully customize the behaviour of an experiment, have a look at
 * "customize_and_extend.php". If you want to perform a split test, have a look at "url_redirect_test.php".
 */

date_default_timezone_set('utc');

use InnoCraft\Experiments\Experiment;

include_once '../vendor/autoload.php';

$variations = [['name' => 'green'], ['name' => 'blue']];
$experiment = new Experiment('experimentName', $variations);

// to get a randomly activated variation or a previously forced variation call "getActivatedVariation"
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