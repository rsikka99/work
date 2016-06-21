<?php

require_once APPLICATION_BASE_PATH.'/application/modules/dealerapi/controllers/DeviceController.php';

/**
 * Class MPSToolbox_Settings_Form_AllSettingsTest
 * @property Dealerapi_DeviceController $controller
 * @property Zend_Controller_Request_HttpTestCase $request
 * @property Zend_Controller_Response_HttpTestCase $response
 */
class Dealerapi_DeviceControllerTest extends My_DatabaseTestCase
{

    public $fixtures = [
        'images','dealers','clients','users',
        'toner_configs','manufacturers',
        'base_printer',
        'rms_providers','rms_devices','rms_uploads','rms_upload_rows',
        'device_instances','device_instance_meters','device_instance_master_devices',
        'toner_vendor_ranking_sets','toner_vendor_rankings'
    ];

    public function setUp()
    {
        $this->request = new Zend_Controller_Request_HttpTestCase();
        $this->response = new Zend_Controller_Response_HttpTestCase();
        Zend_Controller_Front::getInstance()->setRequest($this->request);
        Zend_Controller_Front::getInstance()->setResponse($this->response);
        $this->controller = new Dealerapi_DeviceController($this->request,$this->response);
        $this->controller->dealerId = 2;
        parent::setUp();
    }

    public function testSwap() {
        $this->user2();
        $this->request->setParam('id',1);
        $this->request->setParam('ampv',1000);
        $this->controller->swapAction();
        $json = $this->response->getBody();
        $this->assertJson($json);
        $arr = json_decode($json,true);
        $this->assertEquals(['result'=>[]], $arr);
    }



}