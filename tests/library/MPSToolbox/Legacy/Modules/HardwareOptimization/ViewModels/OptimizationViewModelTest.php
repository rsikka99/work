<?php
use MPSToolbox\Legacy\Modules\HardwareOptimization\Models\HardwareOptimizationModel;
use MPSToolbox\Legacy\Modules\HardwareOptimization\ViewModels\OptimizationViewModel;
use MPSToolbox\Legacy\Models\DealerModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\RmsUploadModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\ClientModel;

/**
 * Class MPSToolbox_Settings_Form_AllSettingsTest
 *
 * @property OptimizationViewModel model
 * @property HardwareOptimizationModel hardwareOptimizationModel
 */
class MPSToolbox_Legacy_Modules_HardwareOptimization_ViewModels_OptimizationViewModelTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        My_Model_Abstract::setAuthDealerId(3);

        $this->hardwareOptimizationModel = new HardwareOptimizationModel([
            "id"           => 1,
            "clientId"     => 2,
            "dealerId"     => 3,
            "dateCreated"  => '4',
            "lastModified" => '6',
            "name"         => '7',
            "rmsUploadId"  => 8,
            "stepName"     => '9',
        ]);
        $this->hardwareOptimizationModel->setClient(new ClientModel([
            'id' => 2,
            'dealerId' => 3,
        ]));
        $this->hardwareOptimizationModel->setDealer(new DealerModel([
            'id' => 3,
        ]));
        $this->hardwareOptimizationModel->setRmsUpload(new RmsUploadModel([
            'id' => 8,
        ]));

        $this->model = new OptimizationViewModel($this->hardwareOptimizationModel);
    }

    public function tearDown()
    {
    }

    public function testGetDevices() {
        $result = $this->model->getDevices();
        $this->assertTrue($result instanceof DevicesViewModel);
    }

}