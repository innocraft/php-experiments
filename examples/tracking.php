<?php
/**
 * This is mainly useful for users of A/B Testing for Piwik (www.ab-tests.net). It shows you how to send
 * a tracking request form your server to your Piwik letting it know that you just activated a variation
 * for a user.
 */

date_default_timezone_set('utc');

use InnoCraft\Experiments\Experiment;

include_once '../vendor/autoload.php';

// gives you a JavaScript tracking snippet that you can print in your HTML website to let Piwik know
// that you just activated a specific variation. This is useful when you track your visitors via Piwik
// with the regular JavaScript tracking code and just activated an experiment server side.
$experiment = new Experiment('myExperimentName', [['name' => 'myVariationName']]);
$activatedVariation = $experiment->getActivatedVariation();
// important: you should escape the passed experiment name and variation name if needed to prevent XSS.
$script = $experiment->getTrackingScript($experiment->getExperimentName(), $activatedVariation->getName());
echo $script; // prints eg "<script>_paq.push(['AbTesting::enter', {...}])"


// This is useful when you track your users server side via the PiwikTracker (https://github.com/piwik/piwik-php-tracker)
// and just activated an experiment server side. It will make sure to call the correct PiwikTracker method to let Piwik
// know you just activated an experiment.
if (class_exists('\PiwikTracker')) {
    $tracker = new \PiwikTracker($idSite = 1, $apiUrl = 'https://piwik.example.com');
    $experiment = new Experiment('myExperimentName', [['name' => 'myVariationName']]);
    $activatedVariation = $experiment->getActivatedVariation();
    $experiment->trackVariationActivation($tracker);
}
