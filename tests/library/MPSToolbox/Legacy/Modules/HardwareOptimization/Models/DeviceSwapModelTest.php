<?php
use MPSToolbox\Legacy\Modules\HardwareOptimization\Models\DeviceSwapModel;
use MPSToolbox\Legacy\Modules\HardwareOptimization\Mappers\DeviceSwapMapper;

/**
 * Class MPSToolbox_Settings_Form_AllSettingsTest
 *
 * @property DeviceSwapModel deviceSwapModel
 */
class MPSToolbox_Legacy_Modules_HardwareOptimization_Models_DeviceSwapModelTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->deviceSwapModel = new DeviceSwapModel();
    }

    public function tearDown()
    {
        DeviceSwapMapper::getInstance()->delete(array(1,2));
    }

    public function testPopulate() {
        $this->deviceSwapModel->populate(array(
            'masterDeviceId'=>1,
            'dealerId'=>2,
            'minimumPageCount'=>3,
            'maximumPageCount'=>4,
        ));
        $arr = get_object_vars($this->deviceSwapModel);
        $this->assertEquals(array(
            'masterDeviceId'=>1,
            'dealerId'=>2,
            'minimumPageCount'=>3,
            'maximumPageCount'=>4,
        ),$arr);
    }

    public function testToArray()
    {
        $this->deviceSwapModel->populate(array(
            'masterDeviceId'=>1,
            'dealerId'=>2,
            'minimumPageCount'=>3,
            'maximumPageCount'=>4,
        ));
        $arr = $this->deviceSwapModel->toArray();
        $this->assertEquals(array(
            'masterDeviceId'=>1,
            'dealerId'=>2,
            'minimumPageCount'=>3,
            'maximumPageCount'=>4,
        ),$arr);
    }

    public function testGetMasterDevice() {
        $obj = $this->deviceSwapModel->getMasterDevice();
        $this->assertEquals(false, $obj);

        $this->deviceSwapModel->masterDeviceId = 1;
        $obj = $this->deviceSwapModel->getMasterDevice();
        $this->assertfalse(empty($obj));
        $this->assertTrue($obj instanceof MPSToolbox\Legacy\Modules\ProposalGenerator\Models\MasterDeviceModel);
        $this->assertEquals(1, $obj->id);
    }

    public function testSaveObject() {
        $result = $this->deviceSwapModel->saveObject(array(
            'masterDeviceId'=>1,
            'dealerId'=>2,
            'minimumPageCount'=>3,
            'maximumPageCount'=>4,
        ));
        $this->assertTrue($result);
    }


}