InnoCraft\Experiments\Variations\UrlRedirectVariation
===============






* Class name: UrlRedirectVariation
* Namespace: InnoCraft\Experiments\Variations
* Parent class: [InnoCraft\Experiments\Variations\StandardVariation](InnoCraft-Experiments-Variations-StandardVariation.md)







Methods
-------


### __construct

    mixed InnoCraft\Experiments\Variations\StandardVariation::__construct(array $variation)





* Visibility: **public**
* This method is defined by [InnoCraft\Experiments\Variations\StandardVariation](InnoCraft-Experiments-Variations-StandardVariation.md)


#### Arguments
* $variation **array** - &lt;p&gt;eg array(&#039;name&#039; =&gt; &#039;blueColor&#039;, &#039;percentage&#039; =&gt; 50).
A name has to be given and can be also an ID, eg &quot;4&quot;. Percentage is optional.
If given, it defines how much traffic this variation should get. For example defining
50 means, this variation will be activated in 50% of overall experiment activations.&lt;/p&gt;



### getUrl

    mixed InnoCraft\Experiments\Variations\UrlRedirectVariation::getUrl()





* Visibility: **public**




### getUrlWithExperimentParameters

    mixed InnoCraft\Experiments\Variations\UrlRedirectVariation::getUrlWithExperimentParameters()





* Visibility: **public**




### run

    void InnoCraft\Experiments\Variations\VariationInterface::run()

Runs / executes the given variation. Depending on the variation type a different action may be executed.

For example a redirect or calling a callable.

* Visibility: **public**
* This method is defined by [InnoCraft\Experiments\Variations\VariationInterface](InnoCraft-Experiments-Variations-VariationInterface.md)




### getName

    string InnoCraft\Experiments\Variations\VariationInterface::getName()

Get the name of the variation.



* Visibility: **public**
* This method is defined by [InnoCraft\Experiments\Variations\VariationInterface](InnoCraft-Experiments-Variations-VariationInterface.md)




### getPercentage

    string InnoCraft\Experiments\Variations\VariationInterface::getPercentage()

Get the percentage allocated to this variation. Only returns a percentage if a fixed percentage was allocated
to this variation. If no percentage is allocated, it will use the default percentage of a variation which depends
on the number of set variations within an experiment.



* Visibility: **public**
* This method is defined by [InnoCraft\Experiments\Variations\VariationInterface](InnoCraft-Experiments-Variations-VariationInterface.md)



