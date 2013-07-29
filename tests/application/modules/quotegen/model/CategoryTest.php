<?php

class Quotegen_Model_CategoryTest extends PHPUnit_Framework_TestCase
{

    public function testCanCreateCategoryObject ()
    {
        $category = new Quotegen_Model_Category();
        $this->assertInstanceOf('Quotegen_Model_Category', $category);
    }

    public function testCanPopulate ()
    {
        $category = new Quotegen_Model_Category();
        $data     = array(
            'id'          => 1,
            'dealerId'    => 1,
            'name'        => 'Finisher',
            'description' => 'A fine finisher'
        );

        $category->populate($data);
        $this->assertSame($data, $category->toArray());
    }
}