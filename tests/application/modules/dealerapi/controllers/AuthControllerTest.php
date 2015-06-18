<?php

require_once APPLICATION_BASE_PATH.'/application/modules/dealerapi/controllers/AuthController.php';

/**
 * Class MPSToolbox_Settings_Form_AllSettingsTest
 * @property Dealerapi_AuthController $controller
 * @property Zend_Controller_Request_HttpTestCase $request
 * @property Zend_Controller_Response_HttpTestCase $response
 */
class Dealerapi_AuthControllerTest extends My_ControllerTestCase
{

    public $fixtures = ['dealers'];

    public function setUp()
    {
        parent::setUp();
    }

    public function testAuth() {
        $this->request->setParam('key','123');
        $this->request->setParam('secret','abc');
        $this->dispatch('dealerapi/auth/index');
        $json = $this->response->getBody();
        $this->assertJson($json);
        $arr = json_decode($json,true);
        $this->assertEquals(['ok'=>'Welcome Root Company'], $arr);
    }



}