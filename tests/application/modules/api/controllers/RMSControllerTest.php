<?php

require_once APPLICATION_BASE_PATH.'/application/modules/api/controllers/RMSController.php';

/**
 * Class MPSToolbox_Settings_Form_AllSettingsTest
 * @property Api_ComputersController $controller
 * @property Zend_Controller_Request_HttpTestCase $request
 * @property Zend_Controller_Response_HttpTestCase $response
 */
class Api_RMSControllerTest extends My_ControllerTestCase
{

    public $fixtures = ['dealers','manufacturers','users','ext_computer'];

    public function setUp() {
        parent::setUp();
        $user = \MPSToolbox\Legacy\Mappers\UserMapper::getInstance()->find(2);
        Zend_Auth::getInstance()->getStorage()->write($user);
    }

    public function testToShopify() {
        $this->dispatch('api/v1/rms/to-shopify/1');
        $this->assertNotRedirect();
        $json = $this->response->getBody();
        $this->assertJson($json);
        $arr = json_decode($json,true);
        $this->assertEquals('ok', $arr['message']);
    }


}