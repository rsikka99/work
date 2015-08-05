<?php

class Proposalgen_CostController_FileMock extends Zend_Form_Element_File {
    public $filename = 'unit_test.csv';
    public function isUploaded() {
        return true;
    }
    public function receive() {
        return true;
    }
    public function isValid($value, $context = null) {
        return true;
    }
    public function getFileName($value=null, $path=true) {
        return $this->filename;
    }
}

class Proposalgen_CostControllerTest extends My_ControllerTestCase {

    public $fixtures = [
        'users', 'clients', 'master_devices', 'device_toners'
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
        $controller = new Proposalgen_CostsController($this->getRequest(), $this->getResponse());

        $this->getRequest()->setMethod('POST');
        $this->getRequest()->setParams([''=>'']);

        #$matchupService = $this->getMock('\MPSToolbox\Legacy\Modules\ProposalGenerator\Services\Import\TonerMatchupImportService');
        #$matchupService->expects($this->once())->method('validatedHeaders')->will($this->returnValue(true));
        #$matchupService->importFile = fopen(APPLICATION_BASE_PATH.'/docs/Sample Import Files/matchup/test.csv','rb');
        #$matchupService->importHeaders = fgetcsv($matchupService->importFile);
        $matchupService = new MyTonerMatchupImportService();
        $controller->setMatchupService($matchupService);

        $controller->getFlashMessenger()->clearMessages();
        $controller->bulkFileTonerMatchupAction();
        $result = $controller->getFlashMessenger()->getCurrentMessages();
        $this->assertEquals([['success' => 'Your pricing updates have been applied successfully.']], $result);
    }


}

class MyTonerMatchupImportService extends \MPSToolbox\Legacy\Modules\ProposalGenerator\Services\Import\TonerMatchupImportService {
    /** @override */
    public function getValidFile ($config)
    {
        $this->importFile    = fopen(APPLICATION_BASE_PATH.'/docs/Sample Import Files/matchup/test.csv','rb');
        $this->importHeaders = fgetcsv($this->importFile);

        return null;
    }
    /** @override */
    public function closeFiles () {
        //noop
    }

}
