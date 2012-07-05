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
        $data = array(
                	'id' => 1,
                	'name' =>  'Finisher',
                	'description' => 'A fine finisher'
                );
        
        $this->assertAttributeEmpty('id', $category);
        $this->assertAttributeEmpty('name', $category);
        $this->assertAttributeEmpty('description', $category);
        $category->populate($data);
        $this->assertNotEmpty($category);
    }
}