<?php

class My_FeatureTest extends PHPUnit_Framework_TestCase
{
    protected $validFeatures = array(
        'my_random_feature',
    );

    public function setUp ()
    {
        $mockAdapter = $this->getMock('My_Feature_AdapterInterface', array('getFeatures'));
        $mockAdapter->expects($this->any())->method('getFeatures')->will($this->returnValue($this->validFeatures));
        My_Feature::setAdapter($mockAdapter);

        parent::setUp();
    }

    /**
     * Test the get and set adapter
     */
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

    /**
     *   Test whether a given feature is allowed to be accessed
     */
    public function testCanAccess ()
    {
        $feature = $this->validFeatures[0];
        $this->assertTrue(My_Feature::canAccess($feature));
    }

    /**
     *   Test to ensure getFeatures in base class can access adapter features
     */
    public function testGetFeatures ()
    {
        $this->assertEquals(My_Feature::getFeatures(), $this->validFeatures);
    }
}