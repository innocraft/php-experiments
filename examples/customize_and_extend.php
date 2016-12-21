<?php
/**
 * By default, an experiment is created and configured via simple arrays. This works well in many cases,
 * especially when you want to use it with A/B Testing for Piwik (www.ab-tests.net).
 *
 * If you want to have custom behaviour you can alternatively choose to create instances of everything
 * and this way for example customize where the app will store which variation was activated for which
 * user (storage). You can also add custom variations to have custom logic for what happens when a
 * test gets activated and custom filters to decide who will take part in an experiment
 */

date_default_timezone_set('utc');

use InnoCraft\Experiments\Experiment;
use InnoCraft\Experiments\Variations;
use InnoCraft\Experiments\Variations\StandardVariation;
use InnoCraft\Experiments\Storage\Cookie;
use InnoCraft\Experiments\Filters\AlwaysTrigger;

include_once '../vendor/autoload.php';

$variations = new Variations('experimentName', [
    // you can pass either instances of different variations
    new StandardVariation(['name' => 'blue']),
    // or an array and the instance will be created in the background
    ['name' => 'green', 'percentage' => 60]  // percentage can be specified to allocate more or less traffic to a variation
    // various different variations can be found in the "src/Variations" directory. They currently only
    // differ in the way they implement the "run" method. You can provide custom variation instances
    // by implementing the VariationInterface
]);

$config = [
    'storage' => new Cookie(),
    // you can define a different storage by implementing a StorageInterface. For example you could use it to store the
    // variation in a database instead of a cookie. Existing storages can be found in the "src/storage" directory.
    'filter' => new AlwaysTrigger()
    // you can define different filters by implementing a FilterInterface. This can be useful to restrict who
    // will participate in this experiment entirely based on custom rules. Existing filters can be found in the
    // "src/filters" directory.
];

$experiment = new Experiment('experimentName', $variations);

// shouldTrigger() may return false depending on logic of the passed filter
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
    }
} else {
    echo 'experiment should not be activated, usually means the original version will be shown';
}
