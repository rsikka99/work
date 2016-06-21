<?php

use MPSToolbox\Legacy\Modules\HardwareLibrary\Services\ManageMasterDevicesService;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\TonerMapper;
use \MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerModel;
use \MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerColorModel;
use \MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerConfigModel;


/**
 * Class Modules_HardwareLibrary_Services_ManageMasterDevicesServiceTest
 * @property ManageMasterDevicesService $service
 */
class Modules_HardwareLibrary_Services_ManageMasterDevicesServiceTest extends My_DatabaseTestCase
{

    public $fixtures = []; //'dealers','manufacturers','users','master_devices','toner_configs','toner_colors','toners','device_toners'];

    public function setUp() {
        parent::setUp();
        $this->service = new ManageMasterDevicesService(1,1);
    }

    public function test_getForms() {
        $result = $this->service->getForms();
        $expected = array(
            'deviceAttributes',
            'hardwareOptimization',
            'hardwareQuote',
            'availableOptions',
            'availableOptionsForm',
            'hardwareConfigurations',
            'suppliesAndService',
            'deviceImage',
            'delete',
        );
        $this->assertEquals($expected,array_keys($result));
    }

    public function test_validateData() {
        $form = new My_Form_Form();
        $form->addElement('text', 'bar', []);
        $data = ['bar'=>'baz','barr'=>'bazz'];
        $formName = 'foo';
        $result = $this->service->validateData($form, $data, $formName);
        $this->assertEquals(['bar'=>'baz'], $result);

        $form->addElement('text', 'baz', ['required'=>true]);
        $result = $this->service->validateData($form, $data, $formName);
        $this->assertEquals(['errorMessages'=>['baz'=>'Value is required and can\'t be empty'],'name'=>'foo'], $result);
    }

    public function test_validateToners() {
        TonerMapper::getInstance()->saveItemToCache(new TonerModel(['id'=>1001,'manufacturerId'=>1,'tonerColorId'=>TonerColorModel::BLACK]));
        TonerMapper::getInstance()->saveItemToCache(new TonerModel(['id'=>1002,'manufacturerId'=>1,'tonerColorId'=>TonerColorModel::CYAN]));
        TonerMapper::getInstance()->saveItemToCache(new TonerModel(['id'=>1003,'manufacturerId'=>1,'tonerColorId'=>TonerColorModel::MAGENTA]));
        TonerMapper::getInstance()->saveItemToCache(new TonerModel(['id'=>1004,'manufacturerId'=>1,'tonerColorId'=>TonerColorModel::YELLOW]));
        TonerMapper::getInstance()->saveItemToCache(new TonerModel(['id'=>1005,'manufacturerId'=>1,'tonerColorId'=>TonerColorModel::THREE_COLOR]));
        TonerMapper::getInstance()->saveItemToCache(new TonerModel(['id'=>1006,'manufacturerId'=>1,'tonerColorId'=>TonerColorModel::FOUR_COLOR]));

        $result = $this->service->validateToners([1001], TonerConfigModel::BLACK_ONLY, 1);
        $this->assertEquals(true, $result);

        $result = $this->service->validateToners([1001,1002,1003,1004], TonerConfigModel::THREE_COLOR_SEPARATED, 1);
        $this->assertEquals(true, $result);

        $result = $this->service->validateToners([1001,1005], TonerConfigModel::THREE_COLOR_COMBINED, 1);
        $this->assertEquals(true, $result);

        $result = $this->service->validateToners([1006], TonerConfigModel::FOUR_COLOR_COMBINED, 1);
        $this->assertEquals(true, $result);

        $result = $this->service->validateToners([1006], TonerConfigModel::BLACK_ONLY, 1);
        $this->assertEquals('Missing Black OEM Toner. Four Color Toners cannot be assigned to this device.', $result);

        TonerMapper::getInstance()->clearItemCache();

        $this->setup_fixtures(['toner_configs','dealers','manufacturers','users','base_printer','toner_colors','base_printer_cartridge','oem_printing_device_consumable']);
        $result = $this->service->validateToners([1,2,3,4], TonerConfigModel::THREE_COLOR_SEPARATED, 1, 1);
        $this->assertEquals(true, $result);
    }

    /**
     * @todo
     *
    public function test_recalculateMaximumRecommendedMonthlyPageVolume() {

    }
    public function test_saveSuppliesAndDeviceAttributes() {

    }
    public function test_uploadImage() {

    }
    public function test_downloadImageFromImageUrl() {

    }
    public function test_saveHardwareOptimization() {

    }
    public function test_saveHardwareQuote() {

    }
    public function test_getSuppliesAndServicesForm() {

    }
    public function test_getDeviceAttributesForm() {

    }
    public function test_getDeviceImageForm() {

    }
    public function test_getHardwareOptimizationForm() {

    }
    public function test_getHardwareQuoteForm() {

    }
    public function test_getHardwareConfigurationsForm() {

    }
    public function test_getAvailableOptionsForm() {

    }
    public function test_getAvailableTonersForm() {

    }
    public function test_updateAvailableTonersForm() {

    }
    public function test_updateAvailableOptionsForm() {

    }
    public function test_updateHardwareConfigurationsForm() {

    }
    public function test_addToners() {

    }
    public function test_removeToners() {

    }
     */
}

