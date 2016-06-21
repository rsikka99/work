<?php
class HardwareLibrary_ManageDevicesControllerTest extends My_ControllerTestCase
{

    public $fixtures = [
        'base_printer_cartridge','base_printer','dealer_settings','history',
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

    public function test_manageMasterDevicesAction() {
        $this->getRequest()->setParam('masterDeviceId',1);
        $this->getRequest()->setMethod('POST');
        $this->dispatch('/hardware-library/devices/load-forms');
        $this->assertNotRedirect();
        echo $this->getResponse()->getBody();
    }

}