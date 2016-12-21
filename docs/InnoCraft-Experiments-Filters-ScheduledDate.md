InnoCraft\Experiments\Filters\ScheduledDate
===============






* Class name: ScheduledDate
* Namespace: InnoCraft\Experiments\Filters
* This class implements: [InnoCraft\Experiments\Filters\FilterInterface](InnoCraft-Experiments-Filters-FilterInterface.md)






Methods
-------


### __construct

    mixed InnoCraft\Experiments\Filters\ScheduledDate::__construct(string|\DateTimeInterface $now, null|string|\DateTimeInterface $startDate, null|string|\DateTimeInterface $endDate)

ScheduledDate constructor.

When passing a string:
Date values separated by slash are assumed to be in American order: m/d/y
Date values separated by dash are assumed to be in European order: d-m-y

* Visibility: **public**


#### Arguments
* $now **string|DateTimeInterface**
* $startDate **null|string|DateTimeInterface** - &lt;p&gt;null if no start date is given and it is valid from any time&lt;/p&gt;
* $endDate **null|string|DateTimeInterface** - &lt;p&gt;null if no end date is given and it is valid &quot;unlimited&quot;&lt;/p&gt;



### shouldTrigger

    boolean InnoCraft\Experiments\Filters\FilterInterface::shouldTrigger()





* Visibility: **public**
* This method is defined by [InnoCraft\Experiments\Filters\FilterInterface](InnoCraft-Experiments-Filters-FilterInterface.md)



