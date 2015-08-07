<?php

class Proposalgen_CostControllerTest extends My_ControllerTestCase {

    public $fixtures = [
        'users', 'clients', 'toners', 'master_devices', 'device_toners', 'manufacturers'
    ];

    /**
     * @var Zend_Session_Namespace
     */
    public $session;

    public function setUp() {
        parent::setUp();
        $user = \MPSToolbox\Legacy\Mappers\UserMapper::getInstance()->find(2);
        Zend_Auth::getInstance()->getStorage()->write($user);
        //\MPSToolbox\Legacy\Entities\ClientEntity::find(5);
        $this->session = new Zend_Session_Namespace('mps-tools');
        $this->session->selectedClientId = 5;
    }

    public function tearDown() {
        Zend_Auth::getInstance()->getStorage()->write(null);
        parent::tearDown();
    }

    public function test_bulkFileTonerMatchupAction() {
        $this->dispatch('/hardware-library/bulk-file-toner-matchup');

        require_once APPLICATION_BASE_PATH.'/application/modules/proposalgen/controllers/CostsController.php';
        $this->getRequest()->setMethod('POST');
        $controller = new Proposalgen_CostsController($this->getRequest(), $this->getResponse());

        $matchupService = new MyTonerMatchupImportService();
        $controller->setMatchupService($matchupService);

        $controller->getFlashMessenger()->clearMessages();
        $controller->bulkFileTonerMatchupAction();
        $result = $controller->getFlashMessenger()->getCurrentMessages();
        $this->assertEquals([['success' => 'Your pricing updates have been applied successfully.']], $result);
        $this->assertEquals(array(), $controller->view->errorMessages);
    }

    public function test_bulkFileTonerPricingAction() {
        $this->dispatch('/hardware-library/bulk-file-toner-pricing');

        require_once APPLICATION_BASE_PATH.'/application/modules/proposalgen/controllers/CostsController.php';
        $this->getRequest()->setMethod('POST');
        $controller = new Proposalgen_CostsController($this->getRequest(), $this->getResponse());

        $pricingService = new MyTonerPricingImportService();
        $controller->setTonerPricingService($pricingService);

        $controller->getFlashMessenger()->clearMessages();
        $controller->bulkFileTonerPricingAction();
        $result = $controller->getFlashMessenger()->getCurrentMessages();
        $this->assertEquals([['success' => 'Your pricing updates have been applied successfully.']], $result);
        $this->assertEquals([3=>['invalid'=>['New Price'=>['notFloat'=>"'#N/A' does not appear to be a float", 'notGreaterThan'=>"'#N/A' is not greater than '0'"]]]], $controller->view->errorMessages);
    }

    public function test_bulkFileTonerPricingAction_comp_manufacturer() {
        $this->dispatch('/hardware-library/bulk-file-toner-pricing');

        require_once APPLICATION_BASE_PATH.'/application/modules/proposalgen/controllers/CostsController.php';
        $this->getRequest()->setMethod('POST');
        $this->getRequest()->setParam('manufacturers',3);
        $controller = new Proposalgen_CostsController($this->getRequest(), $this->getResponse());

        $pricingService = new MyTonerPricingImportService();
        $controller->setTonerPricingService($pricingService);

        $controller->getFlashMessenger()->clearMessages();
        $controller->bulkFileTonerPricingAction();
        $result = $controller->getFlashMessenger()->getCurrentMessages();
        $this->assertEquals([['success' => 'Your pricing updates have been applied successfully.']], $result);
        $this->assertEquals([3=>['invalid'=>['New Price'=>['notFloat'=>"'#N/A' does not appear to be a float", 'notGreaterThan'=>"'#N/A' is not greater than '0'"]]]], $controller->view->errorMessages);
    }

    public function test_bulkFileTonerPricingAction_comp_matchup() {
        $this->dispatch('/hardware-library/bulk-file-toner-pricing');

        require_once APPLICATION_BASE_PATH.'/application/modules/proposalgen/controllers/CostsController.php';
        $this->getRequest()->setMethod('POST');
        $this->getRequest()->setParam('manufacturers',30);
        $controller = new Proposalgen_CostsController($this->getRequest(), $this->getResponse());

        $pricingService = new MyTonerPricingImportService();
        $controller->setTonerPricingService($pricingService);

        $controller->getFlashMessenger()->clearMessages();
        $controller->bulkFileTonerPricingAction();
        $result = $controller->getFlashMessenger()->getCurrentMessages();
        $this->assertEquals([['success' => 'Your pricing updates have been applied successfully.']], $result);
        $this->assertEquals(
            [3=>['invalid'=>['New Price'=>['notFloat'=>"'#N/A' does not appear to be a float", 'notGreaterThan'=>"'#N/A' is not greater than '0'"]]]],
            $controller->view->errorMessages
        );
    }
}

class MyTonerMatchupImportService extends \MPSToolbox\Legacy\Modules\ProposalGenerator\Services\Import\TonerMatchupImportService {
    /** @override */
    public function getValidFile ($config)
    {
        $this->importFile    = fopen(APPLICATION_BASE_PATH.'/docs/Sample Import Files/toner/matchup.csv','rb');
        $this->importHeaders = fgetcsv($this->importFile);

        return null;
    }
    /** @override */
    public function closeFiles () {
        //noop
    }

}

class MyTonerPricingImportService extends \MPSToolbox\Legacy\Modules\ProposalGenerator\Services\Import\TonerPricingImportService {
    /** @override */
    public function getValidFile ($config)
    {
        $this->importFile    = fopen(APPLICATION_BASE_PATH.'/docs/Sample Import Files/toner/pricing.csv','rb');
        $this->importHeaders = fgetcsv($this->importFile);

        return null;
    }
    /** @override */
    public function closeFiles () {
        //noop
    }
}
