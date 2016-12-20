<?php
/**
 * Example for an A/B test where we have the original version (added automatically),
 * a green variation and a blue variation. A variation is forced so it will always
 * activate the green variation.
 */

date_default_timezone_set('utc');

use InnoCraft\Experiments\Experiment;

include_once '../vendor/autoload.php';

$variations = [['name' => 'green'], ['name' => 'blue']];

$experiment = new Experiment('experimentName', $variations);

$experiment->forceVariationName('green');

// will return the forced "green" variation
$activated = $experiment->getActivatedVariation();

if ($activated->getName() === 'green') {
    //...
}