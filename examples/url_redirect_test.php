<?php
/**
 * Example for a very simple split test where we have the original version (added automatically),
 * a layout1 variation and a layout2 variation. Each variation will be activated in 33.3% of the time
 * randomly.
 */

date_default_timezone_set('utc');

use InnoCraft\Experiments\Experiment;

include_once '../vendor/autoload.php';

$variations = [
    ['name' => 'layout1', 'url' => 'https://www.innocraft.com/layout1'],
    ['name' => 'layout2', 'url' => 'https://www.innocraft.com/layout2']
    // ['name' => 'original'] is added in the background
];
$experiment = new Experiment('experimentName', $variations);

// to get a randomly activated variation or a previously forced variation call "getActivatedVariation"
$activated = $experiment->getActivatedVariation();

// will either not redirect if the original version gets activated, or execute a redirect to one of the
// two specified urls "https://www.innocraft.com/layout1" or "https://www.innocraft.com/layout2". For Piwik
// and possible other tools to know which variation was activated, 2 url parameters will be appended to the
// URL
$activated->run();


// ALTERNATIVELY you could handle the logic manually
if ($activated->getName() === 'layout1') {
    header('Location: ' . $activated->getUrl());
}