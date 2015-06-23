<?php

class Proposalgen_FleetController_FileMock extends Zend_Form_Element_File {
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

class Proposalgen_FleetControllerTest extends My_ControllerTestCase {

    public $fixtures = [
        'users', 'clients', 'device_instances', 'master_devices', 'device_instance_master_devices','device_toners','device_instance_meters'];

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

    public function test_indexAction() {
        $this->dispatch('/rms-uploads/2');
        $this->assertNotRedirect();
        $this->assertModule('proposalgen');
        $this->assertController('fleet');
        $this->assertAction('index');
        $this->assertTrue(strpos($this->getResponse()->getBody(),'Upload Complete')>0);
    }

    public function test_indexAction_form() {
        $this->dispatch('/rms-uploads');
        $this->assertNotRedirect();
        $this->assertModule('proposalgen');
        $this->assertController('fleet');
        $this->assertAction('index');
        $this->assertFalse(strpos($this->getResponse()->getBody(),'Upload Complete')>0);
        $this->assertQueryCount('form[@method="post"]',1);
    }

    public function test_indexAction_post() {
        $this->dispatch('/rms-uploads');

        $service = new \MPSToolbox\Legacy\Modules\ProposalGenerator\Services\RmsUploadService(2,2,5);

        require_once APPLICATION_BASE_PATH.'/application/modules/proposalgen/controllers/FleetController.php';
        $controller = new Proposalgen_FleetController($this->getRequest(), $this->getResponse());
        $controller->setRmsUploadService($service);

        $form = $service->getForm();
        $elements = $form->getElements();
        $elements['uploadFile'] = new Proposalgen_FleetController_FileMock('uploadFile');
        $form->uploadFile = $elements['uploadFile'];
        $form->uploadFile->filename = APPLICATION_BASE_PATH.'/docs/Sample Import Files/FMAudit/FMAudExport-partial.csv';

        $this->getRequest()->setMethod('POST');
        $this->getRequest()->setParams(['rmsProviderId'=>'2', 'performUpload'=>true]); // fm audit

        $controller->indexAction();
        $this->assertEquals(3, $this->session->selectedRmsUploadId);
    }

    public function test_mappingAction() {
        $this->dispatch('/rms-uploads/mapping/2');
        $this->assertNotRedirect();
        $this->assertModule('proposalgen');
        $this->assertController('fleet');
        $this->assertAction('mapping');
        $this->assertQueryCount('div#deviceMappingGrid',1);
    }

    public function test_deviceMappingListAction() {
        $this->getRequest()->setParams(['rmsUploadId'=>2]);
        $this->dispatch('/rms-uploads/mapping/list');
        $this->assertNotRedirect();
        $this->assertModule('proposalgen');
        $this->assertController('fleet');
        $this->assertAction('device-mapping-list');
        $json = json_decode($this->_response->getBody());
        $this->assertEquals(1, $json->page);
        $this->assertEquals(3, $json->total);
        $this->assertEquals(23, $json->records);
        $this->assertEquals(10, count($json->rows));
    }

    public function test_setMappedToAction() {
        $this->getRequest()->setParams([
            'deviceInstanceId'=>1,
            'masterDeviceId'=>1,
        ]);
        $this->dispatch('/rms-uploads/mapping/set-mapped-to');
        $this->assertNotRedirect();
        $this->assertModule('proposalgen');
        $this->assertController('fleet');
        $this->assertAction('set-mapped-to');
        $this->assertEquals(200, $this->getResponse()->getHttpResponseCode());
    }

    public function test_summaryAction() {
        $this->dispatch('/rms-uploads/summary/2');
        $this->assertNotRedirect();
        $this->assertModule('proposalgen');
        $this->assertController('fleet');
        $this->assertAction('summary');
        #echo $this->_response->getBody();
        $this->assertQueryCount('tbody#deviceDetails_toners',1);
    }


    public function test_deviceSummaryListAction() {
        $this->getRequest()->setParams(['rmsUploadId'=>2]);
        $this->dispatch('/rms-uploads/summary/device-list');
        $this->assertNotRedirect();
        $this->assertModule('proposalgen');
        $this->assertController('fleet');
        $this->assertAction('device-summary-list');
        #echo $this->_response->getBody();
        $json = json_decode($this->_response->getBody());
        $this->assertEquals(1,$json->page);
        $this->assertEquals(17,$json->recordsTotal);
        $this->assertEquals(10,count($json->rows));
    }

    public function test_excludedListAction() {
        $this->getRequest()->setParams(['rmsUploadId'=>2]);
        $this->dispatch('/rms-uploads/excluded-list');
        $this->assertNotRedirect();
        $this->assertModule('proposalgen');
        $this->assertController('fleet');
        $this->assertAction('excluded-list');
        #echo $this->_response->getBody();
        $json = json_decode($this->_response->getBody());
        $this->assertEquals(0,$json->page);
        $this->assertEquals(0,$json->recordsTotal);
        $this->assertEquals(0,count($json->rows));
    }

    public function test_toggleExcludedFlagAction() {
        $this->getRequest()->setParams(['rmsUploadId'=>2, 'deviceInstanceId'=>1]);
        $this->dispatch('/proposalgen/fleet/toggle-excluded-flag');
        $this->assertNotRedirect();
        $this->assertModule('proposalgen');
        $this->assertController('fleet');
        $this->assertAction('toggle-excluded-flag');
        #echo $this->_response->getBody();
        $this->assertEquals(200, $this->getResponse()->getHttpResponseCode());
    }

    public function test_toggleLeasedFlagAction() {
        $this->getRequest()->setParams(['rmsUploadId'=>2, 'deviceInstanceId'=>2]);
        $this->dispatch('/proposalgen/fleet/toggle-leased-flag');
        $this->assertNotRedirect();
        $this->assertModule('proposalgen');
        $this->assertController('fleet');
        $this->assertAction('toggle-leased-flag');
        #echo $this->_response->getBody();
        $this->assertEquals(200, $this->getResponse()->getHttpResponseCode());
    }

    public function test_toggleManagedFlagAction() {
        $this->getRequest()->setParams(['rmsUploadId'=>2, 'deviceInstanceId'=>1]);
        $this->dispatch('/proposalgen/fleet/toggle-managed-flag');
        $this->assertNotRedirect();
        $this->assertModule('proposalgen');
        $this->assertController('fleet');
        $this->assertAction('toggle-managed-flag');
        #echo $this->_response->getBody();
        $this->assertEquals(200, $this->getResponse()->getHttpResponseCode());
    }

    public function test_toggleJitFlagAction() {
        $this->getRequest()->setParams(['rmsUploadId'=>2, 'deviceInstanceId'=>1]);
        $this->dispatch('/proposalgen/fleet/toggle-jit-flag');
        $this->assertNotRedirect();
        $this->assertModule('proposalgen');
        $this->assertController('fleet');
        $this->assertAction('toggle-jit-flag');
#        echo $this->_response->getBody();
        $this->assertEquals(200, $this->getResponse()->getHttpResponseCode());
    }

    public function test_deviceInstanceDetailsAction() {
        $this->getRequest()->setParams(['rmsUploadId'=>2, 'deviceInstanceId'=>2]);
        $this->dispatch('/proposalgen/fleet/device-instance-details');
        $this->assertNotRedirect();
        $this->assertModule('proposalgen');
        $this->assertController('fleet');
        $this->assertAction('device-instance-details');
        #echo $this->_response->getBody();
        $this->assertEquals(200, $this->getResponse()->getHttpResponseCode());
        $json = json_decode($this->_response->getBody());
        $this->assertEquals(2, $json->id);
    }

}
