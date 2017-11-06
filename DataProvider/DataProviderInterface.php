<?php

/**
 * @author:  Gabriel BONDAZ <gabriel.bondaz@idci-consulting.fr>
 * @author:  Nabil Mansouri <nabil.mansouri@tessi.fr>
 */

namespace Tms\Bundle\DataLimitNamespaceBundle\DataProvider;

interface DataProviderInterface
{
    /**
     * Generate hash based on given data
     *
     * @param array $data
     * @param array $keys
     *
     * @return string
     */
    public function generatetHash(array $data, array $keys);

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
     * Retrieve data.
     *
     * @param array  $data
     * @param array  $keys
     * @param string $namespace
     *
     * @return array
     */
    public function get(array $data, array $keys, $namespace);

    /**
     * Store the given data
     *
     * @param string $id
     * @param array  $data
     * @param array  $keys
     * @param string $namespace
     */
    public function store(array $data, array $keys, $namespace, $id);
}
