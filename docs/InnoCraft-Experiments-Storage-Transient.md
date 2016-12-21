InnoCraft\Experiments\Storage\Transient
===============

Only meant for development in tests. If you wanted to use this for some reason in production,
$data should be static in case the same experiment is created several times during one http requests




* Class name: Transient
* Namespace: InnoCraft\Experiments\Storage
* This class implements: [InnoCraft\Experiments\Storage\StorageInterface](InnoCraft-Experiments-Storage-StorageInterface.md)






Methods
-------


### get

    string|integer InnoCraft\Experiments\Storage\StorageInterface::get(string $namespace, string $key)





* Visibility: **public**
* This method is defined by [InnoCraft\Experiments\Storage\StorageInterface](InnoCraft-Experiments-Storage-StorageInterface.md)


#### Arguments
* $namespace **string**
* $key **string**



### set

    mixed InnoCraft\Experiments\Storage\StorageInterface::set(string $namespace, string $key, string|integer $value)





* Visibility: **public**
* This method is defined by [InnoCraft\Experiments\Storage\StorageInterface](InnoCraft-Experiments-Storage-StorageInterface.md)


#### Arguments
* $namespace **string**
* $key **string**
* $value **string|integer**


