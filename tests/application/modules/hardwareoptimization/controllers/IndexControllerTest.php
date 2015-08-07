<?php

/**
*/
class Hardwareoptimization_IndexControllerTest extends My_ControllerTestCase
{

    public $fixtures = [
        'images','dealers','clients','users',
        'dealer_features',
        'master_devices',
        'rms_uploads',
        'device_instances','device_instance_meters','device_instance_master_devices',
        'device_swap_reasons',
        'device_swaps','hardware_optimizations','hardware_optimization_device_instances',
        'toner_vendor_ranking_sets','fleet_settings','generic_settings','optimization_settings','quote_settings','dealer_settings'
    ];

    public function setUp ()
    {
        parent::setUp();
        $auth   = Zend_Auth::getInstance();
        $user = json_decode(json_encode(['id'=>1, 'eulaAccepted'=>true, 'firstname'=>'unit', 'lastname'=>'testing', 'dealerId'=>1, 'resetPasswordOnNextLogin'=>false, 'email'=>'it@tangentmtw.com']));
        $auth->getStorage()->write($user);
        $mpsSession = new Zend_Session_Namespace('mps-tools');
        $mpsSession->selectedClientId = 1;
        $mpsSession->selectedRmsUploadId=1;
    }

    public function tearDown ()
    {
        parent::tearDown();
    }

    public function test_indexAction()
    {
        $this->dispatch('/hardwareoptimization');
        $this->assertRedirect();
        $this->assertRedirectTo('/hardwareoptimization/optimization');
    }

    public function test_settingsAction() {
        $this->dispatch('/hardwareoptimization/index/settings');
        $this->assertNotRedirect();
        $this->assertQueryCount('input',33);
    }
    public function test_settingsAction_post() {
        $this->getRequest()
            ->setMethod('POST')
            ->setPost([
'autoOptimizeFunctionality'=>0,
'blackToColorRatio'=>30,
'costThreshold'=>20,
'currentPageCoverageColor'=>24,
'currentPageCoverageMono'=>6,
'currentUseDevicePageCoverages'=>0,
'currentUseDevicePageCoverages'=>1,
'defaultDeviceMargin'=>15,
'defaultEnergyCost'=>0.11,
'defaultMonthlyLeasePayment'=>250,
'defaultPageMargin'=>45,
'defaultPrinterCost'=>1000,
'leasedColorCostPerPage'=>0.12,
'leasedMonochromeCostPerPage'=>0.04,
'lossThreshold'=>50,
'minimumPageCount'=>1000,
'mpsColorCostPerPage'=>0.1,
'mpsMonochromeCostPerPage'=>0.02,
'optimizedTargetColorCostPerPage'=>0.09,
'optimizedTargetMonochromeCostPerPage'=>0.012,
'proposedDefaultAdminCostPerPage'=>0.0005,
'proposedDefaultColorLaborCostPerPage'=>0.0015,
'proposedDefaultColorPartsCostPerPage'=>0.0015,
'proposedDefaultMonochromeLaborCostPerPage'=>0.0015,
'proposedDefaultMonochromePartsCostPerPage'=>0.0015,
'proposedPageCoverageColor'=>24,
'proposedPageCoverageMono'=>6,
'proposedUseDevicePageCoverages'=>0,
'saveAndContinue'=>1,
'targetColorCostPerPage'=>0.1,
'targetMonochromeCostPerPage'=>0.02,
'tonerMargin'=>28,
        ]);
        $this->dispatch('/hardwareoptimization/index/settings');
        $this->assertRedirectTo('/hardwareoptimization/optimization');
    }
    public function test_optimizeAction() {
        $this->dispatch('/hardwareoptimization/index/optimize');
        $this->assertNotRedirect();
    }
    public function test_getDeviceByDeviceInstanceIdAction() {

    }
    public function test_updateReplacementDeviceAction() {

    }
    public function test_summaryTableAction() {

    }
    public function test_deviceListAction() {

    }
    public function test_updateDeviceSwapReasonAction() {

    }
    public function test_getDeviceSwapForm() {

    }

}