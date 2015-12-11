<?php

namespace Tms\DataLimitNamespaceBundle\DataProvider;

use Elastica\Client;
use Elastica\Document;
use Elastica\Search;
use Elastica\Filter\BoolAnd;
use Elastica\Filter\Term;
use Elastica\Query;
use Elastica\Query\QueryString;
use Elastica\Query\Match;
use Elastica\Type\Mapping;

class ElasticSearchDataProvider implements DataProviderInterface
{
    private $client;
    private $esIndex;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->client = new Client(array('localhost', 9200));
        $this->esIndex = $this->client->getIndex('limit_data');

        // Checks if the index is already created
        if (!$this->esIndex->exists()) {
            $this->esIndex->create();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCount(array $data, array $keys, $namespace)
    {
        if (!$this->hasNamespace($namespace)) {
            $this->defineMapping($namespace);
        }

        $values = $this->getValues($data, $keys);
        $hash = $this->getHash($values);

        $esTerm = new Term();
        $esFilterAnd = new BoolAnd();

        $esTerm->setTerm('hash', $hash);

        foreach ($keys as $key => $value) {
            $esTerm->setTerm('keys', $value);
        }

        $esFilterAnd->addFilter($esTerm);

        $esQuery = new Query();
        $esQuery->setFields(['hash', 'keys']);
        $esQuery->setPostFilter($esFilterAnd);

        return $this->esIndex->getType($namespace)->search($esQuery)->count();
    }

    /**
     * {@inheritdoc}
     */
    public function hasNamespace($namespace)
    {
        return $this->esIndex->getType($namespace)->exists();
    }

    /**
     * {@inheritdoc}
     */
    public function isLimitReached(array $data, array $keys, $namespace, $limit = 1)
    {
        $count = $this->getCount($data, $keys, $namespace);

        return $count >= $limit;
    }

    /**
     * {@inheritdoc}
     */
    public function store(array $data, array $keys, $namespace)
    {
        $values = $this->getValues($data, $keys);
        $hash = $this->getHash($values);

        $this->esIndex->getType($namespace)->addDocument(new Document(
            '',
            array(
                'hash' => $hash,
                'keys' => $keys
            )
        ));

        $this->esIndex->refresh();
    }

    /**
     * Define a mapping
     *
     * @param string $namespace
     */
    private function defineMapping($namespace)
    {
        $mapping = new Mapping();
        $mapping->setType($this->esIndex->getType($namespace));
        $mapping->setProperties(array(
            'hash'=> array('type' => 'string'),
            'keys'=> array('type' => 'string'),
        ));
        $mapping->send();
    }

    /**
     * Get the values of the given data with the given keys
     *
     * @param array $data
     * @param array $keys
     *
     * @return array
     */
    public function getValues(array $data, array $keys)
    {
        $result = array();
        sort($keys);

        foreach($keys as $key) {
            $result[] = $data[$key];
        }

        return $result;
    }

    /**
     * Get hash based on given data
     *
     * @param string|array $data
     *
     * @return string
     */
    public function getHash($data)
    {
        if (is_string($data)) {
            return md5($data);
        } elseif (is_array($data)) {
            $string  = "";

            foreach($data as $value) {
                $string .= $value;
            }

            return md5($string);
        }
    }
}
