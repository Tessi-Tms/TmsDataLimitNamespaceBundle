<?php

/**
 * @author:  Gabriel BONDAZ <gabriel.bondaz@idci-consulting.fr>
 */

namespace Tms\Bundle\DataLimitNamespaceBundle\DataProvider;

interface DataProviderInterface
{
    /**
     * Count
     *
     * @param array  $data
     * @param array  $keys
     * @param string $namespace
     *
     * @return integer
     */
    public function getCount(array $data, array $keys, $namespace);

    /**
     * Check if namespace exits
     *
     * @param $namespace
     *
     * @return boolean
     */
    public function hasNamespace($namespace);

    /**
     * Check if the limit is reached
     *
     * @param array   $data
     * @param array   $keys
     * @param string  $namespace
     * @param integer $limit
     *
     * @return boolean
     */
    public function isLimitReached(array $data, array $keys, $namespace, $limit = 1);

    /**
     * Store the given data
     *
     * @param array  $data
     * @param array  $keys
     * @param string $namespace
     */
    public function store(array $data, array $keys, $namespace);
}
