# Kohana Elasticsearch module
A simple Kohana [Elasticsearch](http://www.elasticsearch.org/) module.

## Usage

```php
$elasticsearch = Elasticsearch::instance('custom_index');
$elasticsearch->add('my_type', 1, array('value' => 'My value'));
```

## Installation

The best way to install module is to use [Composer](https://getcomposer.org/).