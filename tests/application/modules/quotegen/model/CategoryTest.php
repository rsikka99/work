<?php

use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\CategoryModel;

class Quotegen_Model_CategoryTest extends PHPUnit_Framework_TestCase
{

    public function testCanCreateCategoryObject ()
    {
        $category = new CategoryModel();
        $this->assertInstanceOf('MPSToolbox\Legacy\Modules\QuoteGenerator\Models\CategoryModel', $category);
    }

    public function testCanPopulate ()
    {
        $category = new CategoryModel();
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