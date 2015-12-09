<?php

namespace Tms\DataLimitNamespaceBundle\Tests\DataProvider;

use Tms\DataLimitNamespaceBundle\DataProvider\ElasticSearchDataProvider;

class ElasticSearchDataProviderTest extends \PHPUnit_Framework_TestCase
{

    public function testHasNamespace()
    {
        $elasticSearchDataProvider = new ElasticSearchDataProvider();

        $this->assertEquals(false, $elasticSearchDataProvider->hasNamespace('titi'));
    }
}