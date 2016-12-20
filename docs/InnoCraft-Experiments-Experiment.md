InnoCraft\Experiments\Experiment
===============

Lets you create a new experiment to run an A/B test or a split test.




* Class name: Experiment
* Namespace: InnoCraft\Experiments



Constants
----------


### ORIGINAL_VARIATION_NAME

    const ORIGINAL_VARIATION_NAME = 'original'





### DO_NOT_TRIGGER

    const DO_NOT_TRIGGER = null







Methods
-------


### __construct

    mixed InnoCraft\Experiments\Experiment::__construct(string $experimentNameOrId, array|array<mixed,\InnoCraft\Experiments\Variations\VariationInterface> $variations, array $config)

Creates a new experiment



* Visibility: **public**


#### Arguments
* $experimentNameOrId **string** - &lt;p&gt;Can be any experiment name or an id of the experiment (eg as given by A/B Testing for Piwik)&lt;/p&gt;
* $variations **array|array&lt;mixed,\InnoCraft\Experiments\Variations\VariationInterface&gt;**
* $config **array**



### getExperimentName

    integer|string InnoCraft\Experiments\Experiment::getExperimentName()

Get the name of this experiment.



* Visibility: **public**




### forceVariationName

    mixed InnoCraft\Experiments\Experiment::forceVariationName(string $variationName)

Forces the activation of the given variation name.



* Visibility: **public**


#### Arguments
* $variationName **string**



### shouldTrigger

    boolean InnoCraft\Experiments\Experiment::shouldTrigger()

Detect whether any variation, including the original version, should be activated.

Returns true if a variation should and will be activated when calling eg \getActivatedVariation()
or \run(), false if no variation will be activated.

* Visibility: **public**




### getActivatedVariation

    \InnoCraft\Experiments\Variations\VariationInterface|null InnoCraft\Experiments\Experiment::getActivatedVariation()

Get the activated variation for this experiment, or null if no variation was activated because of a set filter.

For example when the user does not take part in the experiment or when a scheduled date prevents the activation
of a variation. Returns the activated variation if no filter "blocked" it. On the first request, a variation
will be randomly chosen unless it was forced by \forceVariationName(). On all subsequent requests
it will reuse the variation that was activated on the first request.

* Visibility: **public**




### getFilter

    \InnoCraft\Experiments\Filters\FilterInterface InnoCraft\Experiments\Experiment::getFilter()

Get the set filter.



* Visibility: **public**




### getVariations

    \InnoCraft\Experiments\Variations InnoCraft\Experiments\Experiment::getVariations()

Get the set variations.



* Visibility: **public**




### getStorage

    \InnoCraft\Experiments\Storage\Cookie|\InnoCraft\Experiments\Storage\StorageInterface InnoCraft\Experiments\Experiment::getStorage()

Get the set storage.



* Visibility: **public**




### trackVariationActivation

    mixed InnoCraft\Experiments\Experiment::trackVariationActivation(\stdClass|\PiwikTracker $tracker)

Tracks the activation of a variation using for example the Piwik Tracker. This lets Piwik know which variation
was activated and should be used if you track your application using the Piwik Tracker server side. If you are
usually tracking using the JavaScript Tracker, have a look at {@link getTrackingScript()}.



* Visibility: **public**


#### Arguments
* $tracker **stdClass|PiwikTracker** - &lt;p&gt;The passed object needs to implement a &lt;code&gt;doTrackEvent&lt;/code&gt; method accepting
three parameters $category, $action, $name&lt;/p&gt;



### getTrackingScript

    string InnoCraft\Experiments\Experiment::getTrackingScript(string $experimentName, string $variationName)

Returns the JavaScript tracking code that you can echo in your website to let Piwik know which variation was
activated server side.

Do not pass variables from $_GET or $_POST etc. Make sure to escape the variables before passing them
to this method as you would otherwise risk an XSS.

* Visibility: **public**


#### Arguments
* $experimentName **string** - &lt;p&gt;ExperimentName and VariationName needs to be passed cause we do not yet have a way
                               here to properly escape it to prevent XSS.&lt;/p&gt;
* $variationName **string**


