<?php
use MPSToolbox\Legacy\Modules\HardwareOptimization\Models\OptimizationAbstractModel;
use MPSToolbox\Legacy\Modules\HardwareOptimization\Models\HardwareOptimizationModel;
use MPSToolbox\Legacy\Models\DealerModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\RmsUploadModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\ClientModel;



class TestOptimizationModel extends OptimizationAbstractModel {
}

/**
 * Class MPSToolbox_Settings_Form_AllSettingsTest
 *
 * @property OptimizationAbstractModel model
 * @property HardwareOptimizationModel hardwareOptimizationModel
 */
class MPSToolbox_Legacy_Modules_HardwareOptimization_Models_OptimizationAbstractModelTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
    }

    public function tearDown()
    {
    }

    public function testContructor() {
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
        $this->hardwareOptimizationModel->setClient(new ClientModel());
        $this->hardwareOptimizationModel->setDealer(new DealerModel());
        $this->hardwareOptimizationModel->setRmsUpload(new RmsUploadModel());
        $this->model = new TestOptimizationModel($this->hardwareOptimizationModel);
        $this->assertTrue(true);
    }

}