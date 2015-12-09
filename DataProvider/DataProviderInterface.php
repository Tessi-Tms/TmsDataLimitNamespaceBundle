<?php

namespace Tms\DataLimitNamespaceBundle\DataProvider;

interface DataProviderInterface
{
    /**
     * Get the count
     *
     * @param array $data
     * @param array $keys
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
     * @param array $data
     * @param array $keys
     * @param string $namespace
     * @param integer $limit
     *
     * @return boolean
     */
    public function isLimitReached(array $data, array $keys, $namespace, $limit = 1);
}
