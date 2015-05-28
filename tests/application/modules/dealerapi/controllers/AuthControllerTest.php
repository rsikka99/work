<?php

require_once APPLICATION_BASE_PATH.'/application/modules/dealerapi/controllers/AuthController.php';

/**
 * Class MPSToolbox_Settings_Form_AllSettingsTest
 * @property Dealerapi_AuthController $controller
 * @property Zend_Controller_Request_HttpTestCase $request
 * @property Zend_Controller_Response_HttpTestCase $response
 */
class Dealerapi_AuthControllerTest extends My_DatabaseTestCase
{

    public $fixtures = ['dealers'];

    public function setUp()
    {
        parent::setUp();
        $this->request = new Zend_Controller_Request_HttpTestCase();
        $this->response = new Zend_Controller_Response_HttpTestCase();
        Zend_Controller_Front::getInstance()->setRequest($this->request);
        Zend_Controller_Front::getInstance()->setResponse($this->response);
        $this->controller = new Dealerapi_AuthController($this->request,$this->response);
    }

    public function testAuth() {
        $this->request->setParam('key','123');
        $this->request->setParam('secret','abc');
        $this->controller->indexAction();
        $json = $this->response->getBody();
        $this->assertJson($json);
        $arr = json_decode($json,true);
        $this->assertEquals(['ok'=>'welcome Root Company'], $arr);
    }



}