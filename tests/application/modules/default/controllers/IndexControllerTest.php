<?php

class Default_IndexControllerTest extends My_ControllerTestCase
{

    public $fixtures = ['users','clients','device_instances'];

    public function setUp() {
        parent::setUp();
        $user = \MPSToolbox\Legacy\Mappers\UserMapper::getInstance()->find(2);
        Zend_Auth::getInstance()->getStorage()->write($user);
        $this->session = new Zend_Session_Namespace('mps-tools');
        $this->session->selectedClientId = 5;
    }

    public function tearDown() {
        Zend_Auth::getInstance()->getStorage()->write(null);
        parent::tearDown();
    }

    public function test_indexAction()
    {
        $this->session = new Zend_Session_Namespace('mps-tools');
        $this->session->selectedClientId = null;

        $this->dispatch('/');

        $this->assertModule('default');
        $this->assertController('index');
        $this->assertAction('index');

        $this->assertRedirectTo('/select-client');
    }

    public function test_indexAction_dashboard()
    {
        $this->dispatch('/');

        $this->assertModule('default');
        $this->assertController('index');
        $this->assertAction('index');
        $this->assertNotRedirect();

        $this->assertQueryCount('div[@class="tab-content"]',1);
    }

    public function test_changeClientAction() {
        $this->dispatch('/clients/change');
        $this->assertModule('default');
        $this->assertController('index');
        $this->assertAction('change-client');
        $this->assertRedirectTo('/');
    }

    public function test_changeUploadAction() {
        $this->dispatch('/rms-uploads/change');
        $this->assertModule('default');
        $this->assertController('index');
        $this->assertAction('change-upload');
        $this->assertRedirectTo('/select-upload');
    }

    public function test_deleteUploadAction() {
        $this->dispatch('/rms-uploads/delete/2');
        $this->assertModule('default');
        $this->assertController('index');
        $this->assertAction('delete-rms-upload');
        $this->assertRedirectTo('/select-upload');
        $this->assertEquals(302, $this->getResponse()->getHttpResponseCode());
    }

    public function test_selectClientAction() {
        $this->dispatch('/select-client');
        $this->assertModule('default');
        $this->assertController('index');
        $this->assertAction('select-client');
        $this->assertNotRedirect();
    }

    public function test_selectUploadAction() {
        $this->dispatch('/select-upload');
        $this->assertModule('default');
        $this->assertController('index');
        $this->assertAction('select-upload');
        $this->assertNotRedirect();
    }

    public function test_deleteReportAction() {
        $this->dispatch('/delete-assessment/1');
        $this->assertModule('default');
        $this->assertController('index');
        $this->assertAction('delete-report');
        $this->assertRedirectTo('/');
    }

    public function test_noClientsAction() {
        Zend_Db_Table::getDefaultAdapter()->query('truncate clients');
        $this->dispatch('/first-client');
        $this->assertModule('default');
        $this->assertController('index');
        $this->assertAction('no-clients');
        $this->assertNotRedirect();
    }

    public function test_noUploadsAction() {
        Zend_Db_Table::getDefaultAdapter()->query('truncate rms_uploads');
        $this->dispatch('/first-upload');
        $this->assertModule('default');
        $this->assertController('index');
        $this->assertAction('no-uploads');
        $this->assertNotRedirect();
    }





//echo $this->getResponse()->getBody();
}

