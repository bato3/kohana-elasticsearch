# Kohana Elasticsearch module
A simple Kohana [Elasticsearch](http://www.elasticsearch.org/) module.

## Usage

```php
$elasticsearch = Elasticsearch::instance('instance_name');
$elasticsearch->add('my_type', 1, array('value' => 'My value'));
```

On evry error it throws Gruzzle Exceptions

## Installation

The best way to install module is to use [Composer](https://getcomposer.org/).