InnoCraft\Experiments\Filters\IsInTestGroup
===============






* Class name: IsInTestGroup
* Namespace: InnoCraft\Experiments\Filters
* This class implements: [InnoCraft\Experiments\Filters\FilterInterface](InnoCraft-Experiments-Filters-FilterInterface.md)


Constants
----------


### MAX_PERCENTAGE

    const MAX_PERCENTAGE = 100





### STORAGE_NAMESPACE

    const STORAGE_NAMESPACE = 'isInTestGroup'





Properties
----------


### $percentage

    private integer $percentage





* Visibility: **private**


### $storage

    private  $storage





* Visibility: **private**


### $experimentName

    private string $experimentName





* Visibility: **private**


Methods
-------


### __construct

    mixed InnoCraft\Experiments\Filters\IsInTestGroup::__construct(\InnoCraft\Experiments\Storage\StorageInterface $storage, $experimentName, $percentage)





* Visibility: **public**


#### Arguments
* $storage **[InnoCraft\Experiments\Storage\StorageInterface](InnoCraft-Experiments-Storage-StorageInterface.md)**
* $experimentName **mixed**
* $percentage **mixed**



### shouldTrigger

    boolean InnoCraft\Experiments\Filters\FilterInterface::shouldTrigger()





* Visibility: **public**
* This method is defined by [InnoCraft\Experiments\Filters\FilterInterface](InnoCraft-Experiments-Filters-FilterInterface.md)



