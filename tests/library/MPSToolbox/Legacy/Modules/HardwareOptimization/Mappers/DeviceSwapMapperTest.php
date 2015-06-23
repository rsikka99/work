<?php
use MPSToolbox\Legacy\Modules\HardwareOptimization\Mappers\DeviceSwapMapper;
use MPSToolbox\Legacy\Modules\HardwareOptimization\Models\DeviceSwapModel;

/**
 * Class MPSToolbox_Settings_Form_AllSettingsTest
 *
 * @property DeviceSwapMapper deviceSwapMapper
 */
class MPSToolbox_Legacy_Modules_HardwareOptimization_Mappers_DeviceSwapMapper extends My_DatabaseTestCase
{

    public $fixtures = [
        'dealers','toner_configs','manufacturers','users','master_devices','toner_colors','toners','device_toners'
    ];

    public function setUp()
    {
        $this->deviceSwapMapper = DeviceSwapMapper::getInstance();
        parent::setup();
    }

    public function tearDown()
    {
        DeviceSwapMapper::getInstance()->delete(array(1,2));
        DeviceSwapMapper::getInstance()->delete(array(1,1));
        DeviceSwapMapper::getInstance()->delete(array(2,1));
        DeviceSwapMapper::getInstance()->delete(array(3,1));
        parent::tearDown();
    }

    public function testInsert() {
        $obj = new DeviceSwapModel(array(
            'masterDeviceId'=>1,
            'dealerId'=>2,
            'minimumPageCount'=>3,
            'maximumPageCount'=>4,
        ));
        $result = $this->deviceSwapMapper->insert($obj);
        $this->assertEquals(array(
            'masterDeviceId'=>1,
            'dealerId'=>2,
        ), $result);
    }

    public function testSave() {
        $obj = new DeviceSwapModel(array(
            'masterDeviceId'=>1,
            'dealerId'=>2,
            'minimumPageCount'=>3,
            'maximumPageCount'=>4,
        ));
        $this->deviceSwapMapper->insert($obj);

        $obj->minimumPageCount = 5;
        $result = $this->deviceSwapMapper->save($obj);
        $this->assertEquals(1, $result);
    }

    public function testDelete() {
        $obj = new DeviceSwapModel(array(
            'masterDeviceId'=>1,
            'dealerId'=>2,
            'minimumPageCount'=>3,
            'maximumPageCount'=>4,
        ));
        $this->deviceSwapMapper->insert($obj);

        $result = $this->deviceSwapMapper->delete($obj);
        $this->assertEquals(true, $result);
    }

    public function testFind() {
        $obj = new DeviceSwapModel(array(
            'masterDeviceId'=>1,
            'dealerId'=>2,
            'minimumPageCount'=>3,
            'maximumPageCount'=>4,
        ));
        $this->deviceSwapMapper->insert($obj);

        $this->deviceSwapMapper->clearItemCache();
        $found = $this->deviceSwapMapper->find(array(1,2));

        $this->assertEquals($found, $obj);
    }

    public function testFetch() {
        $obj = new DeviceSwapModel(array(
            'masterDeviceId'=>1,
            'dealerId'=>2,
            'minimumPageCount'=>3,
            'maximumPageCount'=>4,
        ));
        $this->deviceSwapMapper->insert($obj);

        $this->deviceSwapMapper->clearItemCache();
        $found = $this->deviceSwapMapper->fetch($this->deviceSwapMapper->getDbTable()->select()->where('masterDeviceId=1 and dealerId=2'));

        $this->assertEquals($found, $obj);
    }

    public function testFetchAll() {
        $obj = new DeviceSwapModel(array(
            'masterDeviceId'=>1,
            'dealerId'=>2,
            'minimumPageCount'=>3,
            'maximumPageCount'=>4,
        ));
        $this->deviceSwapMapper->insert($obj);

        $this->deviceSwapMapper->clearItemCache();
        $found = $this->deviceSwapMapper->fetchAll($this->deviceSwapMapper->getDbTable()->select()->where('masterDeviceId=1 and dealerId=2'));

        $this->assertEquals($found, [$obj]);
    }

    public function testWhereId() {
        $obj = new DeviceSwapModel(array(
            'masterDeviceId'=>1,
            'dealerId'=>2,
            'minimumPageCount'=>3,
            'maximumPageCount'=>4,
        ));
        $this->deviceSwapMapper->insert($obj);

        $this->deviceSwapMapper->clearItemCache();
        $found = $this->deviceSwapMapper->fetch($this->deviceSwapMapper->getWhereId(array(1,2)));

        $this->assertEquals($found, $obj);
    }

    public function testGetPrimaryKeyValueForObject() {
        $obj = new DeviceSwapModel(array(
            'masterDeviceId'=>1,
            'dealerId'=>2,
            'minimumPageCount'=>3,
            'maximumPageCount'=>4,
        ));
        $result = $this->deviceSwapMapper->getPrimaryKeyValueForObject($obj);
        $this->assertEquals($result, array(1,2));
    }

    public function testFetAllForDealerCount() {
        $obj = new DeviceSwapModel(array(
            'masterDeviceId'=>1,
            'dealerId'=>1,
            'minimumPageCount'=>3,
            'maximumPageCount'=>4,
        ));
        $this->deviceSwapMapper->insert($obj);
        $result = $this->deviceSwapMapper->fetAllForDealer(1,null,null,null,null,true);
        $this->assertEquals($result, 1);
    }

    public function testFetAllForDealer() {
        $obj = new DeviceSwapModel(array(
            'masterDeviceId'=>1,
            'dealerId'=>1,
            'minimumPageCount'=>3,
            'maximumPageCount'=>4,
        ));
        $this->deviceSwapMapper->insert($obj);
        $obj = new DeviceSwapModel(array(
            'masterDeviceId'=>2,
            'dealerId'=>1,
            'minimumPageCount'=>3,
            'maximumPageCount'=>4,
        ));
        $this->deviceSwapMapper->insert($obj);
        $obj = new DeviceSwapModel(array(
            'masterDeviceId'=>3,
            'dealerId'=>1,
            'minimumPageCount'=>3,
            'maximumPageCount'=>4,
        ));
        $this->deviceSwapMapper->insert($obj);

        $cppModel = new MPSToolbox\Legacy\Modules\ProposalGenerator\Models\CostPerPageSettingModel([
            "adminCostPerPage"              => 1,
            "monochromePartsCostPerPage"    => 1,
            "monochromeLaborCostPerPage"    => 1,
            "colorPartsCostPerPage"         => 1,
            "colorLaborCostPerPage"         => 1,
            "pageCoverageMonochrome"        => 1,
            "pageCoverageColor"             => 1,
            "monochromeTonerRankSet"        => new MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerVendorRankingSetModel(),
            "colorTonerRankSet"             => new MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerVendorRankingSetModel(),
            "useDevicePageCoverages"        => 1,
            "customerMonochromeCostPerPage" => 1,
            "customerColorCostPerPage"      => 1,
            "clientId"                      => 1,
            "dealerId"                      => 1,
            "pricingMargin"                 => 1,
        ]);
        $result = $this->deviceSwapMapper->fetAllForDealer(1,$cppModel,'masterDeviceId');
        $this->assertEquals(3, count($result));
        $this->assertEquals('Color', $result[0]['deviceType']);
        $this->assertEquals('Brother HL-3040CN', $result[0]['device_name']);
        $this->assertEquals('0.0052341597796143', $result[0]['monochromeCpp']);
        $this->assertEquals('0.010897940443395', $result[0]['colorCpp']);
        $this->assertEquals('Monochrome', $result[1]['deviceType']);
        $this->assertEquals('Brother HL-5370DW', $result[1]['device_name']);
        $this->assertEquals('0.0022727272727273', $result[1]['monochromeCpp']);
        $this->assertEquals('Monochrome', $result[2]['deviceType']);
        $this->assertEquals('Brother HL-6050D', $result[2]['device_name']);
        $this->assertEquals('0.0023164983164983', $result[2]['monochromeCpp']);
    }

}