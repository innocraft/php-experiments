InnoCraft\Experiments\Variations\VariationInterface
===============






* Interface name: VariationInterface
* Namespace: InnoCraft\Experiments\Variations
* This is an **interface**






Methods
-------


### getName

    string InnoCraft\Experiments\Variations\VariationInterface::getName()

Get the name of the variation.



* Visibility: **public**




### getPercentage

    string InnoCraft\Experiments\Variations\VariationInterface::getPercentage()

Get the percentage allocated to this variation. Only returns a percentage if a fixed percentage was allocated
to this variation. If no percentage is allocated, it will use the default percentage of a variation which depends
on the number of set variations within an experiment.



* Visibility: **public**




### run

    void InnoCraft\Experiments\Variations\VariationInterface::run()

Runs / executes the given variation. Depending on the variation type a different action may be executed.

For example a redirect or calling a callable.

* Visibility: **public**



