TmsDataLimitNamespaceBundle
===========================

## How to use

This bundle provide an easy way to check if a given dataset already exist.
To do that, you have to use a provider service.

```php
$data      = array('k1' => 'toto', 'k2' => 'titi', 'k3' => 'tutu');
$namespace = 'my_namespace';
$limit     = 1;

$isReached = $this
    ->getContainer()
    ->get('tms_data_limit_namespace.provider.elasticsearch')
    ->isLimitReached($data, array('k1', 'k2'), $namespace, $limit)
;
```

In this example, you will check if the data 'k1' => 'toto', 'k2' => 'titi' exist
in the namespace "my_namespace".
So if it's the first time, this function will return false.
Now you could ask to store this dataset:

```php
$this
    ->getContainer()
    ->get('tms_data_limit_namespace.provider.elasticsearch')
    ->store($data, array('k1', 'k2'), $namespace)
;
```

A hash with the given dataset is created, inside the namespace.

In this example we use an elasticsearch data store, but you could create your own
data provider by simply implemented the [DataProviderInterface](TmsDataLimitNamespaceBundle/DataProvider/DataProviderInterface.php).

Ask an other one if the limit is reached:

```php
// With a limit 1, will return true.
$this
    ->getContainer()
    ->get('tms_data_limit_namespace.provider.elasticsearch')
    ->isLimitReached($data, array('k1', 'k2'), $namespace, 1)
;

// With a limit 2, will return false.
$this
    ->getContainer()
    ->get('tms_data_limit_namespace.provider.elasticsearch')
    ->isLimitReached($data, array('k1', 'k2'), $namespace, 2)
;
```
