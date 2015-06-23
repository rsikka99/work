<?php
use MPSToolbox\Legacy\Modules\HardwareOptimization\Models\HardwareOptimizationModel;

/**
 * @property HardwareOptimizationModel model
 */
class MPSToolbox_Legacy_Modules_HardwareOptimization_Models_HardwareOptimizationModelTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->model = new HardwareOptimizationModel();
    }

    public function tearDown()
    {
    }

    public function testPopulate() {
        $this->model->populate($data=[
            "id"           => 1,
            "clientId"     => 2,
            "dealerId"     => 3,
            "dateCreated"  => '4',
            "lastModified" => '6',
            "name"         => '7',
            "rmsUploadId"  => 8,
            "stepName"     => '9',
        ]);
        $this->assertEquals($data,get_object_vars($this->model));
    }

    public function testToArray() {
        $this->model->populate($data=[
            "id"           => 1,
            "clientId"     => 2,
            "dealerId"     => 3,
            "dateCreated"  => '4',
            "lastModified" => '6',
            "name"         => '7',
            "rmsUploadId"  => 8,
            "stepName"     => '9',
        ]);
        $this->assertEquals($data,$this->model->toArray());
    }

    public function testGetFormattedDatePrepared() {
        $this->model->dateCreated = '2007-07-20';
        $result = $this->model->getFormattedDatePrepared();
        $this->assertEquals('July 20th, 2007',$result);
    }

}


