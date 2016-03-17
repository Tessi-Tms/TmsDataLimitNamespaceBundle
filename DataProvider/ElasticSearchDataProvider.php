<?php

/**
 * @author:  Gabriel BONDAZ <gabriel.bondaz@idci-consulting.fr>
 */

namespace Tms\Bundle\DataLimitNamespaceBundle\DataProvider;

use Elastica\Client;
use Elastica\Document;
use Elastica\Filter\Term;
use Elastica\Query;
use Elastica\Type\Mapping;

class ElasticSearchDataProvider implements DataProviderInterface
{
    /**
     * @var Elastica\Client
     */
    private $client;

    /**
     * @var Elastica\Index
     */
    private $index;

    /**
     * Get hash based on given data
     *
     * @param array $data
     * @param array $keys
     *
     * @return string
     */
    protected function getHash($data, $keys)
    {
        asort($keys);
        $values = array();

        foreach ($keys as $key) {
            if (isset($data[$key])) {
                $values[] = $data[$key];
            }
        }

        return md5(implode('', $values));
    }

    /**
     * Constructor
     *
     * @param Client  $client    The elastic search client.
     * @param string  $indexName The elastic search index name.
     * @param boolean $delete    Delete the index if already exist (default = false).
     */
    public function __construct(Client $client, $indexName, $delete = false)
    {
        $this->client = $client;
        $this->index  = $client->getIndex($indexName);

        // Checks if the given index is already created
        if (!$this->index->exists($indexName)) {
            // Create the index.
            $this->index->create(array(), $delete);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCount(array $data, array $keys, $namespace)
    {
        if (!$this->hasNamespace($namespace)) {
            return 0;
        }

        // Build the query
        $query = new Query();
        $query->setFields(array('hash', 'keys'));

        // Add hash term
        $term = new Term();
        $term->setTerm('hash', $this->getHash($data, $keys));
        $query->setPostFilter($term);

        return $this
            ->index
            ->getType($namespace)
            ->search($query)
            ->getTotalHits()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function hasNamespace($namespace)
    {
        return $this->index->getType($namespace)->exists();
    }

    /**
     * {@inheritdoc}
     */
    public function isLimitReached(array $data, array $keys, $namespace, $limit = 1)
    {
        return $limit <= $this->getCount($data, $keys, $namespace);
    }

    /**
     * {@inheritdoc}
     */
    public function get(array $data, array $keys, $namespace)
    {
        if (!$this->hasNamespace($namespace)) {
            return 0;
        }

        // Build the query
        $query = new Query();
        $query->setFields(array('hash', 'keys'));

        // Add hash term
        $term = new Term();
        $term->setTerm('hash', $this->getHash($data, $keys));
        $query->setPostFilter($term);

        $esResults = $this
            ->index
            ->getType($namespace)
            ->search($query)
            ->getResults()
        ;
        $results = array();

        foreach ($esResults as $esResult) {
            $results[] = $esResult->getData();
        }

        return $results;
    }

    /**
     * {@inheritdoc}
     */
    public function store(array $data, array $keys, $namespace)
    {
        $type = $this->index->getType($namespace);

        // Build mapping
        $mapping = new Mapping();
        $mapping->setType($type);
        $mapping->setProperties(array(
            'hash' => array('type' => 'string', 'include_in_all' => true),
            'keys' => array('type' => 'string', 'include_in_all' => true),
        ));
        $mapping->send();

        // Build document
        $document = new Document(
            $this->getHash($data, $keys),
            array_merge(
                $data,
                array(
                    'hash' => $this->getHash($data, $keys),
                    'keys' => $keys
                )
            )
        );

        $type->addDocument($document);
        $this->index->refresh();
    }
}
