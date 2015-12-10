<?php

namespace Tms\DataLimitNamespaceBundle\Tests\DataProvider;

use Tms\DataLimitNamespaceBundle\DataProvider\ElasticSearchDataProvider;

class ElasticSearchDataProviderTest extends \PHPUnit_Framework_TestCase
{

    public function testHasNamespace()
    {
        $elasticSearchDataProvider = new ElasticSearchDataProvider();

        $this->assertEquals(false, $elasticSearchDataProvider->hasNamespace('brahim'));
        $this->assertEquals(true, $elasticSearchDataProvider->hasNamespace('op2'));
    }

    public function testGetCount()
    {
        $elasticSearchDataProvider = new ElasticSearchDataProvider();

        $this->assertEquals(0, $elasticSearchDataProvider->getCount(
            array(
                'name'    => 'Brahim',
                'address' => '1 rue Peter Parker',
                'age'     => 25
            ),
            array('name', 'address'),
            'test'
        ));
    }
}