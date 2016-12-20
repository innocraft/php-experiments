PHP Experiments is an A/B Test and Split Test library
=========================

## Code Status

[![Build Status](https://travis-ci.org/innocraft/php-experiments.svg?branch=master)](https://travis-ci.org/innocraft/php-experiments)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/innocraft/php-experiments.svg)](https://scrutinizer-ci.com/g/innocraft/php-experiments?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/innocraft/php-experiments/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/innocraft/php-experiments/?branch=master "Unit tests code coverage. Does not include coverage of integration tests, system tests or UI screenshot tests.")
[![Latest Stable Version](https://poser.pugx.org/innocraft/php-experiments/v/stable)](https://packagist.org/packages/innocraft/php-experiments)
[![License](https://poser.pugx.org/innocraft/php-experiments/license)](https://packagist.org/packages/innocraft/php-experiments)

## Introduction 

PHP Experiments is built for [A/B and Split Testing for Piwik Analytics](https://www.ab-tests.net) but can be used in any 
PHP project. [Piwik](https://piwik.org) is the leading open source web analytics platform used on over 1 million 
websites and apps in over 150 countries. [InnoCraft](https://www.innocraft.com) is the company of the makers of Piwik Analytics.

## Features

* Easily create and run A/B tests and split tests.
* Experiment traffic allocation: For example configure that only 80% of all users should participate in an experiment.
* Variation traffic allocation: For example allocate more traffic to some specific variations, giving other variations less traffic.
* When a user enters an experiment for the first time, a variation will be chosen randomly. On all subsequent requests the same variation will be activated.
* Possibility to force a specific variation instead of choosing it randomly.
* To ensure a user always gets to see the same variation, the name of the activated variation is stored in a cookie, a custom storage can be configured.
* Easy to use with plain arrays and easily extensible and customizable (custom storage, filters and variations can be defined).
* Tested with unit tests.
* No dependencies, lightweight, simple.

## Example

See [examples](examples) directory for various examples. 

Running an A/B test might be as easy as:

```php
$variations = [['name' => 'green'], ['name' => 'blue']];
$experiment = new Experiment('experimentName', $variations);
$activated = $experiment->getActivatedVariation();

echo $activated->getName();
```

Running a split test can be as easy as:

```php
$variations = [
    ['name' => 'layout1', 'url' => '/layout1'], 
    ['name' => 'layout2', 'url' => '/layout2']
];
$experiment = new Experiment('experimentName', $variations);
$activated = $experiment->getActivatedVariation();

// redirects to either URL or does nothing if the original version was activated
$activated->run();
```

## Requirements

* PHP 5.5.9 or greater

## Installation

To get the latest version, require the library using [Composer](https://getcomposer.org):

```bash
$ composer require innocraft/php-experiments
```

Instead, you may manually update your require block and run `composer update` if you so choose:

```json
{
    "require": {
        "innocraft/php-experiments": "^1.0"
    }
}
```

## License

PHP Experiments is released under the LGPL v3 license, see [LICENSE](LICENSE).

## Developer

### Docs generation

To update the documentation within the docs directory execute the following commands

* `cd docs`
* `composer install` (only needed once)
* `./generateDocs.sh`