InnoCraft\Experiments\Filters\DefaultFilters
===============






* Class name: DefaultFilters
* Namespace: InnoCraft\Experiments\Filters
* This class implements: [InnoCraft\Experiments\Filters\FilterInterface](InnoCraft-Experiments-Filters-FilterInterface.md)






Methods
-------


### __construct

    mixed InnoCraft\Experiments\Filters\DefaultFilters::__construct(string|integer $experimentName, \InnoCraft\Experiments\Storage\StorageInterface $storage, array $config)





* Visibility: **public**


#### Arguments
* $experimentName **string|integer**
* $storage **[InnoCraft\Experiments\Storage\StorageInterface](InnoCraft-Experiments-Storage-StorageInterface.md)**
* $config **array** - &lt;p&gt;Can be empty if no default filters should be added&lt;/p&gt;



### addFilter

    mixed InnoCraft\Experiments\Filters\DefaultFilters::addFilter(\InnoCraft\Experiments\Filters\FilterInterface $filter)





* Visibility: **public**


#### Arguments
* $filter **[InnoCraft\Experiments\Filters\FilterInterface](InnoCraft-Experiments-Filters-FilterInterface.md)**



### getFilters

    mixed InnoCraft\Experiments\Filters\DefaultFilters::getFilters()





* Visibility: **public**




### shouldTrigger

    boolean InnoCraft\Experiments\Filters\FilterInterface::shouldTrigger()





* Visibility: **public**
* This method is defined by [InnoCraft\Experiments\Filters\FilterInterface](InnoCraft-Experiments-Filters-FilterInterface.md)



