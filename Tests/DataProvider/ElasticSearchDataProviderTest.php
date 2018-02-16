<?php

namespace Tms\Bundle\DataLimitNamespaceBundle\Tests\DataProvider;

use Elastica\Client;
use Elastica\Connection;
use Tms\Bundle\DataLimitNamespaceBundle\DataProvider\ElasticSearchDataProvider;

/**
 * Class ElasticSearchDataProviderTest.
 */
class ElasticSearchDataProviderTest extends \PHPUnit_Framework_TestCase
{
    const ELASTIC_SEARCH_INDEX_NAME = 'tms_limit_test';

    private $client;
    private $provider;

    /**
     * Set up.
     */
    public function setUp()
    {
        $this->client = new Client(array('host' => $this->getHost(), 'port' => $this->getPort()));
        $this->provider = new ElasticSearchDataProvider(
            $this->client,
            self::ELASTIC_SEARCH_INDEX_NAME,
            true
        );
    }

    /**
     * Tear down.
     */
    public function tearDown()
    {
        // Delete the index if already exists
        $index = $this->client->getIndex(self::ELASTIC_SEARCH_INDEX_NAME);
        if ($index) {
            $index->delete();
        }
    }

    /**
     * Check if ES_HOSt env variable exists and returns its. Returns default host otherwise.
     *
     * @return string
     */
    protected function getHost()
    {
        return getenv('ES_HOST') ?: Connection::DEFAULT_HOST;
    }

    /**
     * Check if ES_PORT env variable exists and returns its. Returns default port otherwise.
     *
     * @return int
     */
    protected function getPort()
    {
        return getenv('ES_PORT') ?: Connection::DEFAULT_PORT;
    }

    /**
     * Test hasNamespace.
     */
    public function testHasNamespace()
    {
        $data = array('key1' => 'value1', 'key2' => 'value2', 'key3' => 'value3');

        // Has namespace
        $this->assertFalse($this->provider->hasNamespace('has_dummy_namespace'));

        $this->provider->store(
            $data,
            array('key1', 'key2'),
            'has_dummy_namespace'
        );

        $this->assertTrue($this->provider->hasNamespace('has_dummy_namespace'));
    }

    /**
     * Test store.
     */
    public function testStore()
    {
        $data = array('key1' => 'value1', 'key2' => 'value2', 'key3' => 'value3');

        $this->provider->store(
            $data,
            array('key1', 'key2'),
            'dummy_namespace'
        );
        $this->assertEquals(1, $this->provider->getCount(
            $data,
            array('key1', 'key2'),
            'dummy_namespace'
        ));

        // Inverse keys
        $this->provider->store(
            $data,
            array('key2', 'key1'),
            'dummy_namespace'
        );
        $this->assertEquals(2, $this->provider->getCount(
            $data,
            array('key2', 'key1'),
            'dummy_namespace'
        ));

        // Change keys
        $this->provider->store(
            $data,
            array('key1', 'key3'),
            'dummy_namespace'
        );
        $this->assertEquals(1, $this->provider->getCount(
            $data,
            array('key1', 'key3'),
            'dummy_namespace'
        ));

        // Change namespace
        $this->provider->store(
            $data,
            array('key1', 'key2'),
            'dummy_namespace_2'
        );
        $this->assertEquals(1, $this->provider->getCount(
            $data,
            array('key1', 'key2'),
            'dummy_namespace_2'
        ));
    }

    /**
     * Test getCount.
     */
    public function testGetCount()
    {
        $data = array('key1' => 'value1', 'key2' => 'value2', 'key3' => 'value3');

        for ($i = 0; $i < 200; ++$i) {
            $this->provider->store(
                $data,
                array('key1', 'key2'),
                'get_count_dummy_namespace'
            );
        }

        $this->assertEquals(200, $this->provider->getCount(
            $data,
            array('key1', 'key2'),
            'get_count_dummy_namespace'
        ));
    }

    /**
     * Test isLimitReached.
     */
    public function testIsLimitReached()
    {
        $data = array('key1' => 'value1', 'key2' => 'value2', 'key3' => 'value3');

        $this->assertFalse($this->provider->isLimitReached(
            $data,
            array('key1', 'key2'),
            'limit_reached_dummy_namespace',
            1
        ));

        $this->provider->store(
            $data,
            array('key1', 'key2'),
            'limit_reached_dummy_namespace'
        );

        $this->assertTrue($this->provider->isLimitReached(
            $data,
            array('key1', 'key2'),
            'limit_reached_dummy_namespace',
            1
        ));

        for ($i = 0; $i < 29; ++$i) {
            $this->provider->store(
                $data,
                array('key1', 'key2'),
                'limit_reached_dummy_namespace'
            );
        }

        $this->assertTrue($this->provider->isLimitReached(
            $data,
            array('key1', 'key2'),
            'limit_reached_dummy_namespace',
            30
        ));
    }
}
