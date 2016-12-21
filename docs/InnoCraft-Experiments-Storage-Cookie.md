InnoCraft\Experiments\Storage\Cookie
===============

Stores the values in a cookie to make sure on subsequent requests the same variation will be activated.




* Class name: Cookie
* Namespace: InnoCraft\Experiments\Storage
* This class implements: [InnoCraft\Experiments\Storage\StorageInterface](InnoCraft-Experiments-Storage-StorageInterface.md)




Properties
----------


### $data

    private array $data = array()

This is static in case the same experiment is created several times during one http request
to make sure we always activate the same variation even within one request



* Visibility: **private**
* This property is **static**.


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



### toName

    mixed InnoCraft\Experiments\Storage\Cookie::toName($namespace, $key)





* Visibility: **private**


#### Arguments
* $namespace **mixed**
* $key **mixed**


