<?php

namespace Tms\Bundle\DataLimitNamespaceBundle\Tests\DataProvider;

use Elastica\Client;
use Tms\Bundle\DataLimitNamespaceBundle\DataProvider\ElasticSearchDataProvider;

class ElasticSearchDataProviderTest extends \PHPUnit_Framework_TestCase
{
    const ELASTIC_SEARCH_INDEX_NAME = 'tms_limit_test';

    public function setUp()
    {
        $client = self::buildClient();
        // Delete the index if already exists
        $index = $client->getIndex(self::ELASTIC_SEARCH_INDEX_NAME);
        if ($index) {
            $index->delete();
        }
    }

    public static function buildClient()
    {
        return new Client(array('host' => 'localhost', 'port' => 9200));
    }

    public static function buildProvider()
    {
        $provider = new ElasticSearchDataProvider(
            self::buildClient(),
            self::ELASTIC_SEARCH_INDEX_NAME,
            true
        );

        return $provider;
    }

    public function testHasNamespace()
    {
        $provider = self::buildProvider();
        $data = array('key1' => 'value1', 'key2' => 'value2', 'key3' => 'value3');

        // Has namespace
        $this->assertFalse($provider->hasNamespace('has_dummy_namespace'));

        $provider->store(
            $data,
            array('key1', 'key2'),
            'has_dummy_namespace'
        );

        $this->assertTrue($provider->hasNamespace('has_dummy_namespace'));
    }

    public function testStore()
    {
        $data = array('key1' => 'value1', 'key2' => 'value2', 'key3' => 'value3');

        $provider = self::buildProvider();

        $provider->store(
            $data,
            array('key1', 'key2'),
            'dummy_namespace'
        );
        $this->assertEquals(1, $provider->getCount(
            $data,
            array('key1', 'key2'),
            'dummy_namespace'
        ));

        // Inverse keys
        $provider->store(
            $data,
            array('key2', 'key1'),
            'dummy_namespace'
        );
        $this->assertEquals(2, $provider->getCount(
            $data,
            array('key2', 'key1'),
            'dummy_namespace'
        ));

        // Change keys
        $provider->store(
            $data,
            array('key1', 'key3'),
            'dummy_namespace'
        );
        $this->assertEquals(1, $provider->getCount(
            $data,
            array('key1', 'key3'),
            'dummy_namespace'
        ));

        // Change namespace
        $provider->store(
            $data,
            array('key1', 'key2'),
            'dummy_namespace_2'
        );
        $this->assertEquals(1, $provider->getCount(
            $data,
            array('key1', 'key2'),
            'dummy_namespace_2'
        ));
    }

    public function testGetCount()
    {
        $data = array('key1' => 'value1', 'key2' => 'value2', 'key3' => 'value3');

        $provider = self::buildProvider();
        for($i = 0; $i < 200; $i++) {
            $provider->store(
                $data,
                array('key1', 'key2'),
                'get_count_dummy_namespace'
            );
        }

        $this->assertEquals(200, $provider->getCount(
            $data,
            array('key1', 'key2'),
            'get_count_dummy_namespace'
        ));
    }

    public function testIsLimitReached()
    {
        $data = array('key1' => 'value1', 'key2' => 'value2', 'key3' => 'value3');

        $provider = self::buildProvider();

        $this->assertFalse($provider->isLimitReached(
            $data,
            array('key1', 'key2'),
            'limit_reached_dummy_namespace',
            1
        ));

        $provider->store(
            $data,
            array('key1', 'key2'),
            'limit_reached_dummy_namespace'
        );

        $this->assertTrue($provider->isLimitReached(
            $data,
            array('key1', 'key2'),
            'limit_reached_dummy_namespace',
            1
        ));

        for($i = 0; $i < 29; $i++) {
            $provider->store(
                $data,
                array('key1', 'key2'),
                'limit_reached_dummy_namespace'
            );
        }

        $this->assertTrue($provider->isLimitReached(
            $data,
            array('key1', 'key2'),
            'limit_reached_dummy_namespace',
            30
        ));
    }
}