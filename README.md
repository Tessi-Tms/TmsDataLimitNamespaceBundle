TmsDataLimitNamespaceBundle
===========================

Symfony2 data limit namespace bundle.


Installation
------------

Add dependencies in your `composer.json` file:
```json
"repositories": [
    ...,
    {
        "type": "vcs",
        "url": "https://github.com/Tessi-Tms/TmsDataLimitNamespaceBundle.git"
    }
],
"require": {
    ...,
    "tms/data-limit-namespace-bundle": "dev-master"
}
```

Install these new dependencies of your application:
```sh
$ php composer.phar update
```

Enable bundles in your application kernel:
```php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Tms\Bundle\DataLimitNamespaceBundle\TmsDataLimitNamespaceBundle(),
    );
}
```

Import the bundle configuration:
```yml
# app/config/config.yml

imports:
    - { resource: @TmsDataLimitNamespaceBundle/Resources/config/config.yml }
```

Define variable values in `parameters.yml`:
```
parameters:
    elasticsearch_host: localhost
    elasticsearch_port: 9200
    tms_data_limit_namespace.elasticsearch_index_name: 'tms_limit'
```


Documentation
-------------

[Read the Documentation](Resources/doc/index.md)


Tests
-----

Install bundle dependencies:
```sh
$ php composer.phar update
```

To execute unit tests:
```sh
$ phpunit --coverage-text
```
