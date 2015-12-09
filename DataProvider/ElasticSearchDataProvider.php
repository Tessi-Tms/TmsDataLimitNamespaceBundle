<?php

namespace Tms\DataLimitNamespaceBundle\DataProvider;

class ElasticSearchDataProvider implements DataProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getCount(array $data, array $keys, $namespace)
    {
        // Reorder keys
        // Return the count of hash based on given namespace & data
    }

    /**
     * {@inheritdoc}
     */
    public function hasNamespace($namespace)
    {
        // Check in elastic search if namespace exist
        // Return true if exist

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isLimitReached(array $data, array $keys, $namespace, $limit = 1)
    {
        // Reorder keys
        // Get the count of hash based on given namespace, data & limit
        // count lower than limit return false

        return true;
    }
}