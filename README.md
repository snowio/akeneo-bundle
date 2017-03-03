# Snowio Bundle for Akeneo

This bundle provides a new connector called Snowio Connector that allow us to export all the data from just on job, zip them and send to snowio endpoint.

* Complete Job
    * Export (products, variant groups, categories, attributes, attribute options, and families)  
* Incomplete Job
    * Export (categories, attributes, attribute options, and families)

Both of them have as final steps: Generate metadata, Zip files, send to Snowio using Guzzle.

### Installation

You can install this bundle via composer.
```
composer require snowio/akeneo-bundle ^1.6
```