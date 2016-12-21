InnoCraft\Experiments\Variations
===============






* Class name: Variations
* Namespace: InnoCraft\Experiments







Methods
-------


### __construct

    mixed InnoCraft\Experiments\Variations::__construct(string $experimentName, array|array<mixed,\InnoCraft\Experiments\Variations\VariationInterface> $variations)

Variations constructor.



* Visibility: **public**


#### Arguments
* $experimentName **string**
* $variations **array|array&lt;mixed,\InnoCraft\Experiments\Variations\VariationInterface&gt;**



### addVariation

    mixed InnoCraft\Experiments\Variations::addVariation(array|\InnoCraft\Experiments\Variations\VariationInterface $variation)

Adds a new variation to the set of existing variations.



* Visibility: **public**


#### Arguments
* $variation **array|[array](InnoCraft-Experiments-Variations-VariationInterface.md)**



### setVariations

    mixed InnoCraft\Experiments\Variations::setVariations(array|array<mixed,\InnoCraft\Experiments\Variations\VariationInterface> $variations)

Set (overwrite) all existing variations by the given variations.



* Visibility: **public**


#### Arguments
* $variations **array|array&lt;mixed,\InnoCraft\Experiments\Variations\VariationInterface&gt;**



### getVariations

    array<mixed,\InnoCraft\Experiments\Variations\VariationInterface> InnoCraft\Experiments\Variations::getVariations()

Get all set variations.



* Visibility: **public**




### selectRandomVariation

    \InnoCraft\Experiments\Variations\VariationInterface|null InnoCraft\Experiments\Variations::selectRandomVariation()

Chooses randomly a variation from the set of existing variations. Each variation may set a percentage to
allocate more or less traffic to each variation. By default all variation share the traffic equally.



* Visibility: **public**




### exists

    boolean InnoCraft\Experiments\Variations::exists(string $variationName)

Detects whether a variation with the given name exists in the pool of set variations.



* Visibility: **public**


#### Arguments
* $variationName **string**



### get

    \InnoCraft\Experiments\Variations\VariationInterface|null InnoCraft\Experiments\Variations::get(string $variationName)

Get the instance of a set variation by its variation name. If no variation matches the given name, null will be
returned.



* Visibility: **public**


#### Arguments
* $variationName **string**


