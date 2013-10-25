<?php

class My_FeatureTest extends PHPUnit_Framework_TestCase
{
    public function testGetAdapterCanBeSet ()
    {
        $testArray     = array('mapperClassName' => 'test');
        $mapperAdapter = new My_Feature_MapperAdapter($testArray);
        My_Feature::setAdapter($mapperAdapter);
        $adapter = null;

        try
        {
            $adapter = My_Feature::getAdapter();
        }
        catch (Exception $e)
        {
            $this->fail("Failed because get adapter sent an exception");
        }

        $this->assertEquals($mapperAdapter, $adapter, "Failed to get the correct mapperAdapter");
    }
}