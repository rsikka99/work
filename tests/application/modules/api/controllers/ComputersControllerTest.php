<?php

require_once APPLICATION_BASE_PATH.'/application/modules/api/controllers/ComputersController.php';

/**
 * Class MPSToolbox_Settings_Form_AllSettingsTest
 * @property Api_ComputersController $controller
 * @property Zend_Controller_Request_HttpTestCase $request
 * @property Zend_Controller_Response_HttpTestCase $response
 */
class Api_ComputersControllerTest extends My_ControllerTestCase
{

    public $fixtures = ['dealers','manufacturers','users','ext_computer'];

    public function setUp() {
        parent::setUp();
        $user = \MPSToolbox\Legacy\Mappers\UserMapper::getInstance()->find(2);
        Zend_Auth::getInstance()->getStorage()->write($user);
    }

    public function testIndex() {
        $this->request->setParam('q','222');
        $this->dispatch('api/v1/computers/');
        $this->assertNotRedirect();
        $json = $this->response->getBody();
        $this->assertJson($json);
        $arr = json_decode($json,true);
        #print_r($arr['result']);
        $this->assertEquals(1, $arr['total']);
        $this->assertEquals(2, $arr['result'][0]['id']);
    }

    public function testGridList() {
        $this->request->setParam('filterSearchIndex','modelName');
        $this->request->setParam('filterSearchValue','222');
        $this->dispatch('api/v1/computers/grid-list');
        $this->assertNotRedirect();
        $this->assertAction('grid-list');
        $json = $this->response->getBody();
        $this->assertJson($json);
        $arr = json_decode($json,true);
        print_r($arr);
        $this->assertEquals(1, $arr['total']);
        $this->assertEquals(2, $arr['rows'][0]['id']);
    }



}